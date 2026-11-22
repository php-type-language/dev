/*
 * Addresses of the phpdoc tag pages.
 */

/**
 * Page of the tag: `codingStandardsIgnoreEnd` is described by
 * `/phpdoc/tags/coding-standards-ignore-end/`.
 */
export function getTagHref(name: string): string {
    const slug = name
        .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
        .replace(/[^A-Za-z0-9]+/g, '-')
        .toLowerCase();

    return `/phpdoc/tags/${slug}/`;
}
