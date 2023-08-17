<?php
$sub_menu = "600100";
require_once './_common.php';

if (!isset($auth) || !isset($w) || !isset($gtm))
    alert('w 값이 제대로 넘어오지 않았습니다.');

auth_check_menu($auth, $sub_menu, 'w');

$id = isset($id) ? (int) $id : 0;
$tableInfos = array(
    'table_name' => '',
    'memo' => '',
);

$html_title = '테이블';
if ($w == '') {
    $html_title .= ' 생성';
} elseif ($w == 'u') {
    $html_title .= ' 수정';
    $sql = " select * from {$gtm['table_list']} where id = '{$id}' ";
    $tableInfos = sql_fetch($sql);
} else {
    alert('w 값이 제대로 넘어오지 않았습니다.');
}

$g5['title'] = $html_title;
require_once '../../admin.head.php';
?>

<form name="fpoll" id="fpoll" action="./update.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id ?>">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="token" value="">

    <div class="tbl_frm01 tbl_wrap">

        <table>
            <caption><?php echo $g5['title']; ?></caption>
            <tbody>
                <tr>
                    <th scope="row"><label for="table_name">테이블명<strong class="sound_only">필수</strong></label></th>
                    <td>
                        <?php echo help("테이블명은 영문만 가능합니다. 최대 30자만 가능합니다.") ?>
                        <input type="text" name="table_name" value="<?php echo $tableInfos['table_name'] ?>" required id="table_name" class="required frm_input" size="30" maxlength="30">
                    </td>
                    <th scope="row"><label for="memo">메모</label></th>
                    <td>
                        <input type="text" name="memo" value="<?php echo $tableInfos['memo'] ?>" id="memo" class="frm_input" size="50">
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
