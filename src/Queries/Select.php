<?php
declare(strict_types = 1);

namespace QueryBuilder\Queries;

use QueryBuilder\Queries\Parts\SQLCondition;
use QueryBuilder\Queries\Traits\{Alias, Order};

/**
 * Class Select (Query)
 * 
 * @package QueryBuilder\Queries
 */
class Select implements Query
{
    use Alias, Order;

    /**
     * Variable where
     * 
     * @var array
     */
    protected array $where   = [];

    /**
     * Variable having
     * 
     * @var array
     */
    protected array $having  = [];

    /**
     * Variable groupBy
     * 
     * @var array
     */
    protected array $groupBy = [];

    /**
     * Variable orderBy
     * 
     * @var array
     */
    protected array $orderBy = [];

    /**
     * Variable limit
     * 
     * @var int
     */
    protected int $limit;
    
    /**
     * Variable offset
     * 
     * @var int
     */
    protected int $offset;

    /**
     * Method where
     * 
     * @param SQLCondition $conditions 
     * @return Select 
     */
    public function where(SQLCondition ...$conditions): self
    {
        foreach ($conditions as $condition) {
            $where = array($condition->getStatement() => $condition->getValues());
            $this->where = array_merge($this->where, $where);
        }

        return $this;
    }

    /**
     * Method having
     * 
     * @param SQLCondition $conditions
     * @return Select 
     */
    public function having(SQLCondition ...$conditions): self
    {
        foreach ($conditions as $condition) {
            $having = array($condition->getStatement() => $condition->getValues());
            $this->having = array_merge($this->having, $having);
        }

        return $this;
    }

    /**
     * Method groupBy
     * 
     * @param string $columns 
     * @return Select 
     */
    public function groupBy(string ...$columns): self
    {
        $this->groupBy = array_merge($this->groupBy, $columns);
        return $this;
    }

    /**
     * Method orderBy
     * 
     * @param string $columns 
     * @return Select 
     */
    public function orderBy(string ...$columns): self
    {
        foreach ($columns as $column) {
            if (!self::isOrder($column)) {
                continue;
            }
            
            $this->orderBy[] = $column;
        } 

        return $this;
    }

    /**
     * Method limit
     * 
     * @param int $limit 
     * @return Select 
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Method offset
     * 
     * @param int $offset 
     * @return Select 
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Method __toString
     * 
     * @return string 
     */
    public function __toString(): string
    {
        return "SELECT";
    }
}
