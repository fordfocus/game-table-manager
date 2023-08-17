<?php
$sub_menu = "600100";
require_once "./_common.php";

if (!isset($auth) || !isset($w) || !isset($gtm))
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

$sql_common = "  table_name = '{$table_name}',
                 memo = '{$posts['memo']}'
				 ";

if ($w == '') {
    /*
     * 같은 이름이 있는지 체크
     */
    $sql = " select id from {$gtm['table_list']} where table_name = '{$table_name}' ";
    $row = sql_fetch($sql);
    if (isset($row['id']) && $row['id']) {
        alert('동일한 이름의 테이블이 존재합니다. ID : ' . $row['id'] . ' / 테이블이름 : ' . $table_name);
    }

    $isCreateTable = false;
    // 테이블 생성 ------------------------------------
    $file = implode('', file('./default_table.sql'));
    eval("\$file = \"$file\";");

    $file = preg_replace('/^--.*$/m', '', $file);
    $file = preg_replace('/table_name/', GTM_MAKE_TABLE_PREFIX.$table_name, $file);
    $f = explode(';', $file);
    for ($i=0; $i<count($f); $i++) {
        if (trim($f[$i]) == '') {
            continue;
        }

        $sql = get_db_create_replace($f[$i]);
        $isCreateTable = sql_query($sql, true);
    }
    if ($isCreateTable == true)
        sql_query(" insert into {$gtm['table_list']} set  writedate = '" . G5_TIME_YMDHIS . "', {$sql_common} ");
} else {
    alert('제대로 된 값이 넘어오지 않았습니다.');
}

goto_url('./list.php');
