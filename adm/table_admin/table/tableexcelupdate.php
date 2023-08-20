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

if($is_upload_file) {
    $file = $_FILES['excelfile']['tmp_name'];

    include_once(G5_LIB_PATH.'/PHPExcel/IOFactory.php');

    $objPHPExcel = PHPExcel_IOFactory::load($file);
    $sheet = $objPHPExcel->getSheet(0);

    $num_rows = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    $dup_it_id = array();
    $fail_it_id = array();
    $dup_count = 0;
    $total_count = 0;
    $fail_count = 0;
    $succ_count = 0;


    /*
     * 1차 : 테이블 만들기
     */
    $table_id = createUserTable($table_name, $memo);
    if ($table_id == 0)
        alert("테이블 생성이 안되었습니다.");

    /*
     * 2차 : 컬럼 만들기
     * 1행 : column_name
     * 2행 : input_type
     * 3행 : input_size
     * 4행 : 메모
     */
    $column_names = array();
    $input_types = array();
    $input_sizes = array();
    $memos = array();
    for ($i = 1; $i <= ($gtm_config["excel_data_row"] - 1); $i++) {
        $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, FALSE);

        for($i2 = 0; $i2 < count($rowData[0]); $i2++) {
            if ($i == 1) {
                $column_names[] = $rowData[0][$i2];
            }
            else if ($i == 2) {
                $input_types[] = $rowData[0][$i2];
            }
            else if ($i == 3) {
                $input_sizes[] = $rowData[0][$i2];
            }
            else if ($i == 4) {
                $memos[] = $rowData[0][$i2];
            }
        }
    }
    if (count($column_names) > 0) {
        for ($i = 0; $i < count($column_names); $i++) {
            if (!isset($column_names[$i]) || !isset($input_types[$i]) || !isset($input_sizes[$i]))
                continue;
            $column_name = $column_names[$i];
            /*
             * 맨 처음 고유번호 컬럼은 무시
             */
            if ($i == 0 && $column_name == "id") continue;

            $input_type = $input_types[$i];
            $input_size = $input_sizes[$i];
            $allow_null = "N";
            $is_linked = "N";
            $memo = isset($memos[$i])?$memos[$i]:$column_name;

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

            $link_table_id = 0;
            $link_column_id = 0;
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
    }
    /*
     * 3차 : 내용 넣기
     */
    for ($i = $gtm_config["excel_data_row"]; $i <= $num_rows; $i++) {
        $total_count++;

        $j = 0;

        $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i, NULL, TRUE, FALSE);

        $sql_common = "";
        for ($i2 = 0; $i2 < count($column_names); $i2++) {
            if ($sql_common !== "")
                $sql_common .= ", ";

            $input_type = $input_types[$i2];
            $input_size = $input_sizes[$i2];

            $value = $rowData[0][$i2];
            if (isset($gtm_column_input_type_string) && $input_type == $gtm_column_input_type_string) {
                $value = "'".addslashes($rowData[0][$i2])."'";
            }
            else if (isset($gtm_column_input_type_int) && $input_type == $gtm_column_input_type_int) {
                $value = only_number($rowData[0][$i2]);
            }
            $sql_common .= $column_names[$i2]." = ".$value;
        }
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

        <div class="btn_win01 btn_win">
            <button type="button" onclick="window.close();">창닫기</button>
        </div>

    </div>

<?php
include_once(G5_PATH.'/tail.sub.php');