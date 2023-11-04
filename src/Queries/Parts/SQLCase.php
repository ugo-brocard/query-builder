<?php
declare(strict_types = 1);

namespace QueryBuilder\Queries\Parts;

/**
 * Class SQLCase
 * 
 * @package QueryBuilder\Queries\Parts
 */
class SQLCase
{
    /**
     * Variable alias
     * 
     * @var string
     */
    protected string $alias;

    /**
     * Method as
     * 
     * @param string $alias 
     * @return SQLCase 
     */
    public function as(string $alias): self
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * Method getStatement
     * 
     * @return string 
     */
    public function getStatement(): string
    {
        $statement = "CASE";
        
        $statement .= "END";
        if ($this->alias) {
            $statement .= "AS {$this->alias}";
        }

        return $statement;
    }
}
