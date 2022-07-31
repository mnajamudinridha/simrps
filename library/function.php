<?php
defined("BASEPATH") or exit("No direct access allowed");

function saveencode($input)
{
    return strtr(base64_encode($input), '+/=', '-_,');
}
function savedecode($input)
{
    return base64_decode(strtr($input, '-_,', '+/='));
}
function clear($data)
{
    $filter1 = trim($data);
    $filter = stripslashes(strip_tags(htmlspecialchars($filter1, ENT_QUOTES, 'UTF-8')));
    return $filter;
}
function cetakHTML($data){
    // $a = htmlentities($data);
    $b = html_entity_decode($data);
    return $b;
}
?>