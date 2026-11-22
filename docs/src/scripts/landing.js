/* TypeLang landing — stages, trees, autoplay, scrollytelling. */
(function () {
  'use strict';

  const TL = window.TypeLang;
  const $ = (sel, root) => (root || document).querySelector(sel);
  const $$ = (sel, root) => Array.from((root || document).querySelectorAll(sel));
  const esc = s => s.replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const KEYWORDS = new Set(['is', 'not', 'of', 'as', 'super', 'covariant', 'contravariant', 'invariant']);

  // ───────────────────────── Tooltip ─────────────────────────
  const tip = $('#tip');
  function showTip(html, x, y) {
    tip.innerHTML = html;
    tip.hidden = false;
    moveTip(x, y);
  }
  function moveTip(x, y) {
    const r = tip.getBoundingClientRect();
    const left = Math.min(x + 14, window.innerWidth - r.width - 8);
    const top = y + 18 + r.height > window.innerHeight ? y - r.height - 10 : y + 18;
    tip.style.left = Math.max(8, left) + 'px';
    tip.style.top = top + 'px';
  }
  function hideTip() { tip.hidden = true; }

  // ───────────────────────── Stage ─────────────────────────
  const stages = [];

  class Stage {
    constructor(root, opts) {
      this.root = root;
      this.opts = Object.assign({ editable: false, showErrors: true, onRender: null }, opts || {});
      this.line = $('.line', root);
      this.mirror = $('.mirror', root);
      this.input = $('.src', root);
      this.hl = $('.hl', root);
      this.tree = $('.tree', root);
      this.msg = $('.msg', root);
      this.meta = { tokens: $('.tokens', root), nodes: $('.nodes', root), depth: $('.depth', root) };
      this.text = '';
      this.features = null;
      this.errTimer = null;
      this.lastGood = null;
      this.measure();
      stages.push(this);

      this.tree.addEventListener('mouseover', e => {
        const br = e.target.closest('.br');
        if (!br) return;
        this.highlight(+br.dataset.s, +br.dataset.l);
        tip.owner = this;
        showTip(`${esc(br.dataset.label)}<small>${esc(br.dataset.cls)} · ${br.dataset.s}–${+br.dataset.s + +br.dataset.l}</small>`, e.clientX, e.clientY);
      });
      this.tree.addEventListener('mousemove', e => { if (!tip.hidden) moveTip(e.clientX, e.clientY); });
      this.tree.addEventListener('mouseout', e => {
        if (e.target.closest('.br') && !(e.relatedTarget && e.relatedTarget.closest && e.relatedTarget.closest('.br') === e.target.closest('.br'))) {
          this.highlight(null); hideTip();
        }
      });

      if (this.input) {
        this.input.addEventListener('input', () => {
          if (this.opts.onInput) this.opts.onInput(this.input.value);
          this.set(this.input.value, { showErrors: true });
        });
      }
    }

    measure() {
      const probe = document.createElement('span');
      probe.textContent = '0'.repeat(50);
      probe.style.cssText = 'position:absolute;visibility:hidden;white-space:pre;font:inherit;left:0;top:0';
      this.line.appendChild(probe);
      const w = probe.getBoundingClientRect().width / 50;
      probe.remove();
      if (w > 0) { this.root.style.setProperty('--cw', w + 'px'); this.cw = w; }
      else this.cw = this.cw || 9;
      this.rowHeight = parseFloat(getComputedStyle(this.root).getPropertyValue('--rh')) || 24;
    }

    highlight(s, l) {
      if (s === null || s === undefined) { this.hl.hidden = true; return; }
      this.hl.style.setProperty('--s', s);
      this.hl.style.setProperty('--l', l);
      this.hl.hidden = false;
    }

    /* Replace the source. `fresh` = [start, end) region to animate as newly inserted. */
    set(text, o) {
      o = o || {};
      const prev = this.text;
      this.text = text;
      if (this.input && this.input.value !== text) this.input.value = text;

      let fresh = null;
      if (o.diff && prev !== text) {
        let a = 0;
        while (a < prev.length && a < text.length && prev[a] === text[a]) a++;
        let b = 0;
        while (b < prev.length - a && b < text.length - a && prev[prev.length - 1 - b] === text[text.length - 1 - b]) b++;
        fresh = [a, text.length - b];
      }

      let tokens = [], lexError = null;
      try { tokens = TL.tokenize(text); } catch (e) { lexError = e; tokens = safeTokens(text, e.offset); }

      let tree = null, error = lexError;
      if (!lexError) {
        try { tree = TL.parse(text, this.features || undefined); } catch (e) { error = e; if (!(e instanceof TL.ParseError)) console.error(e); }
      }

      this.renderMirror(text, tokens, error && (o.showErrors !== false && this.opts.showErrors) ? error : null, fresh);
      clearTimeout(this.errTimer);

      if (tree) {
        this.lastGood = tree;
        this.tree.classList.remove('dim', 'ghost');
        this.renderTree(tree);
        this.setMessage(null, tree);
        this.setMeta(tokens.length - 1, TL.count(tree));
      } else {
        // Unfinished input: draw the tree of the longest prefix that parses once its brackets are closed.
        const ghost = ghostParse(text, tokens.filter(t => t.type !== 'T_UNKNOWN'), this.features);
        if (ghost) { this.renderTree(ghost); this.tree.classList.add('ghost'); this.tree.classList.remove('dim'); }
        else this.tree.classList.add('dim');
        this.setMeta(tokens.length - 1, null);
        if (error && o.showErrors !== false && this.opts.showErrors) {
          const apply = () => {
            this.setMessage(error);
            this.renderMirror(text, tokens, error, null);
          };
          // While typing, let the message wait a moment so half-typed input doesn't flash red.
          if (o.immediate || !this.opts.editable) apply(); else this.errTimer = setTimeout(apply, 320);
        } else {
          this.setMessage(null, null);
        }
      }
      if (this.opts.onRender) this.opts.onRender(tree, error, text);
    }

    renderMirror(text, tokens, error, fresh) {
      let html = '', pos = 0;
      const errS = error ? error.offset : -1, errE = error ? error.offset + error.length : -1;
      for (const t of tokens) {
        if (t.type === 'T_EOI') break;
        if (t.start > pos) html += esc(text.slice(pos, t.start));
        let cls = 'tok';
        if (/^T_(FLOAT|INT_|SQ_STRING|DQ_STRING)/.test(t.type)) cls += ' t-lit';
        else if (t.type === 'T_IDENTIFIER') {
          const v = t.value.toLowerCase();
          if (v === 'true' || v === 'false' || v === 'null') cls += ' t-lit';
          else if (KEYWORDS.has(t.value)) cls += ' t-kw';
        } else if (t.type === 'T_IS_NOT') cls += ' t-kw';
        else if (t.type === 'T_IDENTIFIER_MASK' || t.type === 'T_VIRTUAL_NAME') cls += '';
        else if (t.type === 'T_VARIABLE' || t.type === 'T_THIS') cls += ' t-var';
        else if (t.type === 'T_UNKNOWN') cls += error ? ' t-err' : ' t-punct';
        else cls += ' t-punct';
        if (error && t.start < errE && t.end > errS) cls += ' t-err';
        if (fresh && t.start >= fresh[0] && t.end <= fresh[1]) cls += ' fresh';
        html += `<span class="${cls}">${esc(t.value)}</span>`;
        pos = t.end;
      }
      if (pos < text.length) html += esc(text.slice(pos));
      if (error && error.offset >= text.length) html += '<span class="t-eoi"> </span>';
      this.mirror.innerHTML = html;
    }

    renderTree(root) {
      const { items, rows } = TL.layout(root);
      const maxRow = rows - 1;

      const existing = new Map();
      for (const el of this.tree.children) existing.set(el.dataset.key, el);
      const entered = [];
      for (const it of items) {
        let el = existing.get(it.key);
        if (el) existing.delete(it.key);
        else {
          el = document.createElement('div');
          el.className = 'br enter';
          el.innerHTML = '<i></i><b></b>';
          el.dataset.key = it.key;
          this.tree.appendChild(el);
          entered.push(el);
        }
        el.dataset.s = it.s; el.dataset.l = it.l; el.dataset.label = it.label; el.dataset.cls = it.cls;
        el.style.setProperty('--s', it.s);
        el.style.setProperty('--l', it.l);
        el.style.setProperty('--r', it.r);
        el.classList.toggle('leaf', it.leaf);
        // A label only shows when it fits inside its bracket (10px mono ≈ 6.9px per glyph); the tooltip has it anyway.
        el.classList.toggle('tight', it.l * this.cw < it.label.length * 6.9 + 6);
        const b = el.lastElementChild;
        if (b.textContent !== it.label) b.textContent = it.label;
      }
      for (const el of existing.values()) el.remove();
      // The bracket under the pointer is gone with the old tree: its tooltip goes too.
      if (tip.owner === this && !tip.hidden && !this.tree.querySelector('.br:hover')) {
        this.highlight(null); hideTip();
      }
      if (entered.length) {
        this.tree.getBoundingClientRect(); // flush so the opacity transition runs
        requestAnimationFrame(() => entered.forEach(el => el.classList.remove('enter')));
      }
      this.tree.style.height = ((maxRow + 1) * this.rowHeight + 4) + 'px';
      this.depth = maxRow + 1;
      if (this.meta.depth) this.meta.depth.textContent = `depth ${this.depth}`;
    }

    setMessage(error, tree) {
      if (!this.msg) return;
      if (!error) {
        this.msg.classList.remove('err');
        this.msg.innerHTML = tree ? `<span class="cls">${esc(TL.NODE_CLASSES[tree.kind] || tree.kind)}</span>` : '';
        return;
      }
      let m = error.message;
      const suffix = ` in ${JSON.stringify(error.source === undefined ? this.text : error.source)}`;
      if (m.endsWith(suffix)) m = m.slice(0, -suffix.length);
      this.msg.classList.add('err');
      this.msg.innerHTML = `<span class="cls">${esc(error.name)}</span> ${esc(m)}`;
    }

    setMeta(tokens, nodes) {
      if (this.meta.tokens) this.meta.tokens.textContent = `${tokens} token${tokens === 1 ? '' : 's'}`;
      if (this.meta.nodes) this.meta.nodes.textContent = nodes === null ? '— nodes' : `${nodes} node${nodes === 1 ? '' : 's'}`;
      if (this.meta.depth && nodes === null) this.meta.depth.textContent = `depth —`;
    }
  }

  /* Close whatever brackets are open after the first `k` tokens and try to parse; longest prefix wins. */
  const CLOSERS = { T_ANGLE_OPEN: '>', T_BRACE_OPEN: '}', T_PAREN_OPEN: ')', T_BRACKET_OPEN: ']' };
  const OPENERS = { '>': 'T_ANGLE_OPEN', '}': 'T_BRACE_OPEN', ')': 'T_PAREN_OPEN', ']': 'T_BRACKET_OPEN' };
  function ghostParse(text, tokens, features) {
    const toks = tokens.filter(t => t.type !== 'T_EOI');
    if (!toks.length) return null;
    const stacks = [[]];
    for (const t of toks) {
      const s = stacks[stacks.length - 1].slice();
      if (CLOSERS[t.type]) s.push(CLOSERS[t.type]);
      else if (OPENERS[t.value] && s[s.length - 1] === t.value) s.pop();
      stacks.push(s);
    }
    for (let k = toks.length; k >= 1; k--) {
      const src = text.slice(0, toks[k - 1].end) + stacks[k].slice().reverse().join('');
      try { return TL.parse(src, features || undefined); } catch (e) { if (!(e instanceof TL.ParseError)) throw e; }
    }
    return null;
  }

  /* Tokenize as far as the lexer gets, then mark the rest as one unknown token. */
  function safeTokens(text, at) {
    const tokens = [];
    try { TL.tokenize(text.slice(0, at)).forEach(t => { if (t.type !== 'T_EOI') tokens.push(t); }); } catch (e) { /* ignore */ }
    const m = /\S+/y; m.lastIndex = at;
    const bad = (m.exec(text) || [text.slice(at)])[0];
    tokens.push({ type: 'T_UNKNOWN', value: bad, start: at, end: at + bad.length });
    tokens.push({ type: 'T_EOI', value: '', start: text.length, end: text.length });
    return tokens;
  }

  function makeStageDom(editable) {
    const el = document.createElement('div');
    el.className = 'stage';
    el.innerHTML = `<div class="stage-scroll"><div class="line"><pre class="mirror"></pre>${editable ? '<input class="src" type="text" spellcheck="false" autocomplete="off">' : ''}<div class="hl" hidden></div></div><div class="tree"></div></div>`;
    return el;
  }

  // ───────────────────────── Hero: autoplay ─────────────────────────
  const EXAMPLES = [
    'array<string, list<User|null>>',
    'array{id: int<1, max>, name?: non-empty-string, ...}',
    'callable(int $x, string ...$rest): void',
    'Closure<T of Some, U super Any, V = int>(T, U): V',
    'T is not U ? A : B',
    'Foo::PREFIX_*|*_SUFFIX|JSON_*',
    "Shape['key']|Foo::CONST[0]",
    'iterable<array-key, ?T>[]',
  ];

  const heroPrinted = $('#hero-printed');
  const hero = new Stage($('#hero-stage'), {
    editable: true,
    onRender(tree) { heroPrinted.textContent = tree ? TL.print(tree) : heroPrinted.textContent; },
    onInput() { autoplay.stop(); chips.forEach(c => c.classList.remove('on')); },
  });

  const chipsWrap = $('#hero-chips');
  const chips = EXAMPLES.map((ex, i) => {
    const b = document.createElement('button');
    b.type = 'button'; b.className = 'chip'; b.textContent = ex; b.dataset.i = i;
    b.addEventListener('click', () => {
      autoplay.stop();
      chips.forEach(c => c.classList.toggle('on', c === b));
      hero.set(ex, { immediate: true });
      hero.input.focus({ preventScroll: true });
    });
    chipsWrap.appendChild(b);
    return b;
  });

  // The bar under the hero stage counts down the time an example is shown before the next one. It is drawn from the
  // deadline of the autoplay timer, so it stops and goes on together with it.
  const countdownBar = $('#hero-stage .countdown i');
  const HOLD = 2800;
  let countdownFrame = 0;
  function drawCountdown() {
    countdownFrame = 0;
    if (autoplay.phase !== 'hold' || !autoplay.running) { countdownBar.style.transform = 'scaleX(0)'; return; }
    const left = autoplay.paused ? autoplay.left : Math.max(0, autoplay.deadline - performance.now());
    countdownBar.style.transform = `scaleX(${1 - left / HOLD})`;
    if (!autoplay.paused) countdownFrame = requestAnimationFrame(drawCountdown);
  }
  function startCountdown() { if (!countdownFrame) countdownFrame = requestAnimationFrame(drawCountdown); }

  const autoplay = {
    timer: null, idx: 0, pos: 0, phase: 'type', running: false, paused: false, deadline: 0, left: 0,
    start() {
      if (this.running) return;
      this.running = true;
      this.tick();
    },
    stop() {
      this.running = false; this.paused = false; clearTimeout(this.timer);
      startCountdown();
    },
    // Hovering the stage freezes the example under the pointer, with the time it had left.
    pause() {
      if (!this.running || this.paused) return;
      this.paused = true; clearTimeout(this.timer);
      this.left = Math.max(0, this.deadline - performance.now());
    },
    resume() {
      if (!this.running || !this.paused) return;
      this.paused = false;
      this.schedule(this.left);
      startCountdown();
    },
    schedule(delay) {
      this.deadline = performance.now() + delay;
      this.timer = setTimeout(() => this.tick(), delay);
    },
    tick() {
      if (!this.running || this.paused) return;
      const ex = EXAMPLES[this.idx];
      let delay = 55;
      if (this.phase === 'type') {
        this.pos++;
        hero.set(ex.slice(0, this.pos), { showErrors: false });
        chips.forEach((c, i) => c.classList.toggle('on', i === this.idx));
        const ch = ex[this.pos - 1];
        delay = /[,:|&?]/.test(ch) ? 140 : /[<>{}()\[\]]/.test(ch) ? 110 : 45 + Math.random() * 40;
        if (this.pos >= ex.length) {
          this.phase = 'hold'; delay = HOLD;
          startCountdown();
        }
      } else if (this.phase === 'hold') {
        this.phase = 'delete'; delay = 30;
      } else {
        this.pos = Math.max(0, this.pos - (this.pos > 12 ? 3 : 1));
        hero.set(ex.slice(0, this.pos), { showErrors: false });
        delay = 24;
        if (this.pos === 0) { this.phase = 'type'; this.idx = (this.idx + 1) % EXAMPLES.length; delay = 500; }
      }
      this.schedule(reduceMotion ? Math.max(delay, 400) : delay);
    },
  };

  hero.root.addEventListener('mouseenter', () => autoplay.pause());
  hero.root.addEventListener('mouseleave', () => autoplay.resume());
  hero.input.addEventListener('focus', () => autoplay.stop());
  if (reduceMotion) { hero.set(EXAMPLES[0], { immediate: true }); chips[0].classList.add('on'); }
  else autoplay.start();

  // ───────────────────────── § 01 Grown ─────────────────────────
  const grown = new Stage($('#grown-stage'), { editable: false });
  const steps = $$('#steps .step');
  let activeStep = null;
  function activate(step) {
    if (step === activeStep) return;
    activeStep = step;
    steps.forEach(s => s.classList.toggle('on', s === step));
    grown.set(step.dataset.type, { diff: true, immediate: true });
  }
  activate(steps[0]);
  // The current step is the last one whose top has passed the middle of the window: one line, one answer, so the
  // stage never flips between two neighbours at the boundary.
  let stepFrame = 0;
  // Going back needs a margin: the tree of the new step changes the height of the stage and moves the steps a little.
  const STEP_BACK_MARGIN = 48;
  function pickStep() {
    stepFrame = 0;
    const line = window.innerHeight / 2;
    let current = steps[0];
    for (const step of steps) {
      if (step.getBoundingClientRect().top <= line) current = step;
    }
    const isBack = steps.indexOf(current) < steps.indexOf(activeStep);
    if (isBack && activeStep.getBoundingClientRect().top <= line + STEP_BACK_MARGIN) return;
    activate(current);
  }
  window.addEventListener('scroll', () => { if (!stepFrame) stepFrame = requestAnimationFrame(pickStep); }, { passive: true });
  pickStep();

  // ───────────────────────── § 02 Vocabulary ─────────────────────────
  const cardsWrap = $('#cards');
  const cards = $$('#cards .card').map(card => {
    const h3 = $('h3', card);
    const a = document.createElement('a');
    a.href = card.dataset.href; a.textContent = 'docs →';
    h3.appendChild(a);
    const dom = makeStageDom(false);
    card.appendChild(dom);
    const st = new Stage(dom, { editable: false });
    st.set(card.dataset.type, { immediate: true });
    return { card, h3, stage: st };
  });

  // Every card spans as many grid columns as its type and title need. Cards are packed into rows first fit, and
  // the columns a row has left are shared among its cards: a card may grow wider than its content, never narrower.
  function layoutCards() {
    const width = cardsWrap.clientWidth;
    const cols = width >= 900 ? 12 : width >= 540 ? 6 : 1;
    cardsWrap.style.setProperty('--cols', cols);
    if (cols === 1) {
      cards.forEach(({ card }) => { card.style.setProperty('--span', 1); card.style.order = ''; });
      return;
    }
    const colWidth = width / cols;
    const minSpan = cols / 4;
    const items = cards.map(it => {
      // The title is 13px mono with a little tracking: about 8.6px a glyph, plus a gap before the link.
      const need = Math.max(it.card.dataset.type.length * it.stage.cw, (it.h3.textContent.length + 3) * 8.6) + 48;
      return { ...it, span: Math.min(cols, Math.max(minSpan, Math.ceil(need / colWidth))) };
    });
    const rows = [];
    for (const it of items) {
      let row = rows.find(r => r.used + it.span <= cols);
      if (!row) { row = { used: 0, items: [] }; rows.push(row); }
      row.items.push(it); row.used += it.span;
    }
    let order = 0;
    for (const row of rows) {
      for (let left = cols - row.used, i = 0; left > 0; left--, i = (i + 1) % row.items.length) row.items[i].span++;
      for (const it of row.items) { it.card.style.setProperty('--span', it.span); it.card.style.order = order++; }
    }
  }
  layoutCards();

  // ───────────────────────── § 03 Features ─────────────────────────
  // Mirrors the flags of TypeParserFeatures, in the order of its constructor
  const FLAGS = [
    ['unions', 'T|U'], ['intersections', 'T&U'], ['nullables', '?T'], ['lists', 'T[]'], ['offsets', 'T[K]'],
    ['conditions', 'T is U ? A : B'], ['generics', 'T<U>'], ['wildcards', 'T<*>'], ['hints', 'T<covariant U>'],
    ['callables', 'fn(T): U'], ['templates', 'fn<T>(T): T'], ['defaults', 'fn(T $a = U)'], ['shapes', 'T{k: U}'],
    ['constants', 'T::U'], ['masks', 'T::U_*'], ['literals', '42, "str", null'],
  ];
  const switches = $('#switches');
  const featuresPhp = $('#features-php');
  const featureState = {};
  const featStage = new Stage($('#features-stage'), { editable: true, onRender: renderPhp });

  for (const [flag, ex] of FLAGS) {
    featureState[flag] = true;
    const label = document.createElement('label');
    label.className = 'switch';
    label.innerHTML = `<input type="checkbox" checked data-flag="${flag}"><span class="knob"></span><span class="name">${flag}</span><span class="ex">${esc(ex)}</span>`;
    switches.appendChild(label);
    $('input', label).addEventListener('change', e => {
      featureState[flag] = e.target.checked;
      featStage.features = Object.assign({}, featureState);
      featStage.set(featStage.input.value, { immediate: true });
    });
  }
  const all = document.createElement('div');
  all.className = 'switch-all';
  all.innerHTML = '<button type="button" data-all="1">enable all</button><button type="button" data-all="0">names only</button>';
  switches.appendChild(all);
  all.addEventListener('click', e => {
    const b = e.target.closest('button'); if (!b) return;
    const on = b.dataset.all === '1';
    $$('input[data-flag]', switches).forEach(i => { i.checked = on; featureState[i.dataset.flag] = on; });
    featStage.features = Object.assign({}, featureState);
    featStage.set(featStage.input.value, { immediate: true });
  });

  function renderPhp(tree, error, text) {
    const off = FLAGS.filter(([f]) => !featureState[f]).map(([f]) => f);
    let s = '';
    if (off.length) {
      s += `<span class="v">$parser</span> = <span class="k">new</span> TypeParser(<span class="k">new</span> TypeParserFeatures(\n`;
      s += off.map(f => `    <span class="off">${f}: false</span>,`).join('\n') + '\n));\n\n';
    } else {
      s += `<span class="v">$parser</span> = <span class="k">new</span> TypeParser(); <span class="c">// every feature flag defaults to true</span>\n\n`;
    }
    s += `<span class="v">$parser</span>-&gt;parse(<span class="s">${esc(JSON.stringify(text).replace(/^"|"$/g, "'"))}</span>);\n`;
    if (error) {
      let m = error.message;
      s += `<span class="c">// ${esc(error.name)}:\n// ${esc(m)}</span>`;
    } else if (tree) {
      s += `<span class="c">// ${esc(TL.NODE_CLASSES[tree.kind])}</span>`;
    }
    featuresPhp.innerHTML = s;
  }
  featStage.set(featStage.input.value, { immediate: true });

  // ───────────────────────── § 04 Coverage ─────────────────────────
  const rulers = $$('#rulers .ruler');
  rulers.forEach(r => r.style.setProperty('--v', r.dataset.value));
  const rulerObserver = new IntersectionObserver(entries => {
    entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in'); rulerObserver.unobserve(en.target); } });
  }, { threshold: 0.4 });
  rulers.forEach(r => rulerObserver.observe(r));

  // ───────────────────────── Re-measure on font load / resize ─────────────────────────
  function remeasure() {
    stages.forEach(s => { s.measure(); if (s.text) s.set(s.text, { immediate: true, showErrors: s.opts.editable && !autoplay.running }); });
    layoutCards();
  }
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(remeasure);
  window.TypeLangLanding = { stages, autoplay, remeasure };
  let rt;
  window.addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(remeasure, 150); });
})();
