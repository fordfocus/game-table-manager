<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

function getRadioByYN($htmlName, $datas) {
    if (!isset($htmlName) || !isset($datas)) return null;

    $html = '';
    foreach($datas as $key => $val) {
        $html .= ' <input type="radio" name="'.$htmlName.'" value="'.$key.'" id="'.$htmlName.'_'.$key.'"><label for="'.$htmlName.'_'.$key.'">'.$val.'</label>';
    }
    return $html;
}