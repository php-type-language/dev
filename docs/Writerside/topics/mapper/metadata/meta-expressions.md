# Expressions

Some mapping rules may involve [complex imperative expressions](https://symfony.com/doc/current/components/expression_language.html). 
For example, when using an [attribute reader](meta-reader-attribute.md), you 
could define a rule like this:

```php
use TypeLang\Mapper\Mapping\SkipWhen;

final class UserInfoResponse
{
    public ?string $firstName;
    public ?string $lastName;

    // Skip normalization of this field if
    // one of the [firstName, lastName] is not specified
    #[SkipWhen('this.firstName == null or this.lastName == null')]
    public string $fullName {
        get => $this->firstName . ' ' . $this->lastName;
    }
}
```

To enable support for such rules, an additional dependency must be defined.

```shell
composer require symfony/expression-language
```

By default, you don't need to do anything else to support the expression 
language, but in some cases you may need to 
[extend it](https://symfony.com/doc/current/components/expression_language.html#extending-the-expressionlanguage). 
To do this, you must explicitly pass the expression language instance to the 
[`MetadataBuilder` metadata provider](meta-provider.md).

```php
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use TypeLang\Mapper\Mapping\Provider\MetadataBuilder;

$expression = new ExpressionLanguage();

// ...

$provider = [[[new MetadataBuilder(|meta-provider.md]]]
    expression: $expression,
);
```
