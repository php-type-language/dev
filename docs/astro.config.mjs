// @ts-check
import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';
import { readFileSync } from 'node:fs';

const codeTheme = JSON.parse(readFileSync(new URL('./src/styles/code-theme.json', import.meta.url), 'utf8'));

export default defineConfig({
    site: 'https://typelang.dev',
    // Keeps the spaces between inline text and the tags on the next line.
    compressHTML: false,
    integrations: [
        starlight({
            title: 'TypeLang',
            logo: { src: './src/assets/logo.svg' },
            social: [
                { icon: 'github', label: 'GitHub', href: 'https://github.com/php-type-language' },
            ],
            customCss: ['./src/styles/tokens.css', './src/styles/docs.css'],
            components: {
                Header: './src/components/starlight/Header.astro',
                ThemeProvider: './src/components/starlight/ThemeProvider.astro',
                ThemeSelect: './src/components/starlight/ThemeSelect.astro',
                PageTitle: './src/components/starlight/PageTitle.astro',
            },
            expressiveCode: {
                themes: [codeTheme],
                // A plain frame for every block, shell ones included: no window buttons.
                defaultProps: { frame: 'code' },
                styleOverrides: {
                    borderRadius: 'var(--radius)',
                    borderColor: 'var(--line)',
                    codeFontFamily: 'var(--mono)',
                    uiFontFamily: 'var(--sans)',
                    codeBackground: 'var(--paper-2)',
                    frames: {
                        editorTabBarBackground: 'var(--paper-2)',
                        editorActiveTabBackground: 'var(--paper-2)',
                        editorActiveTabIndicatorTopColor: 'var(--accent)',
                        terminalBackground: 'var(--paper-2)',
                        terminalTitlebarBackground: 'var(--paper-3)',
                        frameBoxShadowCssValue: 'none',
                    },
                },
            },
            // One group per section, src/routeData.ts shows the group of the current page only.
            routeMiddleware: './src/routeData.ts',
            sidebar: [
                {
                    label: 'Language',
                    items: [
                        { label: 'Overview', items: [
                            { slug: 'language', label: 'Introduction' },
                            { slug: 'language/comparison' },
                        ] },
                        { label: 'Syntax', items: [{ autogenerate: { directory: 'language/syntax' } }] },
                    ],
                },
                {
                    label: 'Toolkit',
                    items: [
                        { label: 'Overview', items: [
                            { slug: 'toolkit', label: 'Getting started' },
                            { slug: 'toolkit/ast' },
                        ] },
                        { label: 'Parser', items: [{ autogenerate: { directory: 'toolkit/parser' } }] },
                        { label: 'Printer', items: [{ autogenerate: { directory: 'toolkit/printer' } }] },
                        { label: 'Reader', items: [{ slug: 'toolkit/reader', label: 'Reading types' }] },
                    ],
                },
                {
                    label: 'Mapper',
                    items: [
                        { label: 'Overview', items: [
                            { slug: 'mapper', label: 'Getting started' },
                            { slug: 'mapper/configuration' },
                            { slug: 'mapper/comparison' },
                        ] },
                        { label: 'Metadata', items: [{ autogenerate: { directory: 'mapper/metadata' } }] },
                        { label: 'Types', items: [
                            { slug: 'mapper/types' },
                            { slug: 'mapper/coercion' },
                            { slug: 'mapper/errors' },
                        ] },
                        { label: 'Extending', items: [{ autogenerate: { directory: 'mapper/extending' } }] },
                        { label: 'Integrations', items: [{ autogenerate: { directory: 'mapper/integrations' } }] },
                    ],
                },
                {
                    label: 'PHPDoc',
                    items: [
                        { label: 'Overview', items: [
                            { slug: 'phpdoc', label: 'Getting started' },
                            { slug: 'phpdoc/reading' },
                            { slug: 'phpdoc/reflection' },
                        ] },
                        { label: 'Platforms', items: [{ autogenerate: { directory: 'phpdoc/platforms' } }] },
                        { label: 'Extending', items: [{ autogenerate: { directory: 'phpdoc/extending' } }] },
                        { label: 'Tag reference', items: [{ autogenerate: { directory: 'phpdoc/tags' } }] },
                    ],
                },
            ],
        }),
    ],
});
