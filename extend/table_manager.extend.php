<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/*
테이블 관리자 페이지
*/
define('GTM_ADMIN_DIR', 'table_admin');
define('GTM_ADMIN_URL', G5_ADMIN_URL.'/'.GTM_ADMIN_DIR.'');
define('GTM_ADMIN_PATH', G5_ADMIN_PATH.'/'.GTM_ADMIN_DIR.'');
/*
만들기 메뉴
*/
define('GTM_ADMIN_MAKE_DIR', 'make');
define('GTM_ADMIN_MAKE_URL', GTM_ADMIN_URL.'/'.GTM_ADMIN_MAKE_DIR.'');
define('GTM_ADMIN_MAKE_PATH', GTM_ADMIN_PATH.'/'.GTM_ADMIN_MAKE_DIR.'');