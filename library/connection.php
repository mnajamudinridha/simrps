<?php
defined("BASEPATH") or exit("No direct access allowed");
use Dotenv\Dotenv;
$base = str_replace("library", "", __DIR__);
$dotenv = Dotenv::create($base);
$dotenv->load();

$boolean = getenv('true_false') === "true";
$server = getenv('db_host');
$username = getenv('db_user');
$password = getenv('db_pass');
$database = getenv('db_db');
$con = mysqli_connect($server, $username, $password, $database);
if (mysqli_connect_errno()) {
            echo 'Koneksi Gagal di '.mysqli_connect_error();
}
