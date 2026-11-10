/*
 * TypeLang - a browser port of the reference parser (type-lang/parser).
 *
 * The grammar follows libs/parser/resources/grammar*.pp3. Every AST node
 * carries `start`/`end` offsets so the landing can draw the tree right under
 * the source text. Like the PHP parser, a disabled feature is reported only
 * after the whole source has been read successfully.
 *
 * Exposes `window.TypeLang = { tokenize, parse, print, walk, count, Features, NODE_CLASSES, ParseError }`.
 */
(function (global) {
  'use strict';

  // ───────────────────────────── Lexer ─────────────────────────────

  const NS = 'A-Za-z_\\u0080-\\uffff';
  const NC = 'A-Za-z0-9_\\u0080-\\uffff';
  // The end of a word: no name character, hyphen or "\Name" follows
  const WE = `(?![${NC}]|-|\\\\[${NS}])`;
  const D = '[0-9](?:_?[0-9])*';
  const E = `[eE][+-]?${D}`;
  const SIGN = '(?:[+-]\\s*)?';
  const TRIVIA = '(?:\\s+|\\/\\*[\\s\\S]*?\\*\\/|(?:\\/\\/|#)[^\\r\\n]*)';

  // [type, pattern, skip]. The order matters: the first match wins.
  const RULES = [
    ['T_WHITESPACE', /\s+/y, true],
    ['T_COMMENT', /(?:\/\/|#)[^\r\n]*/y, true],
    ['T_DOC_COMMENT', /\/\*[\s\S]*?\*\//y, true],
    ['T_IS_NOT', new RegExp(`is${WE}${TRIVIA}+not${WE}`, 'y')],
    ['T_THIS', new RegExp(`\\$this${WE}`, 'y')],
    ['T_VARIABLE', new RegExp(`\\$[${NS}][${NC}]*`, 'y')],
    ['T_FLOAT', new RegExp(`${SIGN}(?:${D}\\.(?:${D})?(?:${E})?|\\.${D}(?:${E})?|${D}${E})`, 'y')],
    ['T_INT_HEX', new RegExp(`${SIGN}0[xX][0-9a-fA-F](?:_?[0-9a-fA-F])*`, 'y')],
    ['T_INT_BIN', new RegExp(`${SIGN}0[bB][01](?:_?[01])*`, 'y')],
    ['T_INT_OCT', new RegExp(`${SIGN}0[oO][0-7](?:_?[0-7])*`, 'y')],
    ['T_INT_OCT_LEGACY', new RegExp(`${SIGN}0(?:_?[0-7])*`, 'y')],
    ['T_INT_DEC', new RegExp(`${SIGN}[1-9](?:_?[0-9])*`, 'y')],
    ['T_SQ_STRING', /'(?:[^'\\]|\\[\s\S])*'/y],
    ['T_DQ_STRING', /"(?:[^"\\]|\\[\s\S])*"/y],
    // PHP_*, *_SUFFIX, A*B*C. A lone star is a wildcard
    ['T_IDENTIFIER_MASK', new RegExp(`(?!\\*(?![${NC}]))(?:[${NS}][${NC}]*\\*|\\*)(?:[${NC}]+\\*)*[${NC}]*`, 'y')],
    // non-empty-string
    ['T_VIRTUAL_NAME', new RegExp(`[${NS}][${NC}]*(?:-[${NC}]+)+`, 'y')],
    // Foo, PHP_EOL
    ['T_IDENTIFIER', new RegExp(`[${NS}][${NC}]*`, 'y')],
    ['T_NS_DELIMITER', /\\/y],
    ['T_WILDCARD', /\*/y],
    ['T_DOUBLE_COLON', /::/y],
    ['T_COLON', /:/y],
    ['T_ELLIPSIS', /\.\.\./y],
    ['T_ASSIGN', /=/y],
    ['T_UNION', /\|/y],
    ['T_AMPERSAND', /&/y],
    ['T_QUESTION', /\?/y],
    ['T_COMMA', /,/y],
    ['T_PAREN_OPEN', /\(/y],
    ['T_PAREN_CLOSE', /\)/y],
    ['T_ANGLE_OPEN', /</y],
    ['T_ANGLE_CLOSE', />/y],
    ['T_BRACKET_OPEN', /\[/y],
    ['T_BRACKET_CLOSE', /\]/y],
    ['T_BRACE_OPEN', /\{/y],
    ['T_BRACE_CLOSE', /\}/y],
  ];

  const INT_TOKENS = new Set(['T_INT_HEX', 'T_INT_BIN', 'T_INT_OCT', 'T_INT_OCT_LEGACY', 'T_INT_DEC']);
  const STRING_TOKENS = new Set(['T_SQ_STRING', 'T_DQ_STRING']);
  const LITERAL_TOKENS = new Set([...INT_TOKENS, ...STRING_TOKENS, 'T_FLOAT']);
  const VARIANCES = new Set(['covariant', 'contravariant', 'invariant']);

  class ParseError extends Error {
    constructor(message, offset, length, kind) {
      super(message);
      this.name = kind || 'UnexpectedTokenException';
      this.offset = offset;
      this.length = length || 1;
    }
  }

  function tokenize(source) {
    const tokens = [];
    let pos = 0;
    outer: while (pos < source.length) {
      for (const [type, re, skip] of RULES) {
        re.lastIndex = pos;
        const m = re.exec(source);
        if (m && m[0].length > 0) {
          if (!skip) tokens.push({ type, value: m[0], start: pos, end: pos + m[0].length });
          pos += m[0].length;
          continue outer;
        }
      }
      const rest = /\S+/y; rest.lastIndex = pos;
      const bad = rest.exec(source)[0];
      throw new ParseError(`Unrecognized syntax ${quote(bad)}`, pos, bad.length, 'UnrecognizedSyntaxException');
    }
    tokens.push({ type: 'T_EOI', value: '', start: source.length, end: source.length });
    return tokens;
  }

  function quote(s) { return JSON.stringify(s); }

  // ─────────────────────────── Features ────────────────────────────

  // Mirrors TypeLang\Parser\TypeParserFeatures
  const Features = {
    unions: true, intersections: true, nullables: true, lists: true, offsets: true, conditions: true,
    generics: true, wildcards: true, hints: true, callables: true, templates: true, defaults: true,
    shapes: true, constants: true, masks: true, literals: true,
  };

  // PHP class names behind every node kind - shown in the tree tooltips.
  const NODE_CLASSES = {
    type: 'TypeLang\\Type\\NamedTypeNode',
    union: 'TypeLang\\Type\\LogicalTypeNode\\UnionTypeNode',
    intersection: 'TypeLang\\Type\\LogicalTypeNode\\IntersectionTypeNode',
    nullable: 'TypeLang\\Type\\WrappingTypeNode\\NullableTypeNode',
    list: 'TypeLang\\Type\\WrappingTypeNode\\TypesListTypeNode',
    offset: 'TypeLang\\Type\\WrappingTypeNode\\TypeOffsetAccessTypeNode',
    int: 'TypeLang\\Type\\Literal\\IntLiteralNode',
    float: 'TypeLang\\Type\\Literal\\FloatLiteralNode',
    string: 'TypeLang\\Type\\Literal\\StringLiteralNode',
    bool: 'TypeLang\\Type\\Literal\\BoolLiteralNode',
    null: 'TypeLang\\Type\\Literal\\NullLiteralNode',
    const: 'TypeLang\\Type\\Constant\\ClassConstTypeNode',
    classMask: 'TypeLang\\Type\\Constant\\ClassConstMaskTypeNode',
    mask: 'TypeLang\\Type\\Constant\\ConstMaskTypeNode',
    callable: 'TypeLang\\Type\\Callable\\CallableTypeNode',
    params: 'TypeLang\\Type\\Callable\\CallableParameterListNode',
    param: 'TypeLang\\Type\\Callable\\CallableParameterNode',
    default: 'TypeLang\\Type\\Callable\\DefaultValueNode',
    tparams: 'TypeLang\\Type\\Template\\TemplateParameterListNode',
    tparam: 'TypeLang\\Type\\Template\\TemplateParameterNode',
    bound: 'TypeLang\\Type\\Template\\TemplateBoundNode',
    args: 'TypeLang\\Type\\Template\\TemplateListNode',
    arg: 'TypeLang\\Type\\Template\\TypedTemplateArgumentNode',
    wildcard: 'TypeLang\\Type\\Template\\WildcardTemplateArgumentNode',
    fields: 'TypeLang\\Type\\Shape\\FieldListNode',
    field: 'TypeLang\\Type\\Shape\\NamedFieldNode',
    sfield: 'TypeLang\\Type\\Shape\\ScalarFieldNode',
    cfield: 'TypeLang\\Type\\Shape\\ComplexFieldNode',
    implicit: 'TypeLang\\Type\\Shape\\ImplicitFieldNode',
    ident: 'TypeLang\\Type\\Identifier',
    cond: 'TypeLang\\Type\\Condition\\ConditionalTypeNode',
    eq: 'TypeLang\\Type\\Condition\\EqualConditionNode',
    neq: 'TypeLang\\Type\\Condition\\NotEqualConditionNode',
    this: 'TypeLang\\Type\\ThisTypeNode',
    var: 'TypeLang\\Type\\VariableNode',
  };

  // ─────────────────────────── Parser ──────────────────────────────

  class Parser {
    constructor(source, features) {
      this.source = source;
      this.features = Object.assign({}, Features, features || {});
      this.tokens = tokenize(source);
      this.i = 0;
      // The first disabled feature met: thrown once the source is read
      this.violation = null;
    }

    // token helpers
    get tok() { return this.tokens[this.i]; }
    peek(n = 1) { return this.tokens[Math.min(this.i + n, this.tokens.length - 1)]; }
    is(type) { return this.tok.type === type; }
    next() { return this.tokens[this.i++]; }
    accept(type) { return this.is(type) ? this.next() : null; }
    prev() { return this.tokens[this.i - 1]; }
    adjacent(a, b) { return a.end === b.start; }
    mark() { return { i: this.i, v: this.violation }; }
    reset(m) { this.i = m.i; this.violation = m.v; }

    // A word is not a keyword when a "\Name" follows it: "is\Foo" is a name
    wordAt(n, value) {
      const t = this.peek(n);
      if (t.type !== 'T_IDENTIFIER' || (value !== undefined && t.value !== value)) return false;
      const bs = this.peek(n + 1), nx = this.peek(n + 2);
      return !(bs.type === 'T_NS_DELIMITER' && bs.start === t.end && nx.start === bs.end
        && (nx.type === 'T_IDENTIFIER' || (nx.type === 'T_IDENTIFIER_MASK' && nx.value[0] !== '*')));
    }
    isWord(value) { return this.wordAt(0, value); }

    expect(type, message) {
      if (this.is(type)) return this.next();
      throw message ? this.malformed(message) : this.unexpected();
    }

    unexpected(expected, token) {
      const t = token || this.tok;
      const what = t.type === 'T_EOI' ? 'end of input' : quote(t.value);
      return new ParseError(`Unexpected ${what}${expected ? `, expected ${expected}` : ''}`,
        t.start, Math.max(1, t.end - t.start), 'UnexpectedTokenException');
    }

    // A construct with a message of its own in the grammar (@error)
    malformed(message, token) {
      const t = token || this.tok;
      return new ParseError(message, t.start, Math.max(1, t.end - t.start), 'MalformedSyntaxException');
    }

    feature(flag, node) {
      if (this.features[flag] === false && !this.violation) {
        this.violation = new ParseError(`${flag[0].toUpperCase()}${flag.slice(1)} feature is not allowed`,
          node.start, Math.max(1, node.end - node.start), 'FeatureNotEnabledException');
      }
    }

    // A piece of the source with no whitespaces and comments inside: `Foo :: /* c */ BAR` is `Foo::BAR`
    text(start, end) {
      return this.source.slice(start, end).replace(/\/\*[\s\S]*?\*\/|(?:\/\/|#)[^\r\n]*|\s+/g, '');
    }

    node(kind, start, end, props, children) {
      return Object.assign({ kind, start, end, children: children || [] }, props || {});
    }

    // Type
    parseDocument() {
      const type = this.parseType();
      if (!this.is('T_EOI')) throw this.unexpected();
      if (this.violation) throw this.violation;
      return type;
    }

    parseType() { return this.parseConditional(); }

    // Type starts: everything an atom starts with, a nullable mark or a variable
    canStartNullable(t) {
      t = t || this.tok;
      switch (t.type) {
        case 'T_IDENTIFIER': case 'T_VIRTUAL_NAME': case 'T_IDENTIFIER_MASK': case 'T_NS_DELIMITER': case 'T_QUESTION':
        case 'T_PAREN_OPEN': case 'T_THIS': case 'T_FLOAT':
          return true;
        default:
          return LITERAL_TOKENS.has(t.type);
      }
    }
    canStartType(t) {
      t = t || this.tok;
      return this.canStartNullable(t) || t.type === 'T_VARIABLE';
    }

    // ConditionalType : UnionType ConditionTail? | Variable ConditionTail
    parseConditional() {
      let subject;
      if (this.is('T_VARIABLE')) {
        subject = this.parseVariable();
        if (!this.is('T_IS_NOT') && !this.isWord('is')) throw this.unexpected('"is" or "is not"');
      } else {
        subject = this.parseUnion();
        if (!this.is('T_IS_NOT') && !this.isWord('is')) return subject;
      }

      const negated = this.next().type === 'T_IS_NOT';
      const operand = this.is('T_VARIABLE') ? this.parseVariable() : this.parseUnion();
      const test = this.node(negated ? 'neq' : 'eq', subject.start, operand.end, { subject, operand },
        [subject, operand]);
      test.label = negated ? 'is not' : 'is';

      this.expect('T_QUESTION', 'A "?" is expected after the target of the condition');
      const then = this.parseType();
      this.expect('T_COLON', 'The condition requires an else branch, ":" expected');
      const otherwise = this.parseType();

      const node = this.node('cond', subject.start, otherwise.end, { negated, test, then, else: otherwise },
        [test, then, otherwise]);
      this.feature('conditions', node);
      return node;
    }

    parseVariable() {
      const t = this.next();
      return this.node('var', t.start, t.end, { name: t.value });
    }

    // UnionType : IntersectionType (`|` IntersectionType)*
    parseUnion() {
      const first = this.parseIntersection();
      if (!this.is('T_UNION')) return first;
      const items = [first];
      while (this.accept('T_UNION')) items.push(this.parseIntersection());
      const node = this.node('union', first.start, items[items.length - 1].end, null, items);
      this.feature('unions', node);
      return node;
    }

    // IntersectionType : NullableType (`&` NullableType)*
    parseIntersection() {
      const first = this.parseNullable();
      // `&` followed by something that cannot start a type belongs to a
      // callable parameter (`Type &$name`), not to an intersection.
      const more = () => this.is('T_AMPERSAND') && this.canStartNullable(this.peek());
      if (!more()) return first;
      const items = [first];
      while (more()) {
        this.next();
        items.push(this.parseNullable());
      }
      const node = this.node('intersection', first.start, items[items.length - 1].end, null, items);
      this.feature('intersections', node);
      return node;
    }

    // NullableType : `?` ListTypeOrOffsetAccess | ListTypeOrOffsetAccess
    parseNullable() {
      const q = this.accept('T_QUESTION');
      if (!q) return this.parseSuffixed();
      const inner = this.parseSuffixed();
      const node = this.node('nullable', q.start, inner.end, null, [inner]);
      this.feature('nullables', node);
      return node;
    }

    // ListTypeOrOffsetAccess : Atom (`[` `]` | `[` Type `]`)*
    parseSuffixed() {
      let type = this.parseAtom();
      while (this.is('T_BRACKET_OPEN')) {
        this.next();
        if (this.is('T_BRACKET_CLOSE')) {
          const close = this.next();
          type = this.node('list', type.start, close.end, null, [type]);
          this.feature('lists', type);
          continue;
        }
        if (!this.canStartType()) throw this.malformed('A type or "]" is expected after "["');
        const key = this.parseType();
        const close = this.expect('T_BRACKET_CLOSE', 'The offset is not closed properly, "]" expected');
        type = this.node('offset', type.start, close.end, { subject: type, key }, [type, key]);
        this.feature('offsets', type);
      }
      return type;
    }

    // Atom : Literal | NamedType | ThisType | ConstMaskType | Group
    parseAtom() {
      const t = this.tok;

      if (t.type === 'T_PAREN_OPEN') {
        this.next();
        if (!this.canStartType()) throw this.malformed('A type is expected after "("');
        const inner = this.parseType();
        this.expect('T_PAREN_CLOSE', 'The group is not closed properly, ")" expected');
        return inner;
      }

      if (t.type === 'T_THIS') {
        this.next();
        return this.node('this', t.start, t.end);
      }

      if (LITERAL_TOKENS.has(t.type)) {
        this.next();
        return this.literal(t, true);
      }

      if (t.type === 'T_IDENTIFIER_MASK') return this.mask(t.start);

      if (t.type === 'T_IDENTIFIER' || t.type === 'T_VIRTUAL_NAME' || t.type === 'T_NS_DELIMITER') {
        return this.parseNamed();
      }

      throw this.unexpected();
    }

    literal(t, check) {
      const kind = STRING_TOKENS.has(t.type) ? 'string' : t.type === 'T_FLOAT' ? 'float' : 'int';
      const node = this.node(kind, t.start, t.end, { value: t.value });
      if (check) this.feature('literals', node);
      return node;
    }

    // PHP_*, \PHP_*, Foo\BAR_*, Foo\*, \*  (the namespace is consumed already)
    mask(start) {
      const t = this.next();
      const node = this.node('mask', start, t.end, { value: this.text(start, t.end) });
      this.feature('masks', node);
      return node;
    }

    isMaskToken(t) { return t.type === 'T_IDENTIFIER_MASK' || t.type === 'T_WILDCARD'; }

    // Name : Namespace Word | VirtualName | Word. A hyphenated name stands alone
    parseName() {
      const start = this.tok.start;
      const parts = [];
      let fq = false;
      if (this.is('T_VIRTUAL_NAME')) {
        const t = this.next();
        return { start, end: t.end, parts: [t.value], fq, text: t.value };
      }
      if (this.accept('T_NS_DELIMITER')) fq = true;
      if (!this.is('T_IDENTIFIER') || (fq && !this.adjacent(this.prev(), this.tok))) throw this.unexpected();
      parts.push(this.next().value);
      while (this.is('T_NS_DELIMITER') && this.adjacent(this.prev(), this.tok)
        && this.peek().type === 'T_IDENTIFIER' && this.adjacent(this.tok, this.peek())) {
        this.next();
        parts.push(this.next().value);
      }
      return { start, end: this.prev().end, parts, fq, text: (fq ? '\\' : '') + parts.join('\\') };
    }

    // NamedType : Name (`::` ConstantMask | TemplateParameters CallableSignature | TemplateArguments
    //                   | ShapeFields | CallableSignature)?
    parseNamed() {
      // \PHP_*, \*
      if (this.is('T_NS_DELIMITER') && this.isMaskToken(this.peek()) && this.adjacent(this.tok, this.peek())) {
        const start = this.next().start;
        return this.mask(start);
      }

      // null, true, false
      const lower = this.tok.value.toLowerCase();
      if (this.tok.type === 'T_IDENTIFIER' && (lower === 'true' || lower === 'false' || lower === 'null')
        && this.wordAt(0)) {
        const t = this.next();
        const node = this.node(lower === 'null' ? 'null' : 'bool', t.start, t.end, { value: t.value });
        this.feature('literals', node);
        return node;
      }

      const name = this.parseName();

      // Foo\BAR_*, Foo\*
      if (this.is('T_NS_DELIMITER') && this.adjacent(this.prev(), this.tok) && this.prev().type !== 'T_VIRTUAL_NAME'
        && this.isMaskToken(this.peek()) && this.adjacent(this.tok, this.peek())) {
        this.next();
        return this.mask(name.start);
      }

      // Foo::BAR, Foo::BAR_*, Foo::*
      if (this.is('T_DOUBLE_COLON')) {
        this.next();
        if (this.isMaskToken(this.tok)) {
          const t = this.next();
          const node = this.node('classMask', name.start, t.end, { value: this.text(name.start, t.end) });
          this.feature('masks', node);
          return node;
        }
        if (!this.is('T_IDENTIFIER')) throw this.unexpected();
        const id = this.next();
        const node = this.node('const', name.start, id.end,
          { value: this.text(name.start, id.end), class: name.text, constant: id.value });
        this.feature('constants', node);
        return node;
      }

      // Foo<T of U>(T): T
      if (this.is('T_ANGLE_OPEN')) {
        const m = this.mark();
        try {
          const tparams = this.parseTemplateParameters();
          if (this.is('T_PAREN_OPEN')) return this.parseCallable(name, tparams);
        } catch (e) {
          if (!(e instanceof ParseError)) throw e;
        }
        this.reset(m);
      }

      // Foo(int): void
      if (this.is('T_PAREN_OPEN')) return this.parseCallable(name, null);

      const node = this.node('type', name.start, name.end, { name: name.text });

      if (this.is('T_ANGLE_OPEN')) {
        // Foo<T>
        node.args = this.parseTemplateArguments();
        node.end = node.args.end;
        node.children.push(node.args);
      } else if (this.is('T_BRACE_OPEN')) {
        // Foo{...}
        node.fields = this.parseShapeFields();
        node.end = node.fields.end;
        node.children.push(node.fields);
      }
      return node;
    }

    // TemplateArguments : `<` TemplateArgument (`,` TemplateArgument)* `,`? `>`
    parseTemplateArguments() {
      const open = this.expect('T_ANGLE_OPEN');
      const args = [];
      if (!this.canStartArgument()) throw this.malformed('A template argument is expected after "<"');
      args.push(this.parseTemplateArgument());
      while (this.accept('T_COMMA')) {
        if (this.is('T_ANGLE_CLOSE')) break;
        args.push(this.parseTemplateArgument());
      }
      const close = this.expect('T_ANGLE_CLOSE', 'Invalid template arguments, "," or ">" is expected');
      const node = this.node('args', open.start, close.end, null, args);
      this.feature('generics', node);
      return node;
    }

    canStartArgument() {
      return this.canStartType() || this.is('T_WILDCARD');
    }

    // TemplateArgument : `*` | Variance Type | Type
    parseTemplateArgument() {
      const start = this.tok.start;
      if (this.is('T_WILDCARD')) {
        const t = this.next();
        const node = this.node('wildcard', t.start, t.end);
        this.feature('wildcards', node);
        return node;
      }
      let variance = null;
      if (this.tok.type === 'T_IDENTIFIER' && VARIANCES.has(this.tok.value) && this.wordAt(0)
        && this.canStartType(this.peek())) {
        variance = this.next().value;
      }
      const type = this.parseType();
      const node = this.node('arg', start, type.end, { variance, value: type }, [type]);
      node.label = variance ? `arg · ${variance}` : 'arg';
      if (variance) this.feature('hints', node);
      return node;
    }

    // ShapeFields : `{` (ShapeFieldList ShapeTail? | ShapeUnsealed)? `}`
    parseShapeFields() {
      const open = this.expect('T_BRACE_OPEN');
      const items = [];
      let unsealed = false, args = null;

      const readUnsealed = () => {
        this.next();
        unsealed = true;
        if (this.is('T_ANGLE_OPEN')) args = this.parseTemplateArguments();
        this.accept('T_COMMA');
      };

      if (this.is('T_ELLIPSIS')) {
        readUnsealed();
      } else if (!this.is('T_BRACE_CLOSE')) {
        items.push(this.parseShapeField());
        while (this.accept('T_COMMA')) {
          if (this.is('T_BRACE_CLOSE')) break;
          if (this.is('T_ELLIPSIS')) { readUnsealed(); break; }
          items.push(this.parseShapeField());
        }
      }
      const close = this.expect('T_BRACE_CLOSE', 'Invalid shape, a field, "," or "}" is expected');

      // The arguments of "..." sit inside the braces, so they are drawn as a part of the list
      const node = this.node('fields', open.start, close.end, { sealed: !unsealed, items, args },
        args ? [...items, args] : items);
      node.label = unsealed ? 'fields · unsealed' : 'fields';
      this.feature('shapes', node);
      return node;
    }

    // ShapeField : ShapeKey `?`? `:` Type | Type
    parseShapeField() {
      const start = this.tok.start;
      const m = this.mark();
      const key = this.tryParseShapeKey();
      if (key) {
        const optional = !!this.accept('T_QUESTION');
        if (this.accept('T_COLON')) {
          if (!this.canStartType()) throw this.malformed('A type is expected after ":"');
          const value = this.parseType();
          const kind = key.kind === 'ident' ? 'field' : key.kind === 'const' ? 'cfield' : 'sfield';
          const node = this.node(kind, start, value.end, { key, optional, value }, [key, value]);
          node.label = optional ? 'field?' : 'field';
          return node;
        }
        this.reset(m);
      }
      const value = this.parseType();
      return this.node('implicit', start, value.end, { value }, [value]);
    }

    // ShapeKey : Name `::` Identifier | Name | IntLiteral | StringLiteral
    tryParseShapeKey() {
      const t = this.tok;
      if (INT_TOKENS.has(t.type) || STRING_TOKENS.has(t.type)) {
        this.next();
        return this.literal(t, false);
      }
      if (t.type !== 'T_IDENTIFIER' && t.type !== 'T_VIRTUAL_NAME' && t.type !== 'T_NS_DELIMITER') return null;
      const m = this.mark();
      try {
        const name = this.parseName();
        if (this.is('T_DOUBLE_COLON') && this.peek().type === 'T_IDENTIFIER') {
          this.next();
          const id = this.next();
          return this.node('const', name.start, id.end,
            { value: this.text(name.start, id.end), class: name.text, constant: id.value });
        }
        return this.node('ident', name.start, name.end, { value: name.text });
      } catch (e) {
        if (!(e instanceof ParseError)) throw e;
        this.reset(m);
        return null;
      }
    }

    // TemplateParameters : `<` TemplateParameter (`,` TemplateParameter)* `,`? `>`
    parseTemplateParameters() {
      const open = this.expect('T_ANGLE_OPEN');
      const params = [];
      if (!this.is('T_IDENTIFIER')) throw this.malformed('A template argument is expected after "<"');
      params.push(this.parseTemplateParameter());
      while (this.accept('T_COMMA')) {
        if (this.is('T_ANGLE_CLOSE')) break;
        params.push(this.parseTemplateParameter());
      }
      const close = this.expect('T_ANGLE_CLOSE', 'Invalid template arguments, "," or ">" is expected');
      const node = this.node('tparams', open.start, close.end, null, params);
      node.label = 'templates';
      this.feature('templates', node);
      return node;
    }

    // TemplateParameter : Variance? Identifier TemplateUpperBound? TemplateLowerBound? (`=` Type)?
    parseTemplateParameter() {
      const start = this.tok.start;
      let variance = null;
      if (VARIANCES.has(this.tok.value) && this.wordAt(0) && this.peek().type === 'T_IDENTIFIER') {
        variance = this.next().value;
      }
      const id = this.expect('T_IDENTIFIER');
      const node = this.node('tparam', start, id.end, { name: id.value, variance, upper: null, lower: null, default: null });
      node.label = variance ? `template · ${variance}` : 'template';
      const bound = () => {
        const k = this.next();
        const type = this.parseType();
        const b = this.node('bound', k.start, type.end, { type }, [type]);
        b.label = k.value;
        node.children.push(b);
        node.end = b.end;
        return b;
      };
      if (this.isWord('of') || this.isWord('as')) node.upper = bound();
      if (this.isWord('super')) node.lower = bound();
      if (this.accept('T_ASSIGN')) {
        node.default = this.parseType();
        node.children.push(node.default);
        node.end = node.default.end;
      }
      return node;
    }

    // CallableType : Name TemplateParameters? CallableParameters (`:` NullableType)?
    parseCallable(name, tparams) {
      const open = this.expect('T_PAREN_OPEN');
      const params = [];
      if (!this.is('T_PAREN_CLOSE')) {
        params.push(this.parseCallableParameter());
        while (this.accept('T_COMMA')) {
          if (this.is('T_PAREN_CLOSE')) break;
          params.push(this.parseCallableParameter());
        }
      }
      const close = this.expect('T_PAREN_CLOSE', 'Invalid callable parameters, a parameter, "," or ")" is expected');
      const list = this.node('params', open.start, close.end, null, params);
      this.feature('callables', list);

      let returns = null, end = close.end;
      if (this.accept('T_COLON')) {
        returns = this.parseNullable();
        end = returns.end;
      }
      const children = [...(tparams ? [tparams] : []), list, ...(returns ? [returns] : [])];
      return this.node('callable', name.start, end, { name: name.text, tparams, params: list, returns }, children);
    }

    // CallableParameter : Type `&`? (`...` Variable? | Variable? Default?) | `...` Type | `&`? (`...` Variable | Variable Default?)
    parseCallableParameter() {
      const start = this.tok.start;
      const node = this.node('param', start, start, { type: null, name: null, byRef: false, variadic: false, default: null });
      const add = (child) => { node.children.push(child); node.end = child.end; };
      const mark = (t) => { node.end = t.end; };

      if (this.is('T_ELLIPSIS') && this.peek().type !== 'T_VARIABLE') {
        // ...int
        mark(this.next());
        node.variadic = true;
        node.type = this.parseType();
        add(node.type);
        return node;
      }

      // "$a" alone is a name of a parameter, "$a is T ? A : B" is its type
      const bareVariable = this.is('T_VARIABLE') && !this.wordAt(1, 'is') && this.peek().type !== 'T_IS_NOT';
      if (!this.is('T_ELLIPSIS') && !this.is('T_AMPERSAND') && !bareVariable) {
        node.type = this.parseType();
        add(node.type);
        if (this.is('T_AMPERSAND')) { mark(this.next()); node.byRef = true; }
      } else if (this.is('T_AMPERSAND')) {
        mark(this.next());
        node.byRef = true;
      }

      if (this.is('T_ELLIPSIS')) {
        mark(this.next());
        node.variadic = true;
        if (this.is('T_VARIABLE')) { node.name = this.parseVariable(); add(node.name); }
        else if (!node.type) throw this.unexpected();
        return node;
      }

      if (this.is('T_VARIABLE')) { node.name = this.parseVariable(); add(node.name); }
      else if (!node.type) throw this.unexpected();

      if (this.is('T_ASSIGN')) {
        const eq = this.next();
        const def = this.node('default', eq.start, eq.end, { value: null });
        if (LITERAL_TOKENS.has(this.tok.type)) {
          def.value = this.literal(this.next(), true);
        } else if (this.is('T_IDENTIFIER') || this.is('T_VIRTUAL_NAME') || this.is('T_NS_DELIMITER')) {
          def.value = this.parseDefaultConstant();
        }
        if (def.value) { def.children.push(def.value); def.end = def.value.end; }
        this.feature('defaults', def);
        node.default = def;
        add(def);
      }
      return node;
    }

    // CallableConstant : null | true | false | FOO | \PHP_EOL | Foo::BAR
    parseDefaultConstant() {
      const lower = this.tok.value.toLowerCase();
      if (this.tok.type === 'T_IDENTIFIER' && (lower === 'true' || lower === 'false' || lower === 'null')
        && this.wordAt(0)) {
        const t = this.next();
        const node = this.node(lower === 'null' ? 'null' : 'bool', t.start, t.end, { value: t.value });
        this.feature('literals', node);
        return node;
      }
      const name = this.parseName();
      if (this.accept('T_DOUBLE_COLON')) {
        if (!this.is('T_IDENTIFIER')) throw this.unexpected();
        const id = this.next();
        return this.node('const', name.start, id.end,
          { value: this.text(name.start, id.end), class: name.text, constant: id.value });
      }
      return this.node('type', name.start, name.end, { name: name.text });
    }
  }

  function parse(source, features) {
    const parser = new Parser(source, features);
    try {
      return parser.parseDocument();
    } catch (e) {
      if (e instanceof ParseError) {
        e.message = `${e.message} in ${quote(source)}`;
        e.source = source;
      }
      throw e;
    }
  }

  // ─────────────────────────── Printer ─────────────────────────────
  // A compact pretty printer in the spirit of TypeLang\Printer\PrettyTypePrinter.

  function print(node, opts) {
    const indent = (opts && opts.indent) || '    ';
    const nl = (opts && opts.newLine) || '\n';

    // Whether a child needs parentheses to stay what it is inside its parent
    const needs = (child, parent) => {
      switch (child.kind) {
        case 'union': case 'intersection':
          // The members of a flat list of the same kind are not wrapped
          return ['nullable', 'list', 'offset', 'callable'].includes(parent) || (parent !== child.kind
            && (parent === 'union' || parent === 'intersection'));
        case 'nullable':
          return ['nullable', 'list', 'offset'].includes(parent);
        case 'callable':
          return child.returns !== null && ['union', 'intersection', 'list', 'offset'].includes(parent);
        default:
          return false;
      }
    };
    const wrap = (child, parent, depth) => {
      const s = go(child, depth || 0);
      return needs(child, parent) ? `(${s})` : s;
    };

    const go = (n, depth) => {
      const pad = indent.repeat(depth);
      switch (n.kind) {
        case 'type': {
          let s = n.name;
          if (n.args) s += go(n.args, depth);
          if (n.fields) s += go(n.fields, depth);
          return s;
        }
        case 'args': return `<${n.children.map(a => go(a, depth)).join(', ')}>`;
        case 'arg': return (n.variance ? n.variance + ' ' : '') + go(n.value, depth);
        case 'wildcard': return '*';
        case 'fields': {
          const items = n.items.map(f => go(f, depth + 1));
          if (n.sealed === false) items.push('...' + (n.args ? go(n.args, depth) : ''));
          if (items.length === 0) return '{}';
          if (items.length === 1) return `{${items[0]}}`;
          return `{${nl}${items.map(i => pad + indent + i).join(',' + nl)}${nl}${pad}}`;
        }
        case 'field': case 'sfield': case 'cfield':
          return `${go(n.key, depth)}${n.optional ? '?' : ''}: ${go(n.value, depth)}`;
        case 'implicit': return go(n.value, depth);
        case 'ident': return n.value;
        case 'union': case 'intersection': {
          // Nested lists of the same kind are one list, a repeated member is printed once
          const flat = (x) => (x.kind === n.kind ? x.children.flatMap(flat) : [x]);
          const parts = [...new Set(flat(n).map(c => wrap(c, n.kind, depth)))];
          return parts.length === 1 ? parts[0] : parts.join(n.kind === 'union' ? '|' : ' & ');
        }
        case 'nullable': return '?' + wrap(n.children[0], 'nullable', depth);
        case 'list': return wrap(n.children[0], 'list', depth) + '[]';
        case 'offset': return wrap(n.subject, 'offset', depth) + '[' + go(n.key, depth) + ']';
        case 'int': case 'float': case 'string': case 'bool': case 'null': case 'const': case 'classMask': case 'mask':
          return n.value;
        case 'this': return '$this';
        case 'var': return n.name;
        case 'cond': {
          const t = n.test;
          // A condition is always parenthesized, like the PHP printer does
          return `(${go(t.subject, depth)} ${n.negated ? 'is not' : 'is'} ${go(t.operand, depth)}`
            + ` ? ${go(n.then, depth)} : ${go(n.else, depth)})`;
        }
        case 'callable': {
          let s = n.name;
          if (n.tparams) s += `<${n.tparams.children.map(p => go(p, depth)).join(', ')}>`;
          s += `(${n.params.children.map(p => go(p, depth)).join(', ')})`;
          if (n.returns) s += `: ${wrap(n.returns, 'callable', depth)}`;
          return s;
        }
        case 'tparam': {
          let s = (n.variance ? n.variance + ' ' : '') + n.name;
          if (n.upper) s += ` ${n.upper.label} ${go(n.upper.type, depth)}`;
          if (n.lower) s += ` super ${go(n.lower.type, depth)}`;
          if (n.default) s += ` = ${go(n.default, depth)}`;
          return s;
        }
        case 'param': {
          let s = n.type ? go(n.type, depth) : '';
          if (n.type && n.name) s += ' ';
          if (n.byRef) s += '&';
          if (n.variadic) s += '...';
          if (n.name) s += n.name.name;
          if (n.default) s += n.default.value ? ` = ${go(n.default.value, depth)}` : '=';
          return s;
        }
        default: return '?';
      }
    };
    return go(node, 0);
  }

  // ─────────────────────────── Helpers ─────────────────────────────

  function walk(node, fn, depth = 0, path = '0') {
    fn(node, depth, path);
    node.children.forEach((c, i) => walk(c, fn, depth + 1, path + '.' + i));
  }

  function count(node) {
    let n = 0;
    walk(node, () => n++);
    return n;
  }

  global.TypeLang = { tokenize, parse, print, walk, count, Features, NODE_CLASSES, ParseError };
})(typeof window !== 'undefined' ? window : globalThis);
