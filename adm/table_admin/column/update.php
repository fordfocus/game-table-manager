<?php
$sub_menu = "600101";
require_once "./_common.php";

if (!isset($auth) || !isset($w) || !isset($gtm) || !isset($gtm_config) )
    alert('값이 제대로 넘어오지 않았습니다.');

auth_check_menu($auth, $sub_menu, 'w');

check_admin_token();

$column_name = isset($_POST['column_name']) ? trim($_POST['column_name']) : '';
$memo = isset($_POST['memo']) ? $_POST['memo'] : '';
$table_id = isset($_POST['table_id']) ? $_POST['table_id'] : 0;
$input_type = isset($_POST['input_type']) ? $_POST['input_type'] : 0;
$input_size = isset($_POST['input_size']) ? $_POST['input_size'] : 0;
$allow_null = isset($_POST['allow_null']) ? $_POST['allow_null'] : 'N';
$is_linked = isset($_POST['is_linked']) ? $_POST['is_linked'] : 'N';

$link_table_id = isset($_POST['link_table_id']) ? $_POST['link_table_id'] : 0;
$link_column_id = isset($_POST['link_column_id']) ? $_POST['link_column_id'] : 0;

/*
 * 링크 되는 컬럼일때, 링크 되는 컬럼 정보로 만들기
 */
if ($is_linked == 'Y' && $link_table_id !== 0 && $link_column_id !== 0) {
    $linkColumnInfos = getColumnInfos($link_column_id);
    if (!$linkColumnInfos) {
        alert("링크할 컬럼 정보가 존재하지 않습니다.");
    }
    $input_type = $linkColumnInfos["input_type"];
    $input_size = $linkColumnInfos["input_size"];
    $allow_null = $linkColumnInfos["allow_null"];
}

$tableName = getTableNameByQuery($table_id);

/*
 * 필수 항목 체크하기
 */

$posts = array();
$check_keys = array(
    'table_id',
    'column_name',
    'memo',
    'input_type',
    'input_size'
);

foreach ($check_keys as $key) {
	$posts[$key] = isset($_POST[$key]) ? clean_xss_tags($_POST[$key], 1, 1) : '';
}

$sql_common = "
table_id = '{$table_id}',
column_name = '{$column_name}',
input_type = '{$input_type}',
input_size = '{$input_size}',
allow_null = '{$allow_null}',
is_linked = '{$is_linked}',
memo = '{$posts['memo']}'
";

if (isset($w) && $w == '') {
    /*
     * 같은 이름이 있는지 체크
     */
    $sql = " select id from {$gtm['column_list']} where table_id = '".$table_id."' and column_name = '{$column_name}' ";
    $row = sql_fetch($sql);
    if (isset($row['id']) && $row['id']) {
        alert('동일한 이름의 컬럼이 존재합니다. ID : ' . $row['id'] . ' / 컬럼 이름 : ' . $column_name);
    }
    // ALTER TABLE `테이블명` ADD `컬럼명` 자료형
    // ALTER TABLE `employee` ADD `comments` VARCHAR(200) NOT NULL
    $not_null_str = '';
    if ($allow_null == 'N') {
        $not_null_str = 'NOT NULL';
    }
    sql_query("alter table ".$tableName." add ".$column_name." ".getColumnDbType($input_type)."(".$input_size.") ".$not_null_str." ");

    sql_query(" insert into {$gtm['column_list']} set  writedate = '" . G5_TIME_YMDHIS . "', {$sql_common} ");

    if ($is_linked == 'Y') {
        $column_id = sql_insert_id();
        $sql_common = "
            table_id = '{$table_id}',
            column_id = '{$column_id}',
            link_table_id = '{$link_table_id}',
            link_column_id = '{$link_column_id}',
            writedate = '".G5_TIME_YMDHIS."'
            ";
        sql_query(" insert into {$gtm['column_link_list']} set {$sql_common} ");
    }
}
else {
    alert('제대로 된 값이 넘어오지 않았습니다.ss');
}

goto_url('./list.php?' . (isset($qstr)?$qstr:"") . '&amp;table_id=' . $table_id, false);
