<?php
declare(strict_types = 1);

namespace QueryBuilder\Queries\Parts;

use Exception;
use QueryBuilder\Exceptions\InvalidConditionException;

/**
 * Class SQLCondition
 * 
 * @package QueryBuilder
 * 
 * @author Ugo Brocard
 * @license MIT
 */
class SQLCondition
{
    const REGEX_SQL_OPERATOR_IN      = "/^(?:NOT\s)?IN$/";
    const REGEX_SQL_OPERATOR_LIKE    = "/^(?:NOT\s)?LIKE$/";
    const REGEX_SQL_OPERATOR_BETWEEN = "/^(?:NOT\s)?BETWEEN$/";
    const REGEX_SQL_OPERATOR_IS_NULL = "/^IS\s(?:NOT\s)?BETWEEN$/";

    const REGEX_SQL_OPERATOR_FILTER  = "/^(?:<(?:=|>)?|!=|=|>=?)$/";

    /**
     * Variable statement
     * 
     * @var string
     */
    protected string $statement;

    /**
     * Variable values
     * 
     * @var array
     */
    protected array  $values = [];

    /**
     * Method __construct
     * 
     * @param SQLCondition|array $condition 
     * @return void 
     */
    public function __construct(self|array $condition)
    {
        $this->statement .= "(" . $this->resolveCondition($condition) . ")";
    }

    /**
     * Method and
     * 
     * @param SQLCondition|array $condition 
     * @return SQLCondition 
     */
    public function and(self|array $condition): self
    {
        $this->statement .= " AND (" . $this->resolveCondition($condition) . ")";
        return $this;
    }

    /**
     * Method or
     * 
     * @param SQLCondition|array $condition 
     * @return SQLCondition 
     */
    public function or(self|array $condition): self
    {
        $this->statement .= " OR (" . $this->resolveCondition($condition) . ")";
        return $this;
    }

    /**
     * Method resolveCondition
     * 
     * @param SQLCondition|array $condition 
     * @return string 
     */
    protected function resolveCondition(self|array $condition): string
    {
        if ($condition instanceof self) {
            $this->values = array_merge($this->values, $condition->getValues());
            return $condition->getStatement();
        }

        return $this->resolveConditionFromArray($condition);
    }

    /**
     * Method resolveConditionFromArray
     * 
     * @param array $condition 
     * @return string 
     * @throws Exception 
     */
    protected function resolveConditionFromArray(array $condition): string
    {
        if (sizeof($condition) < 2) {
            throw new InvalidConditionException;
        }
        
        $column   = $condition[0];
        if (!is_string($column)) {
            throw new InvalidConditionException("Columns must be of type string");
        }

        $operator = $condition[1];
        if (!is_string($operator)) {
            throw new InvalidConditionException("Operators must be of type string");
        }
        
        if (preg_match(self::REGEX_SQL_OPERATOR_IS_NULL, $operator)) {
            $statement = $this->isNull($column, $operator);
        }
        
        if (sizeof($condition) < 3) {
            throw new InvalidConditionException;
        }
        
        if (preg_match(self::REGEX_SQL_OPERATOR_IN, $operator)) {
            [ $statement, $values ] = $this->in($column, $operator, $condition[2]);
        }
        
        if (preg_match(self::REGEX_SQL_OPERATOR_LIKE, $operator)) {
            [ $statement, $values ] = $this->like($column, $operator, $condition[2]);
        }
        
        if (preg_match(self::REGEX_SQL_OPERATOR_BETWEEN, $operator)) {
            [ $statement, $values ] = $this->between($column, $operator, $condition[2]);
        }
        
        if (preg_match(self::REGEX_SQL_OPERATOR_FILTER, $operator)) {
            $statement = $this->filter($column, $operator, $condition[2])[0];
            $values    = array($this->filter($column, $operator, $condition[2])[1]);
        }
        
        if (!isset($statement)) {
            throw new InvalidConditionException;
        }
        
        if (isset($values)) {
            $this->values = array_merge($this->values, $values);
        }
        
        return $statement;
    }
    
    /**
     * Method isNull
     * 
     * @param string $column 
     * @param string $operator 
     * @return string 
     */
    protected function isNull(string $column, string $operator): string
    {
        return "{$column} {$operator}";
    }
    
    /**
     * Method in
     * 
     * @param string $column 
     * @param string $operator 
     * @param array $values 
     * @return array 
     */
    protected function in(string $column, string $operator, array $values): array
    {
        $numberOfValues = sizeof($values);
        $statement      = "{$column} {$operator} (" . rtrim( str_repeat("?, ", $numberOfValues), ", ") . ")";
        
        return array($statement, $values);
    }
    
    /**
     * Method like
     * 
     * @param string $column 
     * @param string $operator 
     * @param array $values 
     * @return array 
     */
    protected function like(string $column, string $operator, array $values): array
    {
        $values    = $values[0];
        $statement = "{$column} {$operator} {$values}";
        
        return array($statement, $values);
    }

    /**
     * Method between
     * 
     * @param string $column 
     * @param string $operator 
     * @param array $values 
     * @return array 
     */
    protected function between(string $column, string $operator, array $values): array
    {
        $statement = "{$column} {$operator} ? AND ?";
        $values    = array($values[0], $values[1]);
        
        return array($statement, $values);
    }
    
    /**
     * Method filter
     * 
     * @param string $column 
     * @param string $operator 
     * @param mixed $value 
     * @return array 
     */
    protected function filter(string $column, string $operator, mixed $value): array
    {
        $statement = "{$column} {$operator} ?";
        return array($statement, $value);
    }

    /**
     * Method getStatement
     * 
     * @return string 
     */
    public function getStatement(): string
    {
        return $this->statement;
    }
    
    /**
     * Method getValues
     * 
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
