<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
/**
 * 테이블 정보 가져오기
 * @param $table_id
 * @param $columns
 * @return array|false|null
 */
function getTableInfo($table_id, $columns = null) {
    global $gtm;

    if (!isset($gtm) || !isset($table_id) || !isset($gtm['table_list'])) return null;
    if ($columns == null)
        $columns = "*";
    $sql = "select {$columns} from {$gtm['table_list']} where id = '{$table_id}' ";
    return sql_fetch($sql);
}

/**
 * 테이블 이름 가져오기
 * @param $table_id
 * @return string|null
 */
function getTableName($table_id) {
    global $gtm;

    if (!isset($gtm) || !isset($table_id)) return null;
    $result = getTableInfo($table_id, "table_name, memo");
    return $result["table_name"].(isset($result["memo"])?" ({$result["memo"]})":"");
}