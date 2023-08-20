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

$sql = "select * from ".$tableName." order by id";
$result = sql_query($sql);

$g5['title'] = "테이블 보기";
require_once '../../admin.head.php';

$colspan = 7;
?>

    <div class="local_desc01 local_desc">
        <p>
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


<?php
require_once '../../admin.tail.php';
