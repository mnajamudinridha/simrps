<?php
$active = clear(isset($_GET['menu']) ? $_GET['menu'] : '');
if ($active == '') {
    echo '<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard</span></h4>';
    // echo '<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forms/</span> Vertical Layouts</h4>';
}else{
    $datamenu = mysqli_query($con, "SELECT * FROM menu WHERE modul = '$active' LIMIT 1");
    $cekmenu = mysqli_num_rows($datamenu);
    if($cekmenu > 0){
        while($m = mysqli_fetch_array($datamenu)){
            echo '<h4 class="fw-bold py-3 mb-4"><a href="index.php"><span class="text-muted fw-light">Dashboard /</span></a> <i class="tf-icon '.$m['icon'].' bx-sm text-primary me-1"></i> '.$m['nama'].'</h4>';
        }
    }else{
        echo '<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard</span></h4>';
    }
}
?>
