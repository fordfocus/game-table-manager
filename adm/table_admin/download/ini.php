<?php
$sub_menu = '600201';
include_once ("./_common.php");


if (!isset($auth) || !isset($w) || !isset($gtm) || !isset($gtm_config))
    alert('w 값이 제대로 넘어오지 않았습니다.');

$table_id = isset($_GET["table_id"]) ? (int) $_GET["table_id"] : 0;
$tableName = getTableNameByQuery($table_id);
if (!isset($tableName) || !$tableName || $tableName == "") {
    alert('테이블을 선택해주세요. table_id:'.$table_id);
}

// 테이블 내용이 많을 경우 대비 설정변경
set_time_limit ( 0 );
ini_set('memory_limit', '50M');

auth_check_menu($auth, $sub_menu, "r");

$lists = getTableLists($table_id);
$columLists = getColumnListByTableId($table_id);

$iniContents = '#';
$query = "SHOW COLUMNS FROM {$tableName}";
$result = sql_query($query);
for($i=0; $row=sql_fetch_array($result); ++$i) {
    if ($i != 0)
        $iniContents .= "\t";
    $iniContents .= $row['Field'];
}

$iniContents .= "\n";
for($i = 0; $i < count($lists); ++$i) {
    $list = $lists[$i];
    if (!isset($list)) continue;
    $j = 0;
    foreach($list as $column_name => $val) {
        if ($j != 0)
            $iniContents .= "\t";
        $iniContents .= $val;
        ++$j;
    }
    $iniContents .= "\n";
}
$tableININame = getTableInIName($table_id);
// 파일명과 MIME 타입 설정
$filename = $tableININame;
$mime = 'text/plain';

// 헤더 설정
header("Content-type: $mime");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");

// ini 파일 내용 출력
echo $iniContents;
