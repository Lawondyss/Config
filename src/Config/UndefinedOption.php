<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\Config;

use InvalidArgumentException;
use function levenshtein;
use function sprintf;
use function strlen;

class UndefinedOption extends InvalidArgumentException
{
  /**
   * @param string[] $possibilities
   */
  public static function create(string $configuratorName, string $optionName, array $possibilities): self
  {
    $message = sprintf('Option %s::$%s is not defined', $configuratorName, $optionName);
    $suggestion = self::getSuggestion($possibilities, $optionName);

    if (isset($suggestion)) {
      $message .= sprintf(', do you mean "%s"?', $suggestion);
    }

    return new self($message);
  }


  /**
   * @param string[] $possibilities
   */
  private static function getSuggestion(array $possibilities, string $name): ?string
  {
    $best = null;
    $min = (strlen($name) / 4 + 1) * 10 + .1;

    foreach ($possibilities as $item) {
      $lev = levenshtein($item, $name, 10, 11, 10);

      if ($lev <= 0 || $lev >= $min) {
          continue;
      }

      $min = $lev;
      $best = $item;
    }

    return $best;
  }
}
