/*
 * TypeLang — a browser port of the reference parser (type-lang/parser).
 *
 * The grammar follows "Appendix B — Grammar Summary" of the TypeLang
 * specification. Every AST node carries `start`/`end` byte offsets so the
 * landing can draw the tree right under the source text.
 *
 * Exposes `window.TypeLang = { tokenize, parse, print, Features, NODE_CLASSES }`.
 */
(function (global) {
  'use strict';

  // ───────────────────────────── Lexer ─────────────────────────────

  const RULES = [
    ['T_WHITESPACE', /\s+/y, true],
    ['T_COMMENT', /\/\/[^\n]*|#[^\n]*|\/\*[\s\S]*?\*\//y, true],
    ['T_VARIABLE', /\$[A-Za-z_\u0080-\uffff][A-Za-z0-9_\u0080-\uffff]*/y],
    ['T_FLOAT_LITERAL', /[+-]?(?:\d(?:_?\d)*\.(?:\d(?:_?\d)*)?(?:[eE][+-]?\d(?:_?\d)*)?|\.\d(?:_?\d)*(?:[eE][+-]?\d(?:_?\d)*)?|\d(?:_?\d)*[eE][+-]?\d(?:_?\d)*)/y],
    ['T_BIN_INT_LITERAL', /[+-]?0[bB][01](?:_?[01])*/y],
    ['T_HEX_INT_LITERAL', /[+-]?0[xX][0-9a-fA-F](?:_?[0-9a-fA-F])*/y],
    ['T_OCT_INT_LITERAL', /[+-]?0[oO][0-7](?:_?[0-7])*|[+-]?0(?:_?[0-7])+/y],
    ['T_DEC_INT_LITERAL', /[+-]?(?:[1-9](?:_?\d)*|0)/y],
    ['T_SINGLE_QUOTED_STRING', /'(?:[^'\\]|\\[\s\S])*'/y],
    ['T_DOUBLE_QUOTED_STRING', /"(?:[^"\\]|\\[\s\S])*"/y],
    ['T_NAME', /[A-Za-z_\u0080-\uffff][A-Za-z0-9_\u0080-\uffff-]*/y],
    ['T_DOUBLE_COLON', /::/y],
    ['T_ELLIPSIS', /\.\.\./y],
    ['T_NS_DELIMITER', /\\/y],
    ['T_NULLABLE', /\?/y],
    ['T_OR', /\|/y],
    ['T_AMP', /&/y],
    ['T_ASTERISK', /\*/y],
    ['T_COMMA', /,/y],
    ['T_COLON', /:/y],
    ['T_ASSIGN', /=/y],
    ['T_PARENTHESIS_OPEN', /\(/y],
    ['T_PARENTHESIS_CLOSE', /\)/y],
    ['T_SQUARE_BRACKET_OPEN', /\[/y],
    ['T_SQUARE_BRACKET_CLOSE', /\]/y],
    ['T_BRACE_OPEN', /\{/y],
    ['T_BRACE_CLOSE', /\}/y],
    ['T_ANGLE_BRACKET_OPEN', /</y],
    ['T_ANGLE_BRACKET_CLOSE', />/y],
  ];

  const INT_TOKENS = new Set(['T_BIN_INT_LITERAL', 'T_HEX_INT_LITERAL', 'T_OCT_INT_LITERAL', 'T_DEC_INT_LITERAL']);
  const STRING_TOKENS = new Set(['T_SINGLE_QUOTED_STRING', 'T_DOUBLE_QUOTED_STRING']);
  const LITERAL_TOKENS = new Set([...INT_TOKENS, ...STRING_TOKENS, 'T_FLOAT_LITERAL']);

  class ParseError extends Error {
    constructor(message, offset, length, kind) {
      super(message);
      this.name = kind || 'ParsingException';
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
      // Unrecognized: swallow up to the next whitespace so the message reads like the PHP one.
      const rest = /\S+/y; rest.lastIndex = pos;
      const bad = rest.exec(source)[0];
      throw new ParseError(`Syntax error, unexpected ${quote(bad)} (unknown token)`, pos, bad.length, 'UnrecognizedTokenException');
    }
    tokens.push({ type: 'T_EOI', value: '', start: source.length, end: source.length });
    return tokens;
  }

  function quote(s) { return JSON.stringify(s); }

  // ─────────────────────────── Features ────────────────────────────

  const Features = {
    literals: true, generics: true, hints: true, lists: true, offsets: true,
    callables: true, shapes: true, unions: true, intersections: true, conditions: true,
  };

  const FEATURE_MESSAGES = {
    literals: 'Literal values not allowed',
    generics: 'Template arguments not allowed',
    templateParameters: 'Template parameters not allowed',
    hints: 'Template argument hints not allowed',
    lists: 'Square bracket list types not allowed',
    offsets: 'Type offsets not allowed',
    callables: 'Callable types not allowed',
    shapes: 'Shape fields not allowed',
    unions: 'Union types not allowed',
    intersections: 'Intersection types not allowed',
    conditions: 'Conditional expressions not allowed',
  };

  // PHP class names behind every node kind — shown in the tree tooltips.
  const NODE_CLASSES = {
    named: 'TypeLang\\Type\\NamedTypeNode',
    union: 'TypeLang\\Type\\UnionTypeNode',
    intersection: 'TypeLang\\Type\\IntersectionTypeNode',
    nullable: 'TypeLang\\Type\\NullableTypeNode',
    list: 'TypeLang\\Type\\TypesListNode',
    offset: 'TypeLang\\Type\\TypeOffsetAccessNode',
    int: 'TypeLang\\Type\\Literal\\IntLiteralNode',
    float: 'TypeLang\\Type\\Literal\\FloatLiteralNode',
    string: 'TypeLang\\Type\\Literal\\StringLiteralNode',
    bool: 'TypeLang\\Type\\Literal\\BoolLiteralNode',
    null: 'TypeLang\\Type\\Literal\\NullLiteralNode',
    const: 'TypeLang\\Type\\ClassConstNode',
    classMask: 'TypeLang\\Type\\ClassConstMaskNode',
    mask: 'TypeLang\\Type\\ConstMaskNode',
    callable: 'TypeLang\\Type\\CallableTypeNode',
    param: 'TypeLang\\Type\\Callable\\CallableParameterNode',
    tparam: 'TypeLang\\Type\\Template\\TemplateParameterNode',
    arg: 'TypeLang\\Type\\Template\\TemplateArgumentNode',
    wildcard: 'TypeLang\\Type\\WildcardNode',
    field: 'TypeLang\\Type\\Shape\\ExplicitFieldNode',
    implicit: 'TypeLang\\Type\\Shape\\ImplicitFieldNode',
    unsealed: 'TypeLang\\Type\\Shape\\FieldsListNode',
    cond: 'TypeLang\\Type\\TernaryExpressionNode',
    this: 'TypeLang\\Type\\ThisNode',
    var: 'TypeLang\\Type\\VariableNode',
  };

  // ─────────────────────────── Parser ──────────────────────────────

  class Parser {
    constructor(source, features) {
      this.source = source;
      this.features = Object.assign({}, Features, features || {});
      this.tokens = tokenize(source);
      this.i = 0;
    }

    // token helpers
    get tok() { return this.tokens[this.i]; }
    peek(n = 1) { return this.tokens[Math.min(this.i + n, this.tokens.length - 1)]; }
    is(type, value) { const t = this.tok; return t.type === type && (value === undefined || t.value === value); }
    isName(value) { return this.is('T_NAME', value); }
    next() { return this.tokens[this.i++]; }
    accept(type, value) { if (this.is(type, value)) return this.next(); return null; }

    expect(type, hint) {
      if (this.is(type)) return this.next();
      throw this.unexpected(hint || `${type} expected`);
    }

    unexpected(hint, token) {
      const t = token || this.tok;
      const what = t.type === 'T_EOI' ? 'end of input' : `${quote(t.value)} (${t.type})`;
      return new ParseError(`Syntax error, unexpected ${what}, ${hint}`, t.start, Math.max(1, t.end - t.start), 'UnexpectedTokenException');
    }

    semantic(message, node, kind) {
      return new ParseError(message, node.start, Math.max(1, node.end - node.start), kind || 'SemanticException');
    }

    feature(flag, node, key) {
      if (this.features[flag] === false) {
        throw this.semantic(FEATURE_MESSAGES[key || flag], node, 'FeatureNotAllowedException');
      }
    }

    node(kind, start, end, props) {
      return Object.assign({ kind, start, end, children: [] }, props || {});
    }

    // Document : Type
    parseDocument() {
      const type = this.parseType();
      if (!this.is('T_EOI')) {
        throw this.unexpected('end of input expected');
      }
      return type;
    }

    parseType() { return this.parseConditional(); }

    // ConditionalType : LogicalType ConditionalSuffix? | Variable ConditionalSuffix
    parseConditional() {
      let subject;
      if (this.is('T_VARIABLE') && this.tok.value !== '$this') {
        const t = this.next();
        subject = this.node('var', t.start, t.end, { name: t.value });
        if (!this.isName('is')) {
          throw this.unexpected('a variable can only be the subject of a conditional type, for example "$var is T ? A : B"');
        }
      } else {
        subject = this.parseLogical();
      }
      if (!this.isName('is')) return subject;

      const isTok = this.next();
      let negated = false;
      if (this.isName('not')) { this.next(); negated = true; }
      if (this.is('T_EOI') || this.is('T_NULLABLE')) {
        throw this.unexpected('a conditional type must carry the type to compare with after "is", for example "T is U ? A : B"');
      }
      let operand;
      if (this.is('T_VARIABLE') && this.tok.value !== '$this') {
        const t = this.next();
        operand = this.node('var', t.start, t.end, { name: t.value });
      } else {
        operand = this.parseLogical();
      }
      this.expect('T_NULLABLE', 'a conditional type must carry a question mark "?" after the condition, for example "T is U ? A : B"');
      const then = this.parseType();
      this.expect('T_COLON', 'a conditional type must carry a colon ":" between the branches, for example "T is U ? A : B"');
      const otherwise = this.parseType();
      const node = this.node('cond', subject.start, otherwise.end, {
        negated, subject, operand, then, else: otherwise,
        children: [subject, operand, then, otherwise],
        label: negated ? 'is not' : 'is',
      });
      this.feature('conditions', node);
      return node;
    }

    // UnionType : IntersectionType (`|` UnionType)?
    parseLogical() {
      const first = this.parseIntersection();
      if (!this.is('T_OR')) return first;
      const items = [first];
      while (this.accept('T_OR')) {
        if (!this.canStartType()) {
          throw this.unexpected('a union type must carry a type after the vertical bar "|", for example "T|U"');
        }
        items.push(this.parseIntersection());
      }
      const node = this.node('union', first.start, items[items.length - 1].end, { children: items });
      this.feature('unions', node);
      return node;
    }

    // IntersectionType : UnaryType (`&` IntersectionType)?
    parseIntersection() {
      const first = this.parseNullable();
      // `&` followed by something that cannot start a type belongs to a
      // callable parameter (`Type &$name`), not to an intersection.
      if (!(this.is('T_AMP') && this.canStartType(this.peek()))) return first;
      const items = [first];
      while (this.is('T_AMP') && this.canStartType(this.peek())) {
        this.next();
        items.push(this.parseNullable());
      }
      const node = this.node('intersection', first.start, items[items.length - 1].end, { children: items });
      this.feature('intersections', node);
      return node;
    }

    canStartType(t) {
      t = t || this.tok;
      switch (t.type) {
        case 'T_NAME': case 'T_NS_DELIMITER': case 'T_NULLABLE': case 'T_PARENTHESIS_OPEN':
        case 'T_ASTERISK': case 'T_FLOAT_LITERAL': case 'T_SINGLE_QUOTED_STRING': case 'T_DOUBLE_QUOTED_STRING':
        case 'T_BIN_INT_LITERAL': case 'T_HEX_INT_LITERAL': case 'T_OCT_INT_LITERAL': case 'T_DEC_INT_LITERAL':
          return true;
        case 'T_VARIABLE':
          return t.value === '$this';
        default:
          return false;
      }
    }

    // NullableType : `?`? PostfixType
    parseNullable() {
      const q = this.accept('T_NULLABLE');
      if (!q) return this.parsePostfix();
      if (!this.canStartType()) {
        throw this.unexpected('a nullable type must carry the type it makes nullable, for example "?T"');
      }
      const inner = this.parsePostfix();
      return this.node('nullable', q.start, inner.end, { children: [inner] });
    }

    // PostfixType : PrimaryType TypeSuffix*
    parsePostfix() {
      let type = this.parsePrimary();
      while (this.is('T_SQUARE_BRACKET_OPEN')) {
        const open = this.next();
        if (this.accept('T_SQUARE_BRACKET_CLOSE')) {
          type = this.node('list', type.start, open.end + 1, { children: [type] });
          this.feature('lists', type);
          continue;
        }
        if (!this.canStartType()) {
          throw this.unexpected('a list type must be closed with a bracket "]", for example "T[]"');
        }
        const key = this.parseType();
        const close = this.expect('T_SQUARE_BRACKET_CLOSE', 'a type offset must be closed with a bracket "]", for example "T[K]"');
        type = this.node('offset', type.start, close.end, { children: [type, key], subject: type, key });
        this.feature('offsets', type);
      }
      return type;
    }

    // PrimaryType : `(` Type `)` | ThisVariable | LiteralType | CallableType | NamedType
    parsePrimary() {
      const t = this.tok;

      if (t.type === 'T_PARENTHESIS_OPEN') {
        this.next();
        const inner = this.parseType();
        this.expect('T_PARENTHESIS_CLOSE', 'a group must be closed with a parenthesis ")", for example "(T|U)"');
        return inner;
      }

      if (t.type === 'T_VARIABLE') {
        if (t.value === '$this') { this.next(); return this.node('this', t.start, t.end); }
        throw this.unexpected('a variable can only be the subject of a conditional type, for example "$var is T ? A : B"');
      }

      if (LITERAL_TOKENS.has(t.type)) {
        this.next();
        const kind = STRING_TOKENS.has(t.type) ? 'string' : t.type === 'T_FLOAT_LITERAL' ? 'float' : 'int';
        const node = this.node(kind, t.start, t.end, { value: t.value });
        this.feature('literals', node);
        return node;
      }

      if (t.type === 'T_ASTERISK') {
        // LeadingMask : `*` Identifier MaskTail?
        this.next();
        if (!this.is('T_NAME')) {
          throw this.unexpected('a constant mask must carry a name next to the asterisk, for example "*_SUFFIX"', t);
        }
        const end = this.parseMaskTail();
        return this.node('mask', t.start, end, { value: this.source.slice(t.start, end) });
      }

      if (t.type === 'T_NAME' || t.type === 'T_NS_DELIMITER') {
        return this.parseNamed();
      }

      throw this.unexpected('a type expected');
    }

    // Name : FullyQualifiedName | RelativeName
    parseName() {
      const start = this.tok.start;
      const parts = [];
      let fq = false;
      if (this.accept('T_NS_DELIMITER')) fq = true;
      if (!this.is('T_NAME')) {
        throw this.unexpected('a name must follow the namespace delimiter "\\", for example "\\Foo\\Bar"');
      }
      parts.push(this.next().value);
      let end = this.tokens[this.i - 1].end;
      while (this.is('T_NS_DELIMITER') && this.peek().type === 'T_NAME') {
        this.next();
        parts.push(this.next().value);
        end = this.tokens[this.i - 1].end;
      }
      return { start, end, parts, fq, text: (fq ? '\\' : '') + parts.join('\\') };
    }

    // MaskTail : Wildcard (Identifier Wildcard)* Identifier?   (the leading `*` already consumed, or not)
    // Returns the end offset. Accepts an optional leading identifier already consumed by the caller.
    parseMaskTail() {
      let end = this.tokens[this.i - 1].end;
      let expectName = this.is('T_NAME');
      for (;;) {
        if (expectName) {
          if (this.is('T_NAME')) { end = this.next().end; }
          else break;
          expectName = false;
        } else if (this.is('T_ASTERISK')) {
          end = this.next().end;
          expectName = true;
        } else break;
      }
      return end;
    }

    parseNamed() {
      const name = this.parseName();
      const lower = name.text.toLowerCase();

      // Reserved literals
      if (!name.fq && name.parts.length === 1) {
        if (lower === 'true' || lower === 'false') {
          const node = this.node('bool', name.start, name.end, { value: name.text });
          this.feature('literals', node);
          return node;
        }
        if (lower === 'null') {
          const node = this.node('null', name.start, name.end, { value: name.text });
          this.feature('literals', node);
          return node;
        }
      }

      // GlobalConstantMask : Name MaskTail | Name `\` MaskTail
      if (this.is('T_ASTERISK') || (this.is('T_NS_DELIMITER') && this.peek().type === 'T_ASTERISK')) {
        if (this.is('T_NS_DELIMITER')) this.next();
        const end = this.parseMaskTail();
        return this.node('mask', name.start, end, { value: this.source.slice(name.start, end) });
      }

      // ClassConstant / ClassConstantMask : Name `::` ...
      if (this.is('T_DOUBLE_COLON')) {
        this.next();
        if (this.is('T_ASTERISK')) {
          const end = this.parseMaskTail();
          return this.node('classMask', name.start, end, { value: this.source.slice(name.start, end) });
        }
        if (!this.is('T_NAME')) {
          throw this.unexpected('a class constant must carry a name after the double colon, for example "Some::CONST"');
        }
        const id = this.next();
        if (this.is('T_ASTERISK')) {
          const end = this.parseMaskTail();
          return this.node('classMask', name.start, end, { value: this.source.slice(name.start, end) });
        }
        return this.node('const', name.start, id.end, { value: this.source.slice(name.start, id.end), class: name.text, constant: id.value });
      }

      // CallableType : Name TemplateParameters? `(` CallableParameters? `)` CallableReturnType?
      if (this.is('T_ANGLE_BRACKET_OPEN')) {
        const saved = this.i;
        let tparams = null;
        try {
          tparams = this.parseTemplateParameters();
          if (!this.is('T_PARENTHESIS_OPEN')) throw new ParseError('backtrack', 0, 0);
        } catch (e) {
          if (!(e instanceof ParseError)) throw e;
          this.i = saved;
          tparams = null;
        }
        if (tparams) return this.parseCallable(name, tparams);
      }
      if (this.is('T_PARENTHESIS_OPEN')) {
        return this.parseCallable(name, null);
      }

      const node = this.node('named', name.start, name.end, { name: name.text });

      // NamedType : Name (TemplateArguments | ShapeFields)?
      if (this.is('T_ANGLE_BRACKET_OPEN')) {
        const args = this.parseTemplateArguments();
        node.end = args.end;
        node.args = args;
        node.children = args.children;
        this.feature('generics', node);
        for (const a of args.children) if (a.hint) this.feature('hints', node);
      } else if (this.is('T_BRACE_OPEN')) {
        const fields = this.parseShapeFields();
        node.end = fields.end;
        node.fields = fields;
        node.children = fields.children;
        this.feature('shapes', node);
      }
      return node;
    }

    // TemplateArguments : `<` TemplateArgument (`,` TemplateArgument)* `,`? `>`
    parseTemplateArguments() {
      const open = this.expect('T_ANGLE_BRACKET_OPEN');
      const args = [];
      if (this.is('T_ANGLE_BRACKET_CLOSE') || this.is('T_EOI')) {
        throw this.unexpected('an argument list must carry at least one argument');
      }
      for (;;) {
        args.push(this.parseTemplateArgument());
        if (this.accept('T_COMMA')) {
          if (this.is('T_ANGLE_BRACKET_CLOSE')) break; // trailing comma
          continue;
        }
        break;
      }
      const close = this.expect('T_ANGLE_BRACKET_CLOSE', 'an argument list must be closed with a bracket ">"');
      return { start: open.start, end: close.end, children: args };
    }

    // TemplateArgument : TemplateArgumentHint | TemplateArgumentValue
    parseTemplateArgument() {
      const start = this.tok.start;
      let hint = null;
      if (this.is('T_NAME') && this.canStartType(this.peek()) && this.peek().value !== 'is'
          && this.peek().type !== 'T_NS_DELIMITER') {
        hint = this.next().value;
      }
      let value;
      if (this.is('T_ASTERISK')) {
        const t = this.next();
        value = this.node('wildcard', t.start, t.end);
      } else {
        if (!this.canStartType()) throw this.unexpected('an argument list must carry a type as its argument');
        value = hint ? this.parseType() : this.parseLogical();
      }
      return this.node('arg', start, value.end, { hint, value, children: [value], label: hint ? `arg · ${hint}` : 'arg' });
    }

    // ShapeFields : `{` ShapeBody? `,`? `}`
    parseShapeFields() {
      const open = this.expect('T_BRACE_OPEN');
      const fields = [];
      let unsealed = null;
      let explicit = null;
      const seen = new Map();
      this.accept('T_COMMA'); // ShapeFields : `{` ShapeBody? `,`? `}`

      while (!this.is('T_BRACE_CLOSE')) {
        if (this.is('T_EOI')) throw this.unexpected('a shape must be closed with a brace "}"');
        if (this.is('T_ELLIPSIS')) {
          const dots = this.next();
          let end = dots.end, args = null;
          if (this.is('T_ANGLE_BRACKET_OPEN')) { args = this.parseTemplateArguments(); end = args.end; }
          unsealed = this.node('unsealed', dots.start, end, { children: args ? args.children : [], label: '...' });
          this.accept('T_COMMA');
          if (!this.is('T_BRACE_CLOSE')) {
            throw this.unexpected('an unsealed shape marker "..." must be the last field of the shape');
          }
          break;
        }
        const field = this.parseShapeField();
        const isExplicit = field.kind === 'field';
        if (explicit !== null && explicit !== isExplicit) {
          throw this.semantic('Cannot mix explicit and implicit shape keys', field, 'ShapeKeysMixingException');
        }
        explicit = isExplicit;
        if (isExplicit) {
          const key = field.keyText;
          if (seen.has(key)) throw this.semantic(`Duplicate key ${quote(field.keyName)}`, field, 'ShapeFieldDuplicationException');
          seen.set(key, true);
        }
        fields.push(field);
        if (!this.accept('T_COMMA')) {
          if (!this.is('T_BRACE_CLOSE')) throw this.unexpected('a shape must be closed with a brace "}"');
        }
      }
      const close = this.next();
      const children = unsealed ? [...fields, unsealed] : fields;
      return { start: open.start, end: close.end, children, fields, unsealed };
    }

    // ShapeField : ExplicitField | ImplicitField
    parseShapeField() {
      const start = this.tok.start;
      const saved = this.i;
      const key = this.tryParseShapeKey();
      if (key) {
        const optional = !!this.accept('T_NULLABLE');
        if (this.accept('T_COLON')) {
          if (!this.canStartType()) throw this.unexpected('a shape field must carry a type after the colon ":", for example "array{key: T}"');
          const value = this.parseType();
          return this.node('field', start, value.end, {
            key, optional, value, children: [value],
            keyText: key.norm, keyName: key.name, label: optional ? 'field?' : 'field',
          });
        }
        this.i = saved;
      }
      if (!this.canStartType()) throw this.unexpected('a shape field must carry a type, for example "array{T}"');
      const value = this.parseType();
      return this.node('implicit', start, value.end, { value, children: [value], label: 'field' });
    }

    // ShapeKey : Identifier | IntLiteral | StringLiteral | ConstantMask | ClassConstant
    tryParseShapeKey() {
      const t = this.tok;
      if (INT_TOKENS.has(t.type)) { this.next(); return { start: t.start, end: t.end, name: t.value, raw: t.value, norm: 'i:' + t.value }; }
      if (STRING_TOKENS.has(t.type)) { const v = t.value.slice(1, -1); this.next(); return { start: t.start, end: t.end, name: v, raw: t.value, norm: (/^\d+$/.test(v) ? 'i:' : 's:') + v }; }
      if (t.type === 'T_NAME' || t.type === 'T_NS_DELIMITER' || t.type === 'T_ASTERISK') {
        try {
          const saved = this.i;
          if (t.type === 'T_ASTERISK') {
            this.next();
            if (!this.is('T_NAME')) { this.i = saved; return null; }
            const end = this.parseMaskTail();
            const text = this.source.slice(t.start, end); return { start: t.start, end, name: text, raw: text, norm: 'm:' + text };
          }
          const name = this.parseName();
          let end = name.end;
          if (this.is('T_DOUBLE_COLON')) {
            this.next();
            if (this.is('T_NAME')) end = this.next().end;
            if (this.is('T_ASTERISK')) end = this.parseMaskTail();
          } else if (this.is('T_ASTERISK')) {
            end = this.parseMaskTail();
          } else if (name.parts.length > 1 || name.fq) {
            return null;
          }
          const text = this.source.slice(t.start, end);
          return { start: t.start, end, name: text, raw: text, norm: 's:' + text };
        } catch (e) {
          if (!(e instanceof ParseError)) throw e;
          return null;
        }
      }
      return null;
    }

    // TemplateParameters : `<` TemplateParameter (`,` TemplateParameter)* `,`? `>`
    parseTemplateParameters() {
      const open = this.expect('T_ANGLE_BRACKET_OPEN');
      const params = [];
      if (this.is('T_ANGLE_BRACKET_CLOSE')) throw this.unexpected('a template parameter list must carry at least one parameter');
      for (;;) {
        params.push(this.parseTemplateParameter());
        if (this.accept('T_COMMA')) {
          if (this.is('T_ANGLE_BRACKET_CLOSE')) break;
          continue;
        }
        break;
      }
      const close = this.expect('T_ANGLE_BRACKET_CLOSE', 'a template parameter list must be closed with a bracket ">"');
      return { start: open.start, end: close.end, children: params };
    }

    // TemplateParameter : Identifier TemplateBound* TemplateDefault?
    parseTemplateParameter() {
      const id = this.expect('T_NAME', 'a template parameter must carry a name');
      const node = this.node('tparam', id.start, id.end, { name: id.value, upper: null, lower: null, default: null });
      for (;;) {
        if (this.isName('of') || this.isName('as')) {
          const kw = this.next();
          node.upperKeyword = kw.value;
          if (node.upper) throw this.semantic('Template parameter cannot have more than one upper bound', { start: kw.start, end: kw.end }, 'TemplateBoundException');
          node.upper = this.parseType();
          node.children.push(node.upper);
          node.end = node.upper.end;
        } else if (this.isName('super')) {
          const kw = this.next();
          if (node.lower) throw this.semantic('Template parameter cannot have more than one lower bound', { start: kw.start, end: kw.end }, 'TemplateBoundException');
          node.lower = this.parseType();
          node.children.push(node.lower);
          node.end = node.lower.end;
        } else break;
      }
      if (this.accept('T_ASSIGN')) {
        node.default = this.parseType();
        node.children.push(node.default);
        node.end = node.default.end;
      }
      return node;
    }

    // CallableType : Name TemplateParameters? `(` CallableParameters? `)` CallableReturnType?
    parseCallable(name, tparams) {
      this.expect('T_PARENTHESIS_OPEN');
      const params = [];
      while (!this.is('T_PARENTHESIS_CLOSE')) {
        if (!this.canStartType()) {
          throw this.unexpected('a parameter list must be closed with a bracket ")"');
        }
        params.push(this.parseCallableParameter());
        if (!this.accept('T_COMMA')) break;
      }
      const close = this.expect('T_PARENTHESIS_CLOSE', 'a parameter list must be closed with a bracket ")"');
      let ret = null, end = close.end;
      if (this.accept('T_COLON')) {
        if (!this.canStartType()) throw this.unexpected('a callable must carry a return type after the colon ":", for example "callable(): T"');
        ret = this.parseType();
        end = ret.end;
      }
      const children = [...(tparams ? tparams.children : []), ...params, ...(ret ? [ret] : [])];
      const node = this.node('callable', name.start, end, { name: name.text, tparams, params, returns: ret, children });
      this.feature('callables', node);
      if (tparams) this.feature('generics', node, 'templateParameters');
      return node;
    }

    // CallableParameter : Type `&`? `...`? Variable? `=`?
    parseCallableParameter() {
      const type = this.parseType();
      const node = this.node('param', type.start, type.end, { type, children: [type], byRef: false, variadic: false, name: null, optional: false });
      let t;
      if ((t = this.accept('T_AMP'))) { node.byRef = true; node.end = t.end; }
      if ((t = this.accept('T_ELLIPSIS'))) { node.variadic = true; node.end = t.end; }
      if (this.is('T_VARIABLE')) { t = this.next(); node.name = t.value; node.end = t.end; }
      if ((t = this.accept('T_ASSIGN'))) {
        node.optional = true; node.end = t.end;
        if (node.variadic) throw this.semantic('Cannot have variadic param with a default', node, 'VariadicWithDefaultException');
      }
      return node;
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

    const wrap = (child, parentKind) => {
      const s = go(child, 0);
      const needs = (child.kind === 'union' || child.kind === 'intersection' || child.kind === 'cond')
        && (parentKind === 'nullable' || parentKind === 'list' || parentKind === 'offset' || parentKind === 'intersection'
          || (parentKind === 'union' && child.kind === 'cond'));
      return needs ? `(${s})` : s;
    };

    const go = (n, depth) => {
      const pad = indent.repeat(depth);
      switch (n.kind) {
        case 'named': {
          let s = n.name;
          if (n.args) s += `<${n.args.children.map(a => go(a, depth)).join(', ')}>`;
          if (n.fields) {
            const items = n.fields.children.map(f => go(f, depth + 1));
            if (items.length === 0) s += '{}';
            else if (items.length === 1) s += `{${items[0]}}`;
            else s += `{${nl}${items.map(i => pad + indent + i).join(',' + nl)}${nl}${pad}}`;
          }
          return s;
        }
        case 'arg': return (n.hint ? n.hint + ' ' : '') + go(n.value, depth);
        case 'wildcard': return '*';
        case 'field': return `${n.key.raw}${n.optional ? '?' : ''}: ${go(n.value, depth)}`;
        case 'implicit': return go(n.value, depth);
        case 'unsealed': return '...' + (n.children.length ? `<${n.children.map(a => go(a, depth)).join(', ')}>` : '');
        case 'union': return n.children.map(c => wrap(c, 'union')).join('|');
        case 'intersection': return n.children.map(c => wrap(c, 'intersection')).join('&');
        case 'nullable': return '?' + wrap(n.children[0], 'nullable');
        case 'list': return wrap(n.children[0], 'list') + '[]';
        case 'offset': return wrap(n.subject, 'offset') + '[' + go(n.key, depth) + ']';
        case 'int': case 'float': case 'string': case 'bool': case 'null': case 'const': case 'classMask': case 'mask':
          return n.value;
        case 'this': return '$this';
        case 'var': return n.name;
        case 'cond':
          return `${go(n.subject, depth)} ${n.negated ? 'is not' : 'is'} ${go(n.operand, depth)} ? ${go(n.then, depth)} : ${go(n.else, depth)}`;
        case 'callable': {
          let s = n.name;
          if (n.tparams) s += `<${n.tparams.children.map(p => go(p, depth)).join(', ')}>`;
          s += `(${n.params.map(p => go(p, depth)).join(', ')})`;
          if (n.returns) s += `: ${go(n.returns, depth)}`;
          return s;
        }
        case 'tparam': {
          let s = n.name;
          if (n.upper) s += ` ${n.upperKeyword} ${go(n.upper, depth)}`;
          if (n.lower) s += ` super ${go(n.lower, depth)}`;
          if (n.default) s += ` = ${go(n.default, depth)}`;
          return s;
        }
        case 'param': {
          let s = go(n.type, depth);
          if (n.byRef) s += ' &';
          if (n.variadic) s += (n.byRef ? '' : ' ') + '...';
          if (n.name) s += (n.byRef || n.variadic ? '' : ' ') + n.name;
          if (n.optional) s += '=';
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
