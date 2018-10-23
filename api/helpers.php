<?php
if (!function_exists('genUid')) {
    function genUid($l = 10)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, $l);
    }
}
