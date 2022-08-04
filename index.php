<?php
session_start();
define("BASEPATH", dirname(__FILE__));
date_default_timezone_set('Asia/Makassar');
include 'vendor/autoload.php';
include 'library/connection.php';
include 'library/function.php';
include 'vendor/gumlet/php-image-resize/lib/ImageResize.php';

$method = clear(isset($_GET['method']) ? $_GET['method'] : '');
$login = clear(isset($_POST['login']) ? $_POST['login'] : '');
$level = clear(isset($_GET['level']) ? $_GET['level'] : '');
if ($method == "ajax") {
    include "source/content.php";
} elseif ($login == "login") {
    include "library/login.php";
} elseif ($level != "") {
    include "library/login.php";
} else {
?>
    <!DOCTYPE html>
    <html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-free">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title><?php echo getenv('web_title'); ?></title>
        <meta name="description" content="<?php echo getenv('web_desc'); ?>" />
        <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
        <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="assets/css/demo.css" />
        <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" />
        <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" />
        <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.1.0/dist/css/tom-select.css" rel="stylesheet">
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="assets/star/star-rating-svg.css">
        <link rel="stylesheet" type="text/css" href="assets/star/demo.css">
        <style>
            @media print {
                .table-rps th {
                    font-size: 10px;
                }

                .table-rps td {
                    font-size: 10px;
                }

                .table-rps td {
                    padding: 0.625rem 5px;
                    margin: 0px;
                }

                .table-rps th {
                    padding: 0.625rem 5px;
                    margin: 0px;
                }
            }
        </style>
        <script src="assets/vendor/libs/jquery/jquery.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.1.0/dist/js/tom-select.complete.min.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
        <script src="assets/vendor/js/helpers.js"></script>
        <script src="assets/js/config.js"></script>
        <script src="assets/js/library.js?v=5"></script>
        <script src="https://cdn.tiny.cloud/1/mv0mzrchoasedvje0gwovxh0647pzzfwvg27p32yjgn7ii6d/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    </head>

    <body id="body">
        <?php
        if (isset($_SESSION['login'])) {
        ?>
            <div class="layout-wrapper layout-content-navbar">
                <div class="layout-container">
                    <?php include "source/sidebar.php"; ?>
                    <div class="layout-page">
                        <?php include "source/navbar.php"; ?>
                        <div class="content-wrapper">

                            <div class="container-xxl flex-grow-1 container-p-y">
                                <?php include "source/breadcrumb.php"; ?>
                                <?php include "source/notification.php"; ?>
                                <?php include "source/content.php"; ?>
                            </div>
                            <?php include "source/footer.php"; ?>
                            <div class="content-backdrop fade"></div>
                        </div>
                    </div>
                </div>
                <div class="layout-overlay layout-menu-toggle"></div>
            </div>
        <?php
        } else {
            include "source/login.php";
        }
        ?>
        <script src="assets/vendor/libs/popper/popper.js"></script>
        <script src="assets/vendor/js/bootstrap.js"></script>
        <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
        <script src="assets/vendor/js/menu.js"></script>
        <script src="assets/js/main.js"></script>
        <script src="assets/star/jquery.star-rating-svg.js"></script>
        <script async defer src="https://buttons.github.io/buttons.js"></script>
        <?php include "source/script.php"; ?>
    </body>

    </html>
<?php
}
