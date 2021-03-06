<?php
/**
 * Created by PhpStorm.
 * User: yizhihouzi
 * Date: 2017/9/17
 * Time: 上午11:19
 */

namespace DBOperate;

/**
 * Class Table
 * @property array cols
 * @package DBOperate
 */
class Table implements Collection
{
    private $tableName;
    private $aliasName;

    /**
     * Table constructor.
     *
     * @param string $tableName
     */
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @param array|null $cols
     * @param bool       $invert
     *
     * @return array|bool
     */
    public function columnObjArr(array $cols = null, $invert = false)
    {
        $cols       = is_array($cols) ? $cols : [];
        $colNameArr = [];
        if (!empty($cols)) {
            foreach ($cols as $key => $v) {
                if (is_numeric($key)) {
                    $colNameArr[] = $v;
                } else {
                    $colNameArr[] = $key;
                }
            }
        }
        if (!empty($colNameArr)) {
            if (!$invert) {
                $colNameArr = array_intersect($colNameArr, $this->cols);
            } else {
                $colNameArr = array_diff($this->cols, $colNameArr);
            }
        } else {
            $colNameArr = $this->cols;
        }
        if (!is_array($colNameArr)) {
            return false;
        }
        $columnObjArr = [];
        foreach ($colNameArr as $v) {
            if (!array_key_exists($v, $cols)) {
                $columnObjArr[] = new Column($v, $this);
            } else {
                $columnObjArr[] = new Column($v, $this, $cols[$v]);
            }
        }
        return $columnObjArr;
    }

    public function withName(string $name)
    {
        $new            = clone $this;
        $new->aliasName = trim($name, '`');
        return $new;
    }

    public function name()
    {
        return $this->tableName;
    }

    public function getReferenceName(): string
    {
        return $this->aliasName ?? $this->tableName;
    }

    function __toString()
    {
        if (!empty($this->aliasName)) {
            return "`$this->tableName` `$this->aliasName`";
        } else {
            return "`$this->tableName`";
        }
    }

    public function __get($colName)
    {
        return new Column(self::unCamelize($colName), $this);
    }

    /**
     * 驼峰命名转下划线命名
     * 思路:
     * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
     *
     * @param        $camelCaps
     * @param string $separator
     *
     * @return string
     */
    private static function unCamelize($camelCaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}