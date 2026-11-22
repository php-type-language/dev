/*
 * Sections of the documentation, shown in the header of the landing and of
 * every documentation page.
 */
export interface Section {
    label: string;
    href: string;
    summary: string;
}

export const sections: Section[] = [
    {
        label: 'Language',
        href: '/language/',
        summary: 'The syntax of the types, how it reads and how other tools read it.',
    },
    {
        label: 'Toolkit',
        href: '/toolkit/',
        summary: 'Parse a type into an AST, print it back, read it from Reflection.',
    },
    {
        label: 'Mapper',
        href: '/mapper/',
        summary: 'Map raw data to objects and back by the types you declare.',
    },
    {
        label: 'PHPDoc',
        href: '/phpdoc/',
        summary: 'Read doc-comments into a description and typed tags.',
    },
];

export const links = [
    { label: 'Spec', href: 'https://typelang.dev/static/spec.html' },
    { label: 'GitHub', href: 'https://github.com/php-type-language' },
];
