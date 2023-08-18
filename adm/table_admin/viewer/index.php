<?php
$sub_menu = "600200";
require_once './_common.php';

if (!isset($auth) || !isset($w) || !isset($gtm) || !isset($config))
    alert('w 값이 제대로 넘어오지 않았습니다.');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from {$gtm['table_list']} ";

$sql_search = " where (1) ";
if (isset($stx) && isset($sfl) && $sfl != "") {
    $sql_search .= " and ( ";
    switch ($sfl) {
        default:
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}
if (!isset($sfl) || !$sfl) {
    $sst = "writedate";
    $sod = "desc";
}
$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if (!isset($page) || $page < 1) {
    $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
}
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$g5['title'] = '테이블 리스트';
require_once '../../admin.head.php';

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$colspan = 7;
?>

    <div class="local_desc01 local_desc">
        <p>
            <b>테스트</b>는 등록된 최고관리자의 이메일로 테스트 메일을 발송합니다.<br>
            현재 등록된 메일은 총 <?php echo $total_count ?>건입니다.<br>
            <strong>주의) 수신자가 동의하지 않은 대량 메일 발송에는 적합하지 않습니다. 수십건 단위로 발송해 주십시오.</strong>
        </p>
    </div>

    <form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">

        <label for="sfl" class="sound_only">검색대상</label>
        <select name="sfl" id="sfl">
            <option value="mb_id" <?php echo get_selected($sfl, "mb_id"); ?>>회원아이디</option>
            <option value="mb_nick" <?php echo get_selected($sfl, "mb_nick"); ?>>닉네임</option>
        </select>
        <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
        <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
        <input type="submit" class="btn_submit" value="검색">

    </form>

    <form name="ftablelist" id="ftablelist" action="./delete.php" method="post">
        <div class="tbl_head01 tbl_wrap">
            <table>
                <caption><?php echo $g5['title']; ?> 목록</caption>
                <thead>
                <tr>
                    <th scope="col"><input type="checkbox" name="chkall" value="1" id="chkall" title="현재 페이지 목록 전체선택" onclick="check_all(this.form)"></th>
                    <th scope="col">번호</th>
                    <th scope="col">테이블 이름</th>
                    <th scope="col">메모</th>
                    <th scope="col">작성일시</th>
                    <th scope="col">테이블보기</th>
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
                        <td class="td_left"><?php echo $row['table_name'] ?></td>
                        <td class=""><?php echo $row['memo'] ?></td>
                        <td class="td_datetime"><?php echo $row['writedate'] ?></td>
                        <td class="">
                            <a href="./view.php?table_id=<?php echo $row['id'] ?>" class="btn btn_03">테이블 보기</a>
                        </td>
                    </tr>

                    <?php
                }
                if (!$i) {
                    echo "<tr><td colspan=\"" . $colspan . "\" class=\"empty_table\">자료가 없습니다.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="btn_fixed_top">
            <?php if (isset($is_admin) && $is_admin == "super") { ?>
                <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
                <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
            <?php } ?>
            <a href="./form.php" id="member_add" class="btn btn_01">테이블 추가</a>
        </div>
    </form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?' . (isset($qstr)?$qstr:"") . '&amp;page='); ?>

    <script>
        $(function() {
            $('#ftablelist').submit(function() {
                if (confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
                    if (!is_checked("chk[]")) {
                        alert("선택삭제 하실 항목을 하나 이상 선택하세요.");
                        return false;
                    }

                    return true;
                } else {
                    return false;
                }
            });
        });
    </script>

<?php
require_once '../../admin.tail.php';
