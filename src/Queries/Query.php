<?php
declare(strict_types = 1);

namespace QueryBuilder\Queries;

/**
 * Interface Query
 * 
 * @package QueryBuilder\Queries
 */
interface Query
{
    /**
     * Method __toString
     * 
     * @return string 
     */
    public function __toString(): string;
}
