/* TypeLang landing — stages, trees, autoplay, scrollytelling. */
(function () {
  'use strict';

  const TL = window.TypeLang;
  const $ = (sel, root) => (root || document).querySelector(sel);
  const $$ = (sel, root) => Array.from((root || document).querySelectorAll(sel));
  const esc = s => s.replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const KEYWORDS = new Set(['is', 'not', 'of', 'as', 'super']);
  const LABELS = {
    named: 'named', union: 'union', intersection: 'inter', nullable: 'nullable', list: 'list', offset: 'offset',
    int: 'literal', float: 'literal', string: 'literal', bool: 'literal', null: 'literal',
    const: 'const', classMask: 'mask', mask: 'mask', callable: 'callable', param: 'param', tparam: 'template',
    arg: 'arg', wildcard: 'wildcard', field: 'field', implicit: 'field', unsealed: 'unsealed',
    cond: 'conditional', this: '$this', var: 'variable',
  };

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
        if (/LITERAL|STRING/.test(t.type)) cls += ' t-lit';
        else if (t.type === 'T_NAME') {
          const v = t.value.toLowerCase();
          if (v === 'true' || v === 'false' || v === 'null') cls += ' t-lit';
          else if (KEYWORDS.has(t.value)) cls += ' t-kw';
        } else if (t.type === 'T_VARIABLE') cls += ' t-var';
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
      const items = [];
      let maxRow = 0;
      // Collapse wrappers that span exactly their only child (arg without hint, implicit field, bare param),
      // then measure heights on the collapsed tree so no row is left empty.
      const collapse = (node, path) => {
        let n = node;
        while (n.children.length === 1 && n.children[0].start === n.start && n.children[0].end === n.end) n = n.children[0];
        const kids = n.children.map((c, i) => collapse(c, `${path}.${i}`));
        return { n, path, kids, h: kids.length ? 1 + Math.max(...kids.map(k => k.h)) : 0 };
      };
      const visit = ({ n, path, kids, h }) => {
        maxRow = Math.max(maxRow, h);
        items.push({
          key: `${path}:${n.kind}`, kind: n.kind, s: n.start, l: Math.max(1, n.end - n.start), r: h,
          label: n.label || LABELS[n.kind] || n.kind, leaf: kids.length === 0,
          cls: TL.NODE_CLASSES[n.kind] || '',
        });
        kids.forEach(visit);
      };
      visit(collapse(root, '0'));

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
        el.style.setProperty('--r', maxRow - it.r);
        el.classList.toggle('leaf', it.leaf);
        // A label only shows when it fits inside its bracket (10px mono ≈ 6.9px per glyph); the tooltip has it anyway.
        el.classList.toggle('tight', it.l * this.cw < it.label.length * 6.9 + 6);
        const b = el.lastElementChild;
        if (b.textContent !== it.label) b.textContent = it.label;
      }
      for (const el of existing.values()) el.remove();
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
  const CLOSERS = { T_ANGLE_BRACKET_OPEN: '>', T_BRACE_OPEN: '}', T_PARENTHESIS_OPEN: ')', T_SQUARE_BRACKET_OPEN: ']' };
  const OPENERS = { '>': 'T_ANGLE_BRACKET_OPEN', '}': 'T_BRACE_OPEN', ')': 'T_PARENTHESIS_OPEN', ']': 'T_SQUARE_BRACKET_OPEN' };
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

  const autoplay = {
    timer: null, idx: 0, pos: 0, phase: 'type', running: false,
    start() {
      if (this.running) return;
      this.running = true;
      this.tick();
    },
    stop() { this.running = false; clearTimeout(this.timer); },
    tick() {
      if (!this.running) return;
      const ex = EXAMPLES[this.idx];
      let delay = 55;
      if (this.phase === 'type') {
        this.pos++;
        hero.set(ex.slice(0, this.pos), { showErrors: false });
        chips.forEach((c, i) => c.classList.toggle('on', i === this.idx));
        const ch = ex[this.pos - 1];
        delay = /[,:|&?]/.test(ch) ? 140 : /[<>{}()\[\]]/.test(ch) ? 110 : 45 + Math.random() * 40;
        if (this.pos >= ex.length) { this.phase = 'hold'; delay = 2800; }
      } else if (this.phase === 'hold') {
        this.phase = 'delete'; delay = 30;
      } else {
        this.pos = Math.max(0, this.pos - (this.pos > 12 ? 3 : 1));
        hero.set(ex.slice(0, this.pos), { showErrors: false });
        delay = 24;
        if (this.pos === 0) { this.phase = 'type'; this.idx = (this.idx + 1) % EXAMPLES.length; delay = 500; }
      }
      this.timer = setTimeout(() => this.tick(), reduceMotion ? Math.max(delay, 400) : delay);
    },
  };

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
  const stepObserver = new IntersectionObserver(entries => {
    // pick the entry closest to the middle of the viewport among intersecting ones
    const mid = window.innerHeight / 2;
    let best = null, bestD = Infinity;
    for (const en of entries) {
      if (!en.isIntersecting) continue;
      const r = en.boundingClientRect;
      const d = Math.abs((r.top + r.bottom) / 2 - mid);
      if (d < bestD) { bestD = d; best = en.target; }
    }
    if (best) activate(best);
  }, { rootMargin: '-35% 0px -35% 0px', threshold: [0, 0.25, 0.5, 0.75, 1] });
  steps.forEach(s => stepObserver.observe(s));

  // ───────────────────────── § 02 Vocabulary ─────────────────────────
  for (const card of $$('#cards .card')) {
    const h3 = $('h3', card);
    const a = document.createElement('a');
    a.href = `https://typelang.dev/${card.dataset.href}`; a.target = '_blank'; a.rel = 'noopener'; a.textContent = 'docs ↗';
    h3.appendChild(a);
    const dom = makeStageDom(false);
    card.appendChild(dom);
    const st = new Stage(dom, { editable: false });
    st.set(card.dataset.type, { immediate: true });
  }

  // ───────────────────────── § 03 Features ─────────────────────────
  const FLAGS = [
    ['literals', '42, "str", null'], ['generics', 'T<U>'], ['hints', 'T<out U>'], ['lists', 'T[]'], ['offsets', 'T[K]'],
    ['callables', 'fn(): T'], ['shapes', 'T{k: U}'], ['unions', 'T|U'], ['intersections', 'T&U'], ['conditions', 'T is U ? A : B'],
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
  }
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(remeasure);
  window.TypeLangLanding = { stages, autoplay, remeasure };
  let rt;
  window.addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(remeasure, 150); });
})();
