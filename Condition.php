<?php
/**
 * Created by PhpStorm.
 * User: yizhihouzi
 * Date: 2017/9/17
 * Time: 上午11:19
 */

namespace DBOperate;


use DBOperate\Exception\DBOperateException;
use DBOperate\Operate\Select;

class Condition
{
    /**
     * @var string 条件组，同一组内是and关系，不同组为或关系
     */
    private $groupName;
    /**
     * @var string 关系 > < <> = in
     */
    private $relation;
    /**
     * @var Column 列名
     */
    private $column;
    /**
     * @var mixed 与$col列值进行比较的值
     */
    private $value;

    /**
     * Condition constructor.
     *
     * @param Column $column
     * @param        $value
     * @param string $relation
     * @param string $groupName
     *
     * @throws DBOperateException
     */
    public function __construct(Column $column, $value, $relation = '=', $groupName = 'e')
    {
        if ($relation == 'in' && !is_array($value)) {
            DBOperateException::invalidConditionValue();
        }
        $this->groupName = $groupName;
        $this->relation  = $relation;
        $this->column    = $column;
        $this->value     = $value;
    }

    /**
     * @return array|bool|float|int|mixed|string
     */
    public function getValue()
    {
        if (is_scalar($this->value)) {
            return $this->value;
        } elseif ($this->relation == 'in' && is_array($this->value)) {
            return $this->value;
        } else {
            if ($this->value instanceof Select) {
                return $this->value->prepareValues();
            }
            return false;
        }
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $relation = $this->relation;
        if (is_scalar($this->value)) {
            $v = '?';
        } elseif ($this->relation == 'in' && is_array($this->value)) {
            $valueHolder = str_repeat('?,', count($this->value));
            $valueHolder = rtrim($valueHolder, ',');
            $v           = "($valueHolder)";
        } else {
            if ($this->value instanceof Select) {
                $v = $this->value->prepareStr();
                $v = "($v)";
            } elseif (is_null($this->value)) {
                $v = '';
                if ($this->relation == '=') {
                    $relation = 'is null';
                } else {
                    $relation = 'is not null';
                }
            } else {
                $v = $this->value;
            }
        }
        return sprintf("%s %s %s", (string)$this->column, $relation, $v);
    }
}