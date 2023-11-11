<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\Config;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use RecursiveArrayIterator;
use ReflectionObject;
use ReflectionProperty;
use function array_map;
use function count;
use function is_array;
use function property_exists;

abstract class Config implements ArrayAccess, Countable, IteratorAggregate
{
  /**
   * @param array<string, mixed> $options
   * @return static
   */
  public static function fromArray(array $options): self
  {
    $config = new static;

    foreach ($options as $name => $value) {
      $config->$name = $value;
    }

    return $config;
  }


  /**
   * @return array<string, mixed>
   */
  public function toArray(): array
  {
    $arr = [];
    $properties = $this->getProperties();

    foreach ($properties as $name) {
      $value = $this->$name;

      if ($value instanceof self) {
        $value = $value->toArray();
      } elseif (is_array($value)) {
        $value = array_map(fn(mixed $val) => $val instanceof Config ? $val->toArray() : $val, $value);
      }

      $arr[$name] = $value;
    }

    return $arr;
  }


  /**
   * @param array<string, mixed> $options
   * @return Config
   */
  public function withOptions(array $options): Config
  {
    $dolly = clone $this;

    foreach ($options as $name => $value) {
      $dolly->$name = $value;
    }

    return $dolly;
  }


  public function offsetExists($offset): bool
  {
    return property_exists($this::class, $offset) && $this->$offset !== null;
  }


  public function offsetGet($offset): mixed
  {
    return $this->$offset;
  }


  public function offsetSet($offset, $value): void
  {
    $this->$offset = $value;
  }


  public function offsetUnset($offset): void
  {
    $this->$offset = null;
  }


  public function count(): int
  {
    return count($this->getProperties());
  }


  public function getIterator(): RecursiveArrayIterator
  {
    return new RecursiveArrayIterator($this->toArray());
  }


  public function __get($name)
  {
    throw UndefinedOption::create($this::class, $name, $this->getProperties());
  }


  public function __set($name, $value)
  {
    throw UndefinedOption::create($this::class, $name, $this->getProperties());
  }


  /**
   * @return string[]
   */
  private function getProperties(): array
  {
    static $cachedProperties = [];

    return $cachedProperties[$this::class] ??= (fn(): array  => array_map(
      fn(ReflectionProperty $rp): string => $rp->name,
      (new ReflectionObject($this))->getProperties(~ReflectionProperty::IS_STATIC)
    ))();
  }
}
