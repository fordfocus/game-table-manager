<?php
include_once('./_common.php');

if (!isset($auth) || !isset($ajax_default_result_array) || !isset($ajax_result)) {
    echo json_encode(array("resultCode" => -1));
    die();
}

$table_id = isset($_POST['table_id']) ? trim($_POST['table_id']) : 0;

if (!$table_id) {
    $ajax_default_result_array[$ajax_result->message] = "테이블을 선택해주세요.";
    echo json_encode($ajax_default_result_array);
    die();
}

$ajax_default_result_array[$ajax_result->code] = $ajax_result->value_success;
$ajax_default_result_array[$ajax_result->datas] = getColumnListByTableId($table_id);
echo json_encode($ajax_default_result_array);