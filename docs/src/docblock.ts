/*
 * HTML of the text of a PHP docblock: paragraphs, lists, inline code and
 * fenced code blocks, the markdown subset the docblocks of the project use.
 */

function escape(text: string): string {
    return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function renderInline(text: string): string {
    return escape(text)
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
}

function renderBlock(block: string): string {
    const lines = block.split('\n');

    if (lines.every((line) => /^\s*[-*] /.test(line) || /^\s{2,}\S/.test(line))) {
        const items: string[] = [];

        for (const line of lines) {
            if (/^\s*[-*] /.test(line)) {
                items.push(line.replace(/^\s*[-*] /, ''));
            } else {
                items[items.length - 1] += ' ' + line.trim();
            }
        }

        return `<ul>${items.map((item) => `<li>${renderInline(item)}</li>`).join('')}</ul>`;
    }

    return `<p>${renderInline(lines.join(' '))}</p>`;
}

export function renderDocBlock(text: string): string {
    const parts = text.split(/^```[a-z]*\n([\s\S]*?)\n```$/m);
    let html = '';

    parts.forEach((part, index) => {
        // Odd parts are the contents of the fences.
        if (index % 2 === 1) {
            html += `<pre class="doc-code"><code>${escape(part)}</code></pre>`;

            return;
        }

        for (const block of part.split(/\n\s*\n/)) {
            if (block.trim() !== '') {
                html += renderBlock(block.trim());
            }
        }
    });

    return html;
}
