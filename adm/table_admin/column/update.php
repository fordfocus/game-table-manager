<?php
$sub_menu = "200100";
require_once "./_common.php";

if (!isset($auth) || !isset($w) || !isset($gtm))
    alert('w 값이 제대로 넘어오지 않았습니다.');

auth_check_menu($auth, $sub_menu, 'w');

check_admin_token();

$column_name = isset($_POST['column_name']) ? trim($_POST['column_name']) : '';
$memo = isset($_POST['memo']) ? $_POST['memo'] : '';
$table_id = isset($_POST['table_id']) ? $_POST['table_id'] : 0;
$input_type = isset($_POST['input_type']) ? $_POST['input_type'] : 0;
$input_size = isset($_POST['input_size']) ? $_POST['input_size'] : 0;

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
memo = '{$posts['memo']}'
";

if (isset($w) && $w == '') {
    /*
     * 같은 이름이 있는지 체크
     */
    $sql = " select id from {$gtm['table_column_list']} where column_name = '{$column_name}' ";
    $row = sql_fetch($sql);
    if (isset($row['id']) && $row['id']) {
        alert('동일한 이름의 테이블이 존재합니다. ID : ' . $row['id'] . ' / 테이블이름 : ' . $column_name);
    }
    // ALTER TABLE `테이블명` ADD `컬럼명` 자료형

    sql_query(" insert into {$gtm['table_column_list']} set  writedate = '" . G5_TIME_YMDHIS . "', {$sql_common} ");
} else {
    alert('제대로 된 값이 넘어오지 않았습니다.');
}

goto_url('./form.php?' . (isset($qstr)?$qstr:"") . '&amp;table_id=' . $table_id, false);
