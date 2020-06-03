<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\Config;

interface Exception
{
}


class UndefinedOption extends \InvalidArgumentException implements Exception
{
  public static function create(string $configuratorName, string $optionName): self
  {
    return new self(sprintf('Option %s::$%s is not defined', $configuratorName, $optionName));
  }
}
