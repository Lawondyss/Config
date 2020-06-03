<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

require_once __DIR__ . '/bootstrap.php';

use Lawondyss\Config\Config;
use Lawondyss\Config\UndefinedOption;
use Tester\Assert;

test(function() {
  $config = new class extends Config {
    public $foo = 'lipsum';

    public $bar = 1980;

    public $baz;
  };

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
              'foo' => 'lipsum',
              'bar' => 1980,
              'baz' => null,
          ],
      ],
      $config::fromArray(['baz' => $config])->toArray(),
      'Subarray does not match the object'
  );
  Assert::notSame($config, $config->withOptions([]), 'Additional options not create new object');
  Assert::same(3, $config->count(), 'Config miscalculated the options');
  Assert::same('foobar', $config::fromArray(['baz' => 'foobar'])->baz, 'Property does not match the option');
  Assert::same('foobar', $config::fromArray(['baz' => 'foobar'])['baz'], 'Array-key does not match the option');
});

test(function() {
  Assert::exception(function() {
    $config = new class extends Config {};
    $config->foo;
  }, UndefinedOption::class);
  Assert::exception(function() {
    $config = new class extends Config {};
    $config['foo'];
  }, UndefinedOption::class);
});
