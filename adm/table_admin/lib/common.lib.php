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
function getRadioByYN($htmlName, $datas) {
    if (!isset($htmlName) || !isset($datas)) return null;

    $html = '';
    foreach($datas as $key => $val) {
        $html .= ' <input type="radio" name="'.$htmlName.'" value="'.$key.'" id="'.$htmlName.'_'.$key.'"><label for="'.$htmlName.'_'.$key.'">'.$val.'</label>';
    }
    return $html;
}