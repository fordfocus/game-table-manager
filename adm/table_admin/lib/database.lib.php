<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/**
 * mysql 트랜잭션 시작
 * @param $link
 * @return void
 */
function sql_begin_transaction($link = null) {
    global $g5;
    if(!$link)
        $link = $g5['connect_db'];
    mysqli_begin_transaction($link);
}

/**
 * mysql 트랜잭션 커밋
 * @param $link
 * @return void
 */
function sql_commit_transaction($link = null) {
    global $g5;
    if(!$link)
        $link = $g5['connect_db'];
    mysqli_commit($link);
}

/**
 * mysql 트랜잭션 롤백
 * @param $link
 * @param $flags
 * @return void
 */
function sql_rollback_transaction($link = null, $flags = 0) {
    global $g5;
    if(!$link)
        $link = $g5['connect_db'];
    mysqli_rollback($link, $flags);
}