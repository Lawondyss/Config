<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\Config;

abstract class Config implements \ArrayAccess, \Countable, \IteratorAggregate
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
      } elseif (is_array($value) && count($value) === count(array_filter($value, function($item) {
            return $item instanceof self;
          }))) {
        $value = array_map(function(Config $item) {
          return $item->toArray();
        }, $value);
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
    foreach ($options as $option => $value) {
      $dolly->$option = $value;
    }

    return $dolly;
  }


  public function offsetExists($offset)
  {
    return property_exists(static::class, $offset) && $this->$offset !== null;
  }


  public function offsetGet($offset)
  {
    return $this->$offset;
  }


  public function offsetSet($offset, $value)
  {
    $this->$offset = $value;
  }


  public function offsetUnset($offset)
  {
    $this->$offset = null;
  }


  public function count()
  {
    return count($this->getProperties());
  }


  public function getIterator()
  {
    return new \RecursiveArrayIterator($this->toArray());
  }


  public function __get($name)
  {
    $this->assertOptionDefined($name);
  }


  public function __set($name, $value)
  {
    $this->assertOptionDefined($name);
  }


  private function assertOptionDefined(string $optionName): void
  {
    if (!property_exists(static::class, $optionName)) {
      throw UndefinedOption::create(static::class, $optionName);
    }
  }


  /**
   * @return string[]
   */
  private function getProperties(): array
  {
    static $cachedProperties = null;

    if (!isset($cachedProperties)) {
      $cachedProperties = [];

      $rf = new \ReflectionObject($this);
      foreach ($rf->getProperties() as $property) {
        $cachedProperties[] = $property->name;
      }
    }

    return $cachedProperties;
  }
}
