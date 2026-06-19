# Date/Time (Clock)

After metadata building, each DTO stores a datetime snapshot with the DTO 
build time to prevent reading stale data (this can be used, for example, 
in cache implementations).

To ensure testing, the time generator can be passed explicitly using 
any [PSR-20 library](https://www.php-fig.org/psr/psr-20/).

Here are the [most popular](https://packagist.org/providers/psr/clock-implementation) 
implementations:
- [nesbot/carbon](https://packagist.org/packages/nesbot/carbon)
- [symfony/clock](https://packagist.org/packages/symfony/clock)
- [lcobucci/clock](https://packagist.org/packages/lcobucci/clock)
- [beste/clock](https://packagist.org/packages/beste/clock)
- [cakephp/chronos](https://packagist.org/packages/cakephp/chronos)
- ...and [many other](https://packagist.org/providers/psr/clock-implementation)

To create a time provider, you can install any implementation, for example:
```shell
composer require symfony/clock
```

Or write your own:
```php
final MySimpleClock implements [[[Psr\Clock\ClockInterface|https://www.php-fig.org/psr/psr-20/]]]
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
```

Next, in case for the datetime to be "applied" to the metadata, the 
implementation must be passed to the 
[`MetadataBuilder` metadata provider](meta-provider.md).

```php
use TypeLang\Mapper\Mapping\Provider\MetadataBuilder;

$provider = [[[new MetadataBuilder(|meta-provider.md]]]
    clock: new MySimpleClock(),
);
```
