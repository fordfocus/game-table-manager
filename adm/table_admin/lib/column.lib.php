<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/**
 * 특정 테이블의 컬럼 리스트 가져오기
 * @param $table_id
 * @return array|null
 */
function getColumnListByTableId($table_id) {
    global $gtm;

    if (!isset($gtm) || !isset($table_id)) return null;
    $sql = "select * from ".$gtm['column_list']." where table_id = '".$table_id."' order by column_name asc ";
    $result = sql_query($sql);
    $datas = array();
    for ($i = 0; $row = sql_fetch_array($result); $i++) {
        $datas[] = $row;
    }
    return $datas;

}

/**
 * 컬럼 정보 가져오기
 * @param $table_id
 * @param $column_id
 * @return array|false|null
 */
function getColumnInfos($column_id)
{
    global $gtm;

    if (!isset($gtm) || !isset($column_id)) return null;
    $sql = "select * from ".$gtm['column_list']." where id ='".$column_id."' ";
    $result = sql_fetch($sql);
    return $result;
}
function getColumnLinkInfos($table_id, $column_id)
{
    global $gtm;

    if (!isset($gtm) || !isset($table_id) || !isset($column_id)) return null;

    $sql = "select * from ".$gtm['column_link_list']." where table_id = '".$table_id."' and column_id ='".$column_id."' ";
    $result = sql_fetch($sql);
    return $result;
}
/**
 * 컬럼 입력 타입 selectbox 만들기 
 * @return string|null
 */
function getSelectBoxByColumnType() {
    global $gtm_config;

    if (!isset($gtm_config)) return null;

    $html = "<select name='input_type' id='input_type'>";
    foreach($gtm_config["column_input_types"] as $key => $val) {
        $html .= "<option value='".$key."'>".$val["text"]." (".$val["db_type"].")</option>";
    }
    $html .= "</select>";
    return $html;
}

/**
 * 컬럼 입력 db type 가져오기
 * @param $input_type
 * @return mixed|null
 */
function getColumnDbType($input_type) {
    global $gtm_config;

    if (!isset($gtm_config) || !isset($input_type)) return null;
    return $gtm_config["column_input_types"][$input_type]["db_type"];
}
function getColumnName($column_id, $addMemo = false) {
    global $gtm_config;

    if (!isset($gtm_config) || !isset($column_id)) return null;
    $result = getColumnInfos($column_id);
    if (!isset($result) || !$result || !$result["column_name"]) {
        return null;
    }
    return $result["column_name"].($addMemo == true && isset($result["memo"])?" ({$result["memo"]})":"");
}
