<?php
$sub_menu = '600100';
include_once('./_common.php');

if (!isset($auth))
    alert('w 값이 제대로 넘어오지 않았습니다.');

auth_check_menu($auth, $sub_menu, "w");

$g5['title'] = '엑셀파일로 테이블 등록';
include_once(G5_PATH.'/head.sub.php');
?>

    <div class="new_win">
        <h1><?php echo $g5['title']; ?></h1>

        <div class="local_desc01 local_desc">
            <p>
                엑셀파일을 이용하여 테이블을 등록할 수 있습니다.<br>
                형식은 <strong>테이블등록용 엑셀파일</strong>을 다운로드하여 정보를 입력하시면 됩니다.<br>
                수정 완료 후 엑셀파일을 업로드하시면 상품이 일괄등록됩니다.<br>
                엑셀파일을 저장하실 때는 <strong>Excel 97 - 2003 통합문서 (*.xls)</strong> 로 저장하셔야 합니다.
            </p>

            <p>
                <a href="<?php echo GTM_ADMIN_LIB_URL; ?>/Excel/table_sample_excel.xls">상품일괄등록용 엑셀파일 다운로드</a>
            </p>
        </div>

        <form name="fitemexcel" method="post" action="./tableexcelupdate.php" enctype="MULTIPART/FORM-DATA" autocomplete="off">

            <div class="tbl_frm01 tbl_wrap" style="margin-left:10px;">

                <table>
                    <caption><?php echo $g5['title']; ?></caption>
                    <tbody>
                    <tr>
                        <th scope="row"><label for="table_name">테이블명<strong class="sound_only">필수</strong></label></th>
                        <td>
                            <?php echo help("테이블명은 영문만 가능합니다. 최대 30자만 가능합니다.") ?>
                            <input type="text" name="table_name" value="" required id="table_name" class="required frm_input" size="30" maxlength="30">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="memo">메모</label></th>
                        <td>
                            <input type="text" name="memo" value="" id="memo" class="frm_input" size="50">
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <div id="excelfile_upload">
                <label for="excelfile">파일선택</label>
                <input type="file" name="excelfile" id="excelfile">
            </div>

            <div class="win_btn btn_confirm">
                <input type="submit" value="상품 엑셀파일 등록" class="btn_submit btn">
                <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
            </div>

        </form>

    </div>

<?php
include_once(G5_PATH.'/tail.sub.php');