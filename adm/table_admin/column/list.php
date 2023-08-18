<?php
$sub_menu = "600101";
require_once './_common.php';

if (!isset($auth) || !isset($w) || !isset($gtm))
    alert('w 값이 제대로 넘어오지 않았습니다.');

auth_check_menu($auth, $sub_menu, 'r');

$table_id = isset($table_id) ? (int) $table_id : 0;

if ($table_id == 0) {
    alert("테이블을 선택해주세요", GTM_ADMIN_MAKE_TABLE_URL . '/list.php');
}

$sql = " select * from {$gtm['table_column_list']} where table_id = '{$table_id}' ";
$result = sql_query($sql);

$g5['title'] = "컬럼 관리";
require_once '../../admin.head.php';

$colspan = 7;
?>

<div class="local_ov01 local_ov">
</div>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" id="sfl">
        <option value="mb_id" <?php echo get_selected($sfl, "mb_id"); ?>>회원아이디</option>
        <option value="po_content" <?php echo get_selected($sfl, "po_content"); ?>>내용</option>
    </select>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
    <input type="submit" class="btn_submit" value="검색">
</form>

<form name="fpointlist" id="fpointlist" method="post" action="./point_list_delete.php" onsubmit="return fpointlist_submit(this);">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="token" value="">

    <div class="tbl_head01 tbl_wrap">
        <table>
            <caption><?php echo $g5['title']; ?> 목록</caption>
            <thead>
            <tr>
                <th scope="col">
                    <label for="chkall" class="sound_only">포인트 내역 전체</label>
                    <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
                </th>
                <th scope="col">번호</th>
                <th scope="col">컬럼 이름</th>
                <th scope="col">메모</th>
                <th scope="col">입력가능 타입</th>
                <th scope="col">입력가능 사이즈</th>
                <th scope="col">Null 허용</th>
                <th scope="col">작성일시</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $row = sql_fetch_array($result); $i++) {
                $bg = 'bg' . ($i % 2);
                ?>

                <tr class="<?php echo $bg; ?>">
                    <td class="td_chk">
                        <label for="chk_<?php echo $i; ?>" class="sound_only"></label>
                        <input type="checkbox" id="chk_<?php echo $i ?>" name="chk[]" value="<?php echo $row['id'] ?>">
                    </td>
                    <td class="td_num_c"><?php echo $row["id"] ?></td>
                    <td class="td_left"><?php echo $row['column_name'] ?></td>
                    <td class=""><?php echo $row['memo'] ?></td>
                    <td class=""><?php echo $row['input_type'] ?></td>
                    <td class=""><?php echo $row['input_size'] ?></td>
                    <td class=""><?php echo $row['allow_null'] ?></td>
                    <td class="td_datetime"><?php echo $row['writedate'] ?></td>
                </tr>

                <?php
            }

            if ($i == 0) {
                echo '<tr><td colspan="' . $colspan . '" class="empty_table">자료가 없습니다.</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>

    <div class="btn_fixed_top">
        <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    </div>

</form>
<?php
$tableName = getTableName($table_id, true);
?>

<section id="point_mng">
    <h2 class="h2_frm">신규 컬럼 추가</h2>
    <form name="fcolumn" id="fcolumn" action="./update.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="w" value="">
        <input type="hidden" name="table_id" value="<?php echo $table_id ?>">
        <input type="hidden" name="token" value="">

        <div class="tbl_frm01 tbl_wrap">

            <table>
                <caption><?php echo $g5['title']; ?></caption>
                <tbody>
                <tr>
                    <th scope="row"><label for="column_name">테이블명<strong class="sound_only"></strong></label></th>
                    <td colspan="3"><?=$tableName?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="column_name">컬럼명<strong class="sound_only">필수</strong></label></th>
                    <td>
                        <?php echo help("컬럼명은 영문만 가능합니다. 최대 20자만 가능합니다.") ?>
                        <input type="text" name="column_name" value="" required id="column_name" class="required frm_input" size="20" maxlength="20">
                    </td>
                    <th scope="row"><label for="memo">메모</label></th>
                    <td>
                        <input type="text" name="memo" value="" id="memo" class="frm_input" size="50">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="input_type">입력가능 타입<strong class="sound_only">필수</strong></label></th>
                    <td>
                        <?php echo help("1:문자, 2:정수, 3:소수점") ?>
                        <?=getSelectBoxByColumnType()?>
                    </td>
                    <th scope="row"><label for="input_size">입력가능 사이즈<strong class="sound_only">필수</strong></label></th>
                    <td>
                        <?php echo help("1 ~ 255") ?>
                        <input type="number" name="input_size" value="" id="input_size" class="required frm_input" size="4" maxlength="4">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="input_type">NOT NULL 허용<strong class="sound_only"></strong></label></th>
                    <td colspan="3">
                        <?=getRadioByYN('allow_null', array("Y"=>"NULL 허용","N"=>"NULL 비허용"))?>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>

        <div class="btn_confirm01 btn_confirm">
            <input type="submit" value="확인" class="btn_submit btn" >
        </div>

    </form>
</section>

<?php
require_once '../../admin.tail.php';
