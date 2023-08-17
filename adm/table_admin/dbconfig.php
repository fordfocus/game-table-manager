<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

define('GTM_TABLE_PREFIX', 'gtm_');
define('GTM_MAKE_TABLE_PREFIX', 'gtm_user_');

/*
 * 테이블 리스트
 */
$gtm['table_list'] = GTM_TABLE_PREFIX.'table_list';
$gtm['table_column_list'] = GTM_TABLE_PREFIX.'table_column_list';
