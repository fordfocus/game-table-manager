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
function getTableName($table_id, $addMemo = false) {
    global $gtm;

    if (!isset($gtm) || !isset($table_id)) return null;
    $result = getTableInfo($table_id, "table_name, memo");
    if (!isset($result) || !$result || !$result["table_name"]) {
        return null;
    }
    return $result["table_name"].($addMemo == true && isset($result["memo"])?" ({$result["memo"]})":"");
}

/**
 * 생성한 테이블 정보를 ini 로 다운로드할때에 사용하는 파일명 
 * @param $table_id
 * @return string|null
 */
function getTableInIName($table_id) {
    global $gtm;

    if (!isset($gtm) || !isset($table_id)) return null;
    $name = getTableName($table_id);
    if (!isset($name) || $name == '')
        alert("테이블 정보가 없습니다.");
    return $name.".ini";
}
/**
 * 유저가 생성한 테이블 이름에 prefix 붙여서 가져오기
 * @param $table_id
 * @return string|null
 */
function getTableNameByQuery($table_id) {
    global $gtm;

    if (!isset($gtm) || !isset($table_id)) return null;
    $tableName = getTableName($table_id);
    if (!isset($tableName) || !$tableName)
        return null;
    return GTM_MAKE_TABLE_PREFIX.$tableName;
}
/**
 * 유저가 생성한 테이블 내용 가져오기
 * @param $table_id
 * @return array|null
 */
function getTableLists($table_id) {
    global $gtm;

    if (!isset($gtm) || !isset($table_id)) return null;
    $tableName = getTableNameByQuery($table_id);
    $sql = "select * from ".$tableName." order by id ";
    $result = sql_query($sql);
    $datas = array();
    for ($i = 0; $row = sql_fetch_array($result); $i++) {
        $datas[] = $row;
    }
    return $datas;
}