<?php
define('G5_IS_ADMIN', true);
include_once ('../../../common.php');

include_once(G5_ADMIN_PATH.'/table_admin/head.php');

run_event('admin_common');

function checkSameTableName($table_name)
{
    global $gtm;

    if (!isset($gtm) || !isset($table_name) || $table_name == "") return null;

    $sql = " select id from {$gtm['table_list']} where table_name = '{$table_name}' ";
    $row = sql_fetch($sql);
    if (isset($row['id']) && $row['id']) {
        alert('동일한 이름의 테이블이 존재합니다. ID : ' . $row['id'] . ' / 테이블이름 : ' . $table_name);
    }
}
function createUserTable($table_name, $memo)
{
    global $gtm;

    if (!isset($gtm) || !isset($table_name) || $table_name == "") return null;

    $table_id = 0;

    $resultCreateTable = false;
    $resultInsertTable = false;
    $resultInsertColumn = false;
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
        $resultCreateTable = sql_query($sql);
    }

    if ($resultCreateTable == true) {
        sql_begin_transaction();

        $sql_common = "  table_name = '{$table_name}',
             memo = '{$memo}'
             ";
        $resultInsertTable = sql_query(" insert into {$gtm['table_list']} set  writedate = '" . G5_TIME_YMDHIS . "', {$sql_common} ");

        $table_id = sql_insert_id();
        $column_name = "id";
        $input_type = isset($gtm_column_input_type_int)?$gtm_column_input_type_int:2;
        $input_size = 11;
        $allow_null = 'N';
        $memo = '고유번호';
        $sql_common = "
    table_id = '{$table_id}',
    column_name = '{$column_name}',
    input_type = '{$input_type}',
    input_size = '{$input_size}',
    allow_null = '{$allow_null}',
    memo = '{$memo}'
    ";
        $resultInsertColumn = sql_query("insert into {$gtm['column_list']} set  writedate = '" . G5_TIME_YMDHIS . "', {$sql_common} ");

        if ($resultInsertTable && $resultInsertColumn) {
            sql_commit_transaction();
        }
        else {
            sql_rollback_transaction();
            sql_query("drop table ".GTM_MAKE_TABLE_PREFIX.$table_name);
            alert("mysql 처리 실패");
        }
    }
    return $table_id;
}