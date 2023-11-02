<?php
declare(strict_types = 1);

namespace QueryBuilder\Queries\Traits;

/**
 * Trait Alias
 * 
 * @package QueryBuilder\Queries\Traits
 */
trait Alias
{
    const REGEX_SQL_ALIAS = "/^\w+\sAS\s\w+\b$/";

    public static function isAlias(string $string): bool
    {
        return (bool) preg_match(self::REGEX_SQL_ALIAS, $string);
    }
}
