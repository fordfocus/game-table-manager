<?php
$sub_menu = "600100";
require_once "./_common.php";

if (!isset($auth) || !isset($w) || !isset($gtm) || !isset($g5))
    alert('w 값이 제대로 넘어오지 않았습니다.');

auth_check_menu($auth, $sub_menu, 'w');

check_admin_token();

$table_name = isset($_POST['table_name']) ? trim($_POST['table_name']) : '';
$memo = isset($_POST['memo']) ? $_POST['memo'] : '';

$posts = array();
$check_keys = array(
    'table_name',
    'memo',
);

foreach ($check_keys as $key) {
	$posts[$key] = isset($_POST[$key]) ? clean_xss_tags($_POST[$key], 1, 1) : '';
}

if ($w == '') {
    /*
     * 같은 이름이 있는지 체크
     */
    checkSameTableName($table_name);

    createUserTable($table_name, $posts['memo']);
} else {
    alert('제대로 된 값이 넘어오지 않았습니다.');
}

goto_url('./list.php');
