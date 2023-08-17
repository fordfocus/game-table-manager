<?php
$sub_menu = "600200";
require_once './_common.php';

if (!isset($auth) || !isset($w) || !isset($gtm))
    alert('w 값이 제대로 넘어오지 않았습니다.');

auth_check_menu($auth, $sub_menu, 'w');

$table_id = isset($table_id) ? (int) $table_id : 0;

if ($table_id == 0) {
    alert("테이블을 선택해주세요", GTM_ADMIN_MAKE_TABLE_URL . '/list.php');
}

$tableName = getTableName($table_id);
if (!$tableName) {
    alert("테이블을 선택해주세요", GTM_ADMIN_MAKE_TABLE_URL . '/list.php');
}

$id = isset($id) ? (int) $id : 0;
$infos = array(
    'column_name' => '',
    'memo' => '',
    'input_type' => '',
    'input_size' => '',
);

$html_title = '컬럼';
if ($w == '') {
    $html_title .= ' 생성';
} elseif ($w == 'u') {
    $html_title .= ' 수정';
    $sql = " select * from {$gtm['table_column_list']} where id = '{$id}' ";
    $infos = sql_fetch($sql);
} else {
    alert('w 값이 제대로 넘어오지 않았습니다.');
}

$g5['title'] = $html_title;
require_once '../../admin.head.php';
?>

<form name="fpoll" id="fpoll" action="./update.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id ?>">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="table_id" value="<?php echo $table_id ?>">
    <input type="hidden" name="token" value="">

    <div class="tbl_frm01 tbl_wrap">

        <table>
            <caption><?php echo $g5['title']; ?></caption>
            <tbody>
            <tr>
                <th scope="row"><label for="column_name">테이블명<strong class="sound_only"></strong></label></th>
                <td colspan="3">
                    <?=$tableName?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="column_name">컬럼명<strong class="sound_only">필수</strong></label></th>
                <td>
                    <?php echo help("컬럼명은 영문만 가능합니다. 최대 20자만 가능합니다.") ?>
                    <input type="text" name="column_name" value="<?php echo $infos['column_name'] ?>" required id="column_name" class="required frm_input" size="20" maxlength="20">
                </td>
                <th scope="row"><label for="memo">메모</label></th>
                <td>
                    <input type="text" name="memo" value="<?php echo $infos['memo'] ?>" id="memo" class="frm_input" size="50">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="input_type">입력가능 타입<strong class="sound_only">필수</strong></label></th>
                <td>
                    <?php echo help("1:문자, 2:정수, 3:소수점") ?>
                    <input type="number" name="input_type" value="<?php echo $infos['input_type'] ?>" required id="input_type" class="required frm_input" size="20" maxlength="20">
                </td>
                <th scope="row"><label for="input_size">입력가능 사이즈<strong class="sound_only">필수</strong></label></th>
                <td>
                    <?php echo help("1 ~ 255") ?>
                    <input type="number" name="input_size" value="<?php echo $infos['input_size'] ?>" id="input_size" class="required frm_input" size="4" maxlength="4">
                </td>
            </tr>
            </tbody>
        </table>

    </div>

    <div class="btn_fixed_top ">
        <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
    </div>

</form>

<?php
require_once '../../admin.tail.php';
