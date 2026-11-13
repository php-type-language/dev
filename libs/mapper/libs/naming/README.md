# Naming

Converts property names into keys in camelCase, PascalCase, snake_case,
SCREAMING_SNAKE_CASE or kebab-case.

```php
use TypeLang\Mapper\Naming\NamingStrategy;

echo NamingStrategy::SnakeCase->rename('getHTTPResponseCode');
// get_http_response_code
```
