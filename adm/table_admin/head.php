<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once (G5_ADMIN_PATH."/table_admin/config.php");
include_once (GTM_ADMIN_PATH."/dbconfig.php");
include_once (GTM_ADMIN_PATH."/lib/common.lib.php");
include_once (GTM_ADMIN_PATH."/lib/database.lib.php");
include_once (GTM_ADMIN_PATH."/lib/table.lib.php");
include_once (GTM_ADMIN_PATH."/lib/column.lib.php");
include_once (GTM_ADMIN_PATH."/lib/user_table.lib.php");

include_once(G5_ADMIN_PATH.'/admin.lib.php');

add_javascript('<script src="'.GTM_ADMIN_URL.'/config.js"></script>', 1);
add_javascript('<script src="'.GTM_ADMIN_URL.'/js/common.js"></script>', 2);
add_javascript('<script>
var gtm_admin_url = "'.GTM_ADMIN_URL.'";
var gtm_admin_ajax_url = "'.GTM_ADMIN_URL.'/ajax";
</script>', 2);
