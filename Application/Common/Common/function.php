<?php
/**
 * 项目公共函数
 */


/**
 * (友好方式)打印输出数组内容
 * @param  array $arr 数组
 */
function p($arr){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

/**
 * 当前日期时间
 * 格式如：1991-01-01 14:08:27
 * @return string
 */
function datetime() {
    return date('Y-m-d H:i:s', NOW_TIME);
}
