<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/*
 * 테이블 관리자 페이지
 */
define('GTM_ADMIN_DIR', 'table_admin');
define('GTM_ADMIN_URL', G5_ADMIN_URL.'/'.GTM_ADMIN_DIR.'');
define('GTM_ADMIN_PATH', G5_ADMIN_PATH.'/'.GTM_ADMIN_DIR.'');

define('GTM_ADMIN_LIB_URL', GTM_ADMIN_URL.'/lib');
define('GTM_ADMIN_LIB_PATH', GTM_ADMIN_PATH.'/lib');

/*
 * 테이블 메뉴
 */
define('GTM_ADMIN_MAKE_TABLE_DIR', 'table');
define('GTM_ADMIN_MAKE_TABLE_URL', GTM_ADMIN_URL.'/'.GTM_ADMIN_MAKE_TABLE_DIR.'');
define('GTM_ADMIN_MAKE_TABLE_PATH', GTM_ADMIN_PATH.'/'.GTM_ADMIN_MAKE_TABLE_DIR.'');

/*
 * 컬럼 메뉴
 */
define('GTM_ADMIN_MAKE_COLUMN_DIR', 'column');
define('GTM_ADMIN_MAKE_COLUMN_URL', GTM_ADMIN_URL.'/'.GTM_ADMIN_MAKE_COLUMN_DIR.'');
define('GTM_ADMIN_MAKE_COLUMN_PATH', GTM_ADMIN_PATH.'/'.GTM_ADMIN_MAKE_COLUMN_DIR.'');
/*
 * 뷰어 메뉴
 */
define('GTM_ADMIN_MAKE_VIEWER_DIR', 'viewer');
define('GTM_ADMIN_TABLE_VIEWER_URL', GTM_ADMIN_URL.'/'.GTM_ADMIN_MAKE_VIEWER_DIR.'');
define('GTM_ADMIN_TABLE_VIEWER_PATH', GTM_ADMIN_PATH.'/'.GTM_ADMIN_MAKE_VIEWER_DIR.'');


$gtm_config = array();
$gtm = array();