<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

require_once __DIR__ . '/bootstrap.php';

use Lawondyss\Config\Config;
use Lawondyss\Config\UndefinedOption;
use Tester\Assert;

class FooConfig extends Config
{
  public string $foo = 'lipsum';

  public int $bar = 1980;

  public ?BazConfig $baz = null;
}

class BazConfig extends Config
{
  public int $lorem;

  public string $ipsum;
}


test('Defined', function (): void {
  $config = new FooConfig;

  Assert::same('lipsum', $config->foo, 'Foo does not match');
  Assert::same(1980, $config->bar, 'Bar does not match');
  Assert::null($config->baz, 'Baz does not match');

  Assert::same(
    [
      'foo' => 'lipsum',
      'bar' => 1980,
      'baz' => null,
    ],
    $config->toArray(),
    'Array does not match the object'
  );
  Assert::same(
    [
      'foo' => 'lipsum',
      'bar' => 1980,
      'baz' => [
        'lorem' => 128,
        'ipsum' => 'IPS',
      ],
    ],
    FooConfig::fromArray(['baz' => BazConfig::fromArray(['lorem' => 128, 'ipsum' => 'IPS'])])->toArray(),
    'Subarray does not match the object'
  );
  Assert::notSame($config, $config->withOptions([]), 'Additional options not create new object');
  Assert::same(3, $config->count(), 'Config miscalculated the options');
  Assert::same(2000, FooConfig::fromArray(['bar' => 2000])->bar, 'Property does not match the option');
  Assert::same(2000, FooConfig::fromArray(['bar' => 2000])['bar'], 'Array-key does not match the option');

});

test('Undefined', function (): void {
  $config = new FooConfig;
  Assert::exception(fn() => $config->lipsum, UndefinedOption::class, 'Option FooConfig::$lipsum is not defined');
  Assert::exception(fn() => $config['lipsum'], UndefinedOption::class, 'Option FooConfig::$lipsum is not defined');

  // Test setting undeclared property via fromArray
  Assert::exception(fn() => FooConfig::fromArray(['undeclaredRandomProperty' => 'testValue']), UndefinedOption::class, 'Option FooConfig::$undeclaredRandomProperty is not defined');

  // Test setting undeclared property via withOptions
  Assert::exception(fn() => $config->withOptions(['anotherUndeclaredProperty' => 'testValue2']), UndefinedOption::class, 'Option FooConfig::$anotherUndeclaredProperty is not defined');

  // Test setting undeclared property via array access
  Assert::exception(fn() => $config['yetAnotherUndeclared'] = 'testValue3', UndefinedOption::class, 'Option FooConfig::$yetAnotherUndeclared is not defined');

  // Test setting undeclared property via direct assignment
  Assert::exception(fn() => $config->directUndeclaredSet = 'testValue4', UndefinedOption::class, 'Option FooConfig::$directUndeclaredSet is not defined');
});

test('Suggestion', function (): void {
  $config = new FooConfig;
  Assert::exception(fn() => $config->boo, UndefinedOption::class, 'Option FooConfig::$boo is not defined, do you mean "foo"?');
  Assert::exception(fn() => $config['boo'], UndefinedOption::class, 'Option FooConfig::$boo is not defined, do you mean "foo"?');
});
