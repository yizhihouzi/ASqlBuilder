<?php
/**
 * Created by PhpStorm.
 * User: yizhihouzi
 * Date: 2017/9/17
 * Time: 上午11:19
 */

namespace DBOperate;

use DBOperate\Table\InformationSchemaColumns;

/**
 * Class Table
 * @property array cols
 * @package DBOperate
 */
class Table
{
    /**
     * eg:['table1'=>['col1','col2'],'table2'=>['col1','col2']]
     * @var array
     */
    private static $tableCols = [];
    private        $tableName;

    /**
     * Table constructor.
     *
     * @param string $table
     *
     * @internal param string $alias
     */
    public function __construct(string $table)
    {
        $this->tableName = $table;
    }

    public function columnObjArr(array $cols = null, $invert = false): array
    {
        if (!empty($cols)) {
            if (!$invert) {
                $cols = array_intersect($cols, $this->cols);
            } else {
                $cols = array_diff($this->cols, $cols);
            }
        } else {
            $cols = $this->cols;
        }
        $columnObjArr = [];
        foreach ($cols as $key => $v) {
            if (is_numeric($key)) {
                $columnObjArr[] = new Column($v, $this);
            } else {
                $columnObjArr[] = new Column($key, $this, $v);
            }
        }
        return $columnObjArr;
    }

    public function cols()
    {
        return $this->cols;
    }

    function __get($name)
    {
        if ($name == 'cols') {
            if (!isset(self::$tableCols[$this->tableName])) {
                $cols                              =
                    InformationSchemaColumns::getTableCols($this->tableName);
                self::$tableCols[$this->tableName] = $cols;
            }
            return self::$tableCols[$this->tableName];
        }
    }

    function __toString()
    {
        return "`$this->tableName`";
    }
}