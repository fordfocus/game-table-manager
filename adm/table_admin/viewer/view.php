<?php
$sub_menu = "600201";
require_once './_common.php';

if (!isset($auth) || !isset($w) || !isset($gtm))
    alert('w 값이 제대로 넘어오지 않았습니다.');

auth_check_menu($auth, $sub_menu, 'w');

$id = isset($_GET["table_id"]) ? (int) $_GET["table_id"] : 0;
$tableName = getTableNameByQuery($id);
if (!isset($tableName) || !$tableName || $tableName == "") {
    alert('테이블을 선택해주세요. id:'.$id);
}
$sql = "select * from ".$gtm['column_list']." where table_id = '".$id."' ";
$result = sql_query($sql);
$columnInfos = array();
for ($i = 0; $row = sql_fetch_array($result); $i++) {
    $columnInfos[] = $row;
}


$sql_common = " from {$tableName} ";

$sql_search = " where (1) ";
if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        default:
            $sql_search .= " ({$sfl} like '{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst = "id";
    $sod = "asc";
}
$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 1;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) {
    $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
}
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$g5['title'] = "테이블 보기";
require_once '../../admin.head.php';

$qstr .= "&amp;table_id=".$table_id;
$colspan = 7;
?>

    <div class="local_ov01 local_ov">
        <a href="<?=$_SERVER['SCRIPT_NAME']?>?table_id=<?=$table_id?>" class="ov_listall">전체목록</a>
    </div>

    <form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
        <input type="hidden" name="table_id" value="<?=$table_id?>">
        <label for="sfl" class="sound_only">검색대상</label>
        <select name="sfl" id="sfl"><?php
            for($i=0; $i<count($columnInfos); $i++) {
                $info = $columnInfos[$i];
                if (!isset($info) || $info["column_name"] == '') continue;
                echo '<option value="'.$info["column_name"].'" '.get_selected($sfl, $info["column_name"]).'>'.(($info["memo"]!='')?$info["memo"]:$info["column_name"]).'</option>';
            }
            ?>
        </select>
        <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
        <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
        <input type="submit" class="btn_submit" value="검색">

    </form>

    <div class="local_desc01 local_desc">
        <p>테이블 이름 : <?=getTableName($id, true)?>
        </p>
    </div>

    <div class="tbl_head01 tbl_wrap">
        <table>
            <caption><?php echo $g5['title']; ?> 목록</caption>
            <thead>
            <tr>
                <th scope="col">번호</th>
                <?php
                for ($i = 0; $i < count($columnInfos); $i++) {
                    $column = $columnInfos[$i];
                    echo '<th scope="col">'.$column["memo"].'</th>';
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $row = sql_fetch_array($result); $i++) {
                $bg = 'bg' . ($i % 2);
                ?>

                <tr class="<?php echo $bg; ?>">
                    <td class="td_num_c"><?php echo $row["id"] ?></td>
                    <?php
                    for ($i = 0; $i < count($columnInfos); $i++) {
                        $column = $columnInfos[$i];
                        echo '<td class="">'.$row[$column["column_name"]].'</td>';
                    }
                    ?>
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

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?' . $qstr . '&amp;page='); ?>

    <div class="btn_fixed_top">
        <a href="<?=GTM_ADMIN_DOWNLOAD_URL?>/ini.php?table_id=<?=$id?>" class="btn btn_01">ini 다운로드</a>
    </div>


<?php
require_once '../../admin.tail.php';
