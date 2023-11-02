<?php
declare(strict_types = 1);

namespace QueryBuilder\Queries\Traits;

/**
 * Trait Order
 * 
 * @package QueryBuilder\Queries\Traits
 */
trait Order
{
    const REGEX_SQL_ORDER = "/^\w+(?:\s(?:ASC|DESC))?$/";

    /**
     * Method isOrder
     * 
     * @param string $string 
     * @return bool 
     */
    public static function isOrder(string $string): bool
    {
        return (bool) preg_match(self::REGEX_SQL_ORDER, $string);
    }
}
