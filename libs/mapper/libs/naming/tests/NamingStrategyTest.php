<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Naming\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Mapper\Naming\NamingStrategy;

final class NamingStrategyTest extends TestCase
{
    /**
     * Expected keys of each property name in the order of
     * {@see NamingStrategy::cases()}.
     *
     * @var array<non-empty-string, list<non-empty-string>>
     */
    private const KEYS = [
        'name' => ['name', 'Name', 'name', 'NAME', 'name'],
        'firstName' => ['firstName', 'FirstName', 'first_name', 'FIRST_NAME', 'first-name'],
        'FirstName' => ['firstName', 'FirstName', 'first_name', 'FIRST_NAME', 'first-name'],
        'userID' => ['userId', 'UserId', 'user_id', 'USER_ID', 'user-id'],
        'HTTPServer' => ['httpServer', 'HttpServer', 'http_server', 'HTTP_SERVER', 'http-server'],
        'getHTTPResponseCode' => [
            'getHttpResponseCode',
            'GetHttpResponseCode',
            'get_http_response_code',
            'GET_HTTP_RESPONSE_CODE',
            'get-http-response-code',
        ],
        'XMLHttpRequest' => [
            'xmlHttpRequest',
            'XmlHttpRequest',
            'xml_http_request',
            'XML_HTTP_REQUEST',
            'xml-http-request',
        ],
        'IOStream' => ['ioStream', 'IoStream', 'io_stream', 'IO_STREAM', 'io-stream'],
        'ID' => ['id', 'Id', 'id', 'ID', 'id'],
        'address2' => ['address2', 'Address2', 'address2', 'ADDRESS2', 'address2'],
        'address_2' => ['address2', 'Address2', 'address_2', 'ADDRESS_2', 'address-2'],
        'utf8Encode' => ['utf8Encode', 'Utf8Encode', 'utf8_encode', 'UTF8_ENCODE', 'utf8-encode'],
        'HTTP2Server' => ['http2Server', 'Http2Server', 'http2_server', 'HTTP2_SERVER', 'http2-server'],
        'render3dModel' => ['render3dModel', 'Render3dModel', 'render3d_model', 'RENDER3D_MODEL', 'render3d-model'],
        'x1Y2' => ['x1Y2', 'X1Y2', 'x1_y2', 'X1_Y2', 'x1-y2'],
        'already_snake' => ['alreadySnake', 'AlreadySnake', 'already_snake', 'ALREADY_SNAKE', 'already-snake'],
        'kebab-case' => ['kebabCase', 'KebabCase', 'kebab_case', 'KEBAB_CASE', 'kebab-case'],
        'SCREAMING_SNAKE' => [
            'screamingSnake',
            'ScreamingSnake',
            'screaming_snake',
            'SCREAMING_SNAKE',
            'screaming-snake',
        ],
        'Content-Type' => ['contentType', 'ContentType', 'content_type', 'CONTENT_TYPE', 'content-type'],
        '__private__name_' => ['privateName', 'PrivateName', 'private_name', 'PRIVATE_NAME', 'private-name'],
        'a' => ['a', 'A', 'a', 'A', 'a'],
        'A' => ['a', 'A', 'a', 'A', 'a'],
        'aB' => ['aB', 'AB', 'a_b', 'A_B', 'a-b'],
        '_' => ['_', '_', '_', '_', '_'],
        '_-.' => ['_-.', '_-.', '_-.', '_-.', '_-.'],
        'listURLs' => ['listUrLs', 'ListUrLs', 'list_ur_ls', 'LIST_UR_LS', 'list-ur-ls'],
        'größe' => ['größe', 'Größe', 'größe', 'GRößE', 'größe'],
    ];

    /**
     * Names whose {@see NamingStrategy::ScreamingSnakeCase} key has a new
     * word boundary after a digit or a non-ASCII letter.
     *
     * @var list<non-empty-string>
     */
    private const UNSTABLE_SCREAMING_NAMES = ['render3dModel', 'größe'];

    /**
     * @return iterable<non-empty-string, array{
     *     NamingStrategy,
     *     non-empty-string,
     *     non-empty-string,
     * }>
     */
    public static function renameDataProvider(): iterable
    {
        foreach (self::KEYS as $name => $keys) {
            foreach (NamingStrategy::cases() as $index => $strategy) {
                yield $strategy->value . ': ' . $name => [$strategy, $name, $keys[$index]];
            }
        }
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $expected
     */
    #[DataProvider('renameDataProvider')]
    public function testRenamesPropertyIntoKey(NamingStrategy $strategy, string $name, string $expected): void
    {
        self::assertSame($expected, $strategy->rename($name));
    }

    /**
     * @return iterable<non-empty-string, array{
     *     NamingStrategy,
     *     non-empty-string,
     * }>
     */
    public static function separatedStrategyDataProvider(): iterable
    {
        $strategies = [
            NamingStrategy::SnakeCase,
            NamingStrategy::ScreamingSnakeCase,
            NamingStrategy::KebabCase,
        ];

        foreach (\array_keys(self::KEYS) as $name) {
            foreach ($strategies as $strategy) {
                $isUnstable = $strategy === NamingStrategy::ScreamingSnakeCase
                    && \in_array($name, self::UNSTABLE_SCREAMING_NAMES, true);

                if ($isUnstable) {
                    continue;
                }

                yield $strategy->value . ': ' . $name => [$strategy, $name];
            }
        }
    }

    /**
     * @param non-empty-string $name
     */
    #[DataProvider('separatedStrategyDataProvider')]
    public function testSeparatedStrategyIsIdempotent(NamingStrategy $strategy, string $name): void
    {
        $key = $strategy->rename($name);

        self::assertSame($key, $strategy->rename($key));
    }

    /**
     * @return iterable<non-empty-string, array{
     *     non-empty-string,
     *     NamingStrategy,
     * }>
     */
    public static function valueDataProvider(): iterable
    {
        yield 'camelCase' => ['camelCase', NamingStrategy::CamelCase];
        yield 'PascalCase' => ['PascalCase', NamingStrategy::PascalCase];
        yield 'snake_case' => ['snake_case', NamingStrategy::SnakeCase];
        yield 'SCREAMING_SNAKE_CASE' => ['SCREAMING_SNAKE_CASE', NamingStrategy::ScreamingSnakeCase];
        yield 'kebab-case' => ['kebab-case', NamingStrategy::KebabCase];
    }

    /**
     * @param non-empty-string $value
     */
    #[DataProvider('valueDataProvider')]
    public function testCreatesStrategyFromStyleName(string $value, NamingStrategy $expected): void
    {
        self::assertSame($expected, NamingStrategy::from($value));
    }

    public function testIgnoresLocale(): void
    {
        $previous = \setlocale(\LC_CTYPE, '0');

        if (\setlocale(\LC_CTYPE, 'tr_TR.ISO-8859-9', 'tr_TR', 'Turkish') === false) {
            self::markTestSkipped('Turkish locale is not available');
        }

        try {
            self::assertSame('id', NamingStrategy::SnakeCase->rename('ID'));
            self::assertSame('ID', NamingStrategy::ScreamingSnakeCase->rename('id'));
            self::assertSame('Id', NamingStrategy::PascalCase->rename('id'));
        } finally {
            \setlocale(\LC_CTYPE, (string) $previous);
        }
    }
}
