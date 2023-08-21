<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
/**
 * 유저가 생성한 테이블의 정보 가져오기
 * @param $table_id
 * @param $columns
 * @return array|false|null
 */
function getUserTableInfo($table_id, $column_id, $value) {
    global $gtm;

    if (!isset($gtm) || !isset($table_id) || !isset($column_id) || !isset($value) || $table_id == 0 || $column_id == 0) return null;
    $tableName = getTableNameByQuery($table_id);
    $columnName = getColumnName($column_id);
    $sql = "select * from {$tableName} where {$columnName} = '{$value}' ";
    $arr = sql_fetch($sql);
    return (isset($arr))?$arr:null;
}

