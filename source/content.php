<?php
defined("BASEPATH") or exit("No direct access allowed");
$menu = clear(isset($_GET['menu']) ? $_GET['menu'] : '');

$getmenu = mysqli_query($con, "SELECT * FROM menu WHERE delete_at IS NULL");
$cekakses = mysqli_query($con, "SELECT menulevel.menu, menu.modul FROM menulevel 
                                LEFT JOIN menu ON menulevel.menu = menu.uuid
                                WHERE menulevel.level = '" . $_SESSION['levelaktif']['level'] . "'");
$allmenu = array();
$allowmenu = array();
while ($al = mysqli_fetch_array($getmenu)) {
    array_push($allmenu, $al['modul']);
}
while ($am = mysqli_fetch_array($cekakses)) {
    array_push($allowmenu, $am['modul']);
}
if ($menu != "") {
    if (in_array($menu, $allmenu)) {
        if (in_array($menu, $allowmenu)) {
            include "modul/".$menu.".php";
        } else {
            include "modul/access.php";
        }
    } else {
        include "modul/notfound.php";
    }
} else {
    include "modul/default.php";
}
