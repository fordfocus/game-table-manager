<?php
$sub_menu = '600100';
include_once('./_common.php');

if (!isset($auth) || !isset($w) || !isset($gtm) || !isset($gtm_config))
    alert('w 값이 제대로 넘어오지 않았습니다.');

/*
 * 엑셀 업로드할때 몇번째 행부터 데이터로 적용할건지
 */
$gtm_config["excel_data_row"] = 5;

// 테이블 내용이 많을 경우 대비 설정변경
set_time_limit ( 0 );
ini_set('memory_limit', '50M');

auth_check_menu($auth, $sub_menu, "w");

function only_number($n)
{
    return preg_replace('/[^0-9]/', '', (string)$n);
}

$is_upload_file = (isset($_FILES['excelfile']['tmp_name']) && $_FILES['excelfile']['tmp_name']) ? 1 : 0;

if( ! $is_upload_file){
    alert("엑셀 파일을 업로드해 주세요.");
}

$table_name = isset($_POST['table_name']) ? trim($_POST['table_name']) : '';
$memo = isset($_POST['memo']) ? $_POST['memo'] : '';

if ($table_name == '') {
    alert("테이블 이름을 입력해주세요.");
}
/*
 * 같은 이름이 있는지 체크
 */
checkSameTableName($table_name);

$sheetNameConfig = "config";
$sheetNameDatas = "datas";

if($is_upload_file) {
    $file = $_FILES['excelfile']['tmp_name'];

    include_once(G5_LIB_PATH.'/PHPExcel/IOFactory.php');

    $objPHPExcel = PHPExcel_IOFactory::load($file);
    if ($objPHPExcel->sheetNameExists($sheetNameConfig) !== true) {
        alert("추가할 컬럼 정보 시트가 없습니다.");
    }
    if ($objPHPExcel->sheetNameExists($sheetNameDatas) !== true) {
        alert("데이터 시트가 없습니다.");
    }

    $sheet = $objPHPExcel->getSheetByName($sheetNameConfig);

    $num_rows = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    /*
     * 1차 : 테이블 만들기
     */
    $table_id = createUserTable($table_name, $memo);
    if ($table_id == 0)
        alert("테이블 생성이 안되었습니다.");

    /*
     * 2차 : 컬럼 만들기
     * 1열 : column_name
     * 2열 : input_type
     * 3열 : input_size
     * 4열 : 메모
     */
    $column_names = array();
    $input_types = array();
    $input_sizes = array();
    $link_table_ids = array();
    $link_column_ids = array();
    $memos = array();
    for ($i = 2; $i <= $num_rows; $i++) {
        $j = 0;

        $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, FALSE);

        $column_name = $rowData[0][$j++];
        $input_type = $rowData[0][$j++];
        $input_size = $rowData[0][$j++];
        $memo = $rowData[0][$j++];
        if ($memo == '')
            $memo = $column_name;
        $link_table_id = $rowData[0][$j++];
        $link_column_id = $rowData[0][$j++];
        $allow_null = "N";
        $is_linked = "N";
        if ($link_table_id != '')
            $is_linked = "Y";

        if (isset($link_table_id) && isset($link_column_id) && $link_table_id != '' && $link_column_id != '') {
            $linkColumnInfos = getColumnInfos($link_column_id);
            if (isset($linkColumnInfos) && isset($linkColumnInfos["input_type"])) {
                $input_type = $linkColumnInfos["input_type"];
                $input_size = $linkColumnInfos["input_size"];
                $allow_null = $linkColumnInfos["allow_null"];
            }
            else {
                sql_query("delete from {$gtm["table_list"]} where id = {$table_id} ");
                sql_query("delete from {$gtm["column_list"]} where table_id = {$table_id} ");
                sql_query("drop table ".GTM_MAKE_TABLE_PREFIX.$table_name);
                alert("연결할 컬럼 정보가 없습니다.");
            }
        }

        $column_names[] = $column_name;
        $input_types[] = $input_type;
        $input_sizes[] = $input_size;
        $memos[] = $memo;
        $link_table_ids[] = $link_table_id;
        $link_column_ids[] = $link_column_id;

        /*
         * 맨 처음 고유번호 컬럼은 무시
         */
        if ($i == 2 && $column_name == "id") continue;

        $sql_common = "
            table_id = '{$table_id}',
            column_name = '{$column_name}',
            input_type = '{$input_type}',
            input_size = '{$input_size}',
            allow_null = '{$allow_null}',
            is_linked = '{$is_linked}',
            memo = '{$memo}'
            ";
        $not_null_str = '';
        if ($allow_null == 'N') {
            $not_null_str = 'NOT NULL';
        }

        $tableName = getTableNameByQuery($table_id);
        sql_query("alter table ".$tableName." add ".$column_name." ".getColumnDbType($input_type)."(".$input_size.") ".$not_null_str." ");

        $sql = "insert into {$gtm['column_list']} set  writedate = '" . G5_TIME_YMDHIS . "', {$sql_common} ";
        sql_query($sql);

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
    $sheet = $objPHPExcel->getSheetByName($sheetNameDatas);

    $num_rows = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    /*
     * 3차 : 내용 넣기
     */
    $failDatas = [];
    for ($i = 1; $i <= $num_rows; $i++) {
        $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, FALSE);

        /*
         * 맨 처음 컬럼에 #이 들어가 있으면 건너 뛰기
         */
        if (!isset($rowData[0][0]) || strpos($rowData[0][0], "#") !== false) continue;

        $sql_common = "";
        $isContinue = false;
        for ($i2 = 0; $i2 < count($column_names); $i2++) {
            $column_name = $column_names[$i2];
            if (!isset($column_name) || $column_name == '') continue;
            $value = $rowData[0][$i2];
            $input_type = $input_types[$i2];
            $input_size = $input_sizes[$i2];
            $link_table_id = $link_table_ids[$i2];
            $link_column_id = $link_column_ids[$i2];

            if ($link_table_id != '' && $link_column_id != '') {
                $linkTableName = getTableNameByQuery($link_table_id);
                $linkInfos = getUserTableInfo($link_table_id, $link_column_id, $value);
                if (!$linkInfos) {
//                    alert("연결된 테이블에 해당 값이 없습니다. table_id:".$link_table_id."/column_id:".$link_column_id."/value:".$value);
                    $failDatas = array("열"=>$i,"행"=>$i2);
                    $isContinue = true;
                    continue;
                }
            }

            if ($sql_common != "")
                $sql_common .= ", ";

            if (isset($gtm_column_input_type_string) && $input_type == $gtm_column_input_type_string) {
                $value = "'".addslashes($rowData[0][$i2])."'";
            }
            else if (isset($gtm_column_input_type_int) && $input_type == $gtm_column_input_type_int) {
                $value = only_number($rowData[0][$i2]);
            }
            $sql_common .= $column_name." = ".$value;
        }
        if ($isContinue == true)
            continue;
        $isql = "insert into ".$tableName." set ".$sql_common." ";
        sql_query($isql);
    }
}

$g5['title'] = '테이블 엑셀일괄등록 결과';
include_once(G5_PATH.'/head.sub.php');
?>

    <div class="new_win">
        <h1><?php echo $g5['title']; ?></h1>

        <div class="local_desc01 local_desc">
            <p>테이블 등록을 완료했습니다.</p>
        </div>
        <div>
            <?php
            print_r2($failDatas);
            ?>
        </div>

        <div class="btn_win01 btn_win">
            <button type="button" onclick="window.close();">창닫기</button>
        </div>

    </div>

<?php
include_once(G5_PATH.'/tail.sub.php');