<?php
defined("BASEPATH") or exit("No direct access allowed");

use \Gumlet\ImageResize;


$aksi = clear(isset($_GET['aksi']) ? $_GET['aksi'] : '');
$proses = clear(isset($_POST['proses']) ? $_POST['proses'] : '');
$team = clear(isset($_GET['team']) ? $_GET['team'] : '');
$teams = clear(isset($_POST['team']) ? $_POST['team'] : '');
$prosesrps = clear(isset($_GET['prosesrps']) ? $_GET['prosesrps'] : '');

/* ****************** **
** FUNGSI TAMPIL DATA **
** ****************** */
function tampildata($con, $team, $prosesrps)
{
    $query = mysqli_query($con, "SELECT * FROM team WHERE uuid = '$team'");
    $cek = mysqli_num_rows($query);
    if ($cek > 0) {
        $data = mysqli_fetch_array($query);
        $queryrps = mysqli_query($con, "SELECT * FROM rps WHERE periode='" . $data['periode'] . "' AND prodi='" . $data['prodi'] . "' AND matakuliah='" . $data['matakuliah'] . "' ");
        $prodi = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM prodi WHERE uuid='" . $data['prodi'] . "'"));
        $matakuliah = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM matakuliah WHERE uuid='" . $data['matakuliah'] . "'"));
        $anggota = mysqli_query($con, "SELECT team.*, user.nama as nama_user FROM team 
                                        LEFT JOIN user ON team.user = user.uuid
                                        WHERE team.periode = '" . $data['periode'] . "' AND team.prodi = '" . $data['prodi'] . "' AND team.matakuliah = '" . $data['matakuliah'] . "'");
        $totalanggota = mysqli_num_rows($anggota);
        $cekrps = mysqli_num_rows($queryrps);
        echo '<ul class="nav nav-pills flex-column flex-md-row pt-3 mb-3">
                <li class="nav-item">
                    <a class="nav-link ' . ($prosesrps == 'lihat' ? 'active' : '') . '" href="index.php?menu=userrps&team=' . $team . '&prosesrps=lihat"><i class="bx bx-user me-1"></i> Lihat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ' . ($prosesrps == 'edit' ? 'active' : '') . '" href="index.php?menu=userrps&team=' . $team . '&prosesrps=edit"><i class="bx bx-link-alt me-1"></i> Edit</a>
                </li>
            </ul>';
        if ($cekrps > 0) {
            //update
            $rps = mysqli_fetch_array($queryrps);

            echo '<div id="printableArea"><h4 class="text-center pt-2">Rencana Pembelajaran Semester (RPS)<br><span>Mata Kuliah ' . $matakuliah['nama'] . '</span></h4><div class="table-responsive"><table class="table">
            <tr><th style="width:40px">1.</th><th style="width:300px">Nama Program Studi</th><td style="width:20px">:</td><td>' . $prodi['nama'] . '</td></tr>
            <tr><th>2.</th><th>Nama Matakuliah</th><td>:</td><td>' . $matakuliah['nama'] . '</td></tr>
            <tr><th>3.</th><th>Kode</th><td>:</td><td>' . $matakuliah['kode'] . '</td></tr>
            <tr><th>4.</th><th>Semester</th><td>:</td><td>' . $matakuliah['semester'] . '</td></tr>
            <tr><th>5.</th><th>S K S</th><td>:</td><td>' . $matakuliah['sks'] . '(' . $matakuliah['skst'] . ',' . $matakuliah['sksp'] . ')</td></tr>
            <tr><th>6.</th><th>Nama Dosen Pengampu</th><td>:</td><td>';
            $no = 1;
            while ($n = mysqli_fetch_array($anggota)) {
                if ($no == $totalanggota) {
                    echo $no . '. ' . $n['nama_user'];
                } else {
                    echo $no . '. ' . $n['nama_user'] . '<br>';
                }
                $no++;
            }
            echo '</td></tr>';
            if ($prosesrps == 'edit') {
                echo '<tr><th>7.</th><th colspan=3>Deskripsi Singkat Mata Kuliah</th></tr>';
                echo '<tr><td colspan=4><textarea class="form-control mytextarea" id="deskripsi" name="deskripsi" placeholder="Deskripsi Singkat Mata Kuliah" style="height: 520px;">' . $rps['deskripsi'] . '</textarea></td></tr>';
                echo '<tr><th>8.</th><th colspan=3>Capaian Pembelajaran Lulusan (CPL)</th></tr>';
                echo '<tr><td colspan=4><textarea class="form-control mytextarea" id="capaian" name="capaian" placeholder="Capaian Pembelajaran Lulusan (CPL)" style="height: 520px;">' . $rps['capaian'] . '</textarea></td></tr>';
                echo '<tr><th>9.</th><th colspan=3>Bobot penilaian Akhir</th></tr>';
                echo '<tr><td colspan=4><textarea class="form-control mytextarea" id="bobot" name="bobot" placeholder="Bobot penilaian Akhir" style="height: 520px;">' . $rps['bobot'] . '</textarea></td></tr>';
                echo '<tr><th>10.</th><th colspan=3>Rencana Kegiatan Tahapan Pembelajaran</th></tr>';
                echo '<input type="hidden" name="rps" value="' . $rps['uuid'] . '">';
                echo '<input type="hidden" name="team" value="' . $team . '">';
                echo '</table></div><br>';
                echo '<div class="table-responsive tabledatarps">';
                tampiltabel($con, $rps['uuid']);
                echo '</div>';
            } else {
                echo '<tr><th>7.</th><th colspan=3>Deskripsi Singkat Mata Kuliah</th></tr>';
                echo '<tr><td colspan=4>' . cetakHTML($rps['deskripsi']) . '</td></tr>';
                echo '<tr><th>8.</th><th colspan=3>Capaian Pembelajaran Lulusan (CPL)</th></tr>';
                echo '<tr><td colspan=4>' . cetakHTML($rps['capaian']) . '</td></tr>';
                echo '<tr><th>9.</th><th colspan=3>Bobot penilaian Akhir</th></tr>';
                echo '<tr><td colspan=4>' . cetakHTML($rps['bobot']) . '</td></tr>';
                echo '<tr><th>10.</th><th colspan=3>Rencana Kegiatan Tahapan Pembelajaran</th></tr>';
                echo '</table></div><br>';
                echo '<div class="table-responsive tabledatarps">';
                tampiltabel($con, $rps['uuid']);
                echo '</div>';
            }
            echo '</div>';
        } else {
            //create
            $user = $_SESSION['uuid'];
            $date = date('Y-m-d h:i:s');
            mysqli_query($con, "INSERT INTO rps (uuid,periode,prodi,matakuliah,create_at,create_by) VALUES
                                (UUID(),'" . $data['periode'] . "','" . $data['prodi'] . "','" . $data['matakuliah'] . "','" . $date . "','" . $user . "')");
            $rps = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM rps WHERE periode='" . $data['periode'] . "' AND prodi='" . $data['prodi'] . "' AND matakuliah='" . $data['matakuliah'] . "' "));
            //rpm
            $rpm = mysqli_query($con, "SELECT * FROM pertemuan WHERE delete_at IS NULL");
            while ($r = mysqli_fetch_array($rpm)) {
                mysqli_query($con, "INSERT INTO rpm (uuid,rps,pertemuan, create_at,create_by) VALUES
                                (UUID(),'" . $rps['uuid'] . "','" . $r['uuid'] . "','" . $date . "','" . $user . "')");
            }

            echo '<div id="printableArea"><h4 class="text-center pt-2">Rencana Pembelajaran Semester (RPS)<br><span>Mata Kuliah ' . $matakuliah['nama'] . '</span></h4><div class="table-responsive"><table class="table">
                    <tr><th style="width:50px">1.</th><th style="width:300px">Nama Program Studi</th><td style="width:20px">:</td><td>' . $prodi['nama'] . '</td></tr>
                    <tr><th>2.</th><th>Nama Matakuliah</th><td>:</td><td>' . $matakuliah['nama'] . '</td></tr>
                    <tr><th>3.</th><th>Kode</th><td>:</td><td>' . $matakuliah['kode'] . '</td></tr>
                    <tr><th>4.</th><th>Semester</th><td>:</td><td>' . $matakuliah['semester'] . '</td></tr>
                    <tr><th>5.</th><th>S K S</th><td>:</td><td>' . $matakuliah['sks'] . '(' . $matakuliah['skst'] . ',' . $matakuliah['sksp'] . ')</td></tr>
                    <tr><th>6.</th><th>Nama Dosen Pengampu</th><td>:</td><td>';
            $no = 1;
            while ($n = mysqli_fetch_array($anggota)) {
                if ($no == $totalanggota) {
                    echo $no . '. ' . $n['nama_user'];
                } else {
                    echo $no . '. ' . $n['nama_user'] . '<br>';
                }
                $no++;
            }
            echo '</td></tr>';
            if ($prosesrps == 'edit') {
                echo '<tr><th>7.</th><th colspan=3>Deskripsi Singkat Mata Kuliah</th></tr>';
                echo '<tr><td colspan=4><textarea class="form-control mytextarea" id="deskripsi" name="deskripsi" placeholder="Deskripsi Singkat Mata Kuliah" style="height: 520px;">' . $rps['deskripsi'] . '</textarea></td></tr>';
                echo '<tr><th>8.</th><th colspan=3>Capaian Pembelajaran Lulusan (CPL)</th></tr>';
                echo '<tr><td colspan=4><textarea class="form-control mytextarea" id="capaian" name="capaian" placeholder="Capaian Pembelajaran Lulusan (CPL)" style="height: 520px;">' . $rps['capaian'] . '</textarea></td></tr>';
                echo '<tr><th>9.</th><th colspan=3>Bobot penilaian Akhir</th></tr>';
                echo '<tr><td colspan=4><textarea class="form-control mytextarea" id="bobot" name="bobot" placeholder="Bobot penilaian Akhir" style="height: 520px;">' . $rps['bobot'] . '</textarea></td></tr>';

                echo '<input type="hidden" name="rps" value="' . $rps['uuid'] . '">';
                echo '<input type="hidden" name="team" value="' . $team . '">';
                echo '</table></div><br>';
                echo '<div class="table-responsive tabledatarps">';
                tampiltabel($con, $rps['uuid']);
                echo '</div>';
            } else {
                echo '<tr><th>7.</th><th colspan=3>Deskripsi Singkat Mata Kuliah</th></tr>';
                echo '<tr><td colspan=4>' . cetakHTML($rps['deskripsi']) . '</td></tr>';
                echo '<tr><th>8.</th><th colspan=3>Capaian Pembelajaran Lulusan (CPL)</th></tr>';
                echo '<tr><td colspan=4>' . cetakHTML($rps['capaian']) . '</td></tr>';
                echo '<tr><th>9.</th><th colspan=3>Bobot penilaian Akhir</th></tr>';
                echo '<tr><td colspan=4>' . cetakHTML($rps['bobot']) . '</td></tr>';
                echo '</table></div><br>';
                echo '<div class="table-responsive tabledatarps">';
                tampiltabel($con, $rps['uuid']);
                echo '</div>';
            }
            echo '</div>';
        }
    }
}

function tampiltabel($con, $uuidrps)
{
    $pertemuan = mysqli_query($con, "SELECT * FROM pertemuan");
    echo '<table class="table table-bordered table-rps">
                <tr class="text-center align-middle"><th rowspan=2  style="padding:0px;margin:0px">MGU</th><th rowspan=2>Kemampuan Akhir Tahap Pembelajaran</th>
                <th rowspan=2>Bahan Kajian (Materi Pembelajaran)</th><th rowspan=2>Metode Pembelajaran</th>
                <th rowspan=2>Alokasi Waktu</th><th rowspan=2>Ket</th><th rowspan=2>Pengalaman Belajar (Deskripsi Tugas)</th>
                <th colspan=3>Penilaian</th><th rowspan=2>Ref</th><th rowspan=2 class="mx-0 my-0" style="padding:0px">#</th></tr>
                <tr><th>Kriteria</th><th>Indikator Ketercapaian</th><th>Bobot (per tahapan)</th></tr>
                <tr class="text-center align-middle"><td  style="padding:0px;margin:0px"><i>(1)</i></td><td><i>(2)</i></td><td><i>(3)</i></td><td><i>(4)</i></td><td><i>(5)</i></td>
                <td><i>(6)</i></td><td><i>(7)</i></td><td><i>(8)</i></td><td><i>(9)</i></td><td><i>(10)</i></td><td><i>(11)</i></td><td style="padding:0px;margin:0px">&nbsp;</td></tr>';
    while ($p = mysqli_fetch_array($pertemuan)) {
        $rpm = mysqli_fetch_array(mysqli_query($con, "SELECT rpm.*, pertemuan.nama as nama_pertemuan FROM rpm LEFT JOIN pertemuan ON rpm.pertemuan = pertemuan.uuid WHERE rpm.rps='" . $uuidrps . "' AND rpm.pertemuan='" . $p['uuid'] . "' AND rpm.delete_at IS NULL"));
        $paramakses = htmlspecialchars(json_encode($rpm));
        if ($p['setting'] == "UTS") {
            echo "<tr class='align-top bg-secondary text-white'><td>$p[kode]</td><td>UTS</td><td>" . cetakHTML($rpm['bahan']) . "</td><td>" . cetakHTML($rpm['metode1']) . "</td><td>" . cetakHTML($rpm['alokasi1']) . "</td>
            <td>" . cetakHTML($rpm['keterangan']) . "</td><td>" . cetakHTML($rpm['tugas']) . "</td><td>" . cetakHTML($rpm['kriteria']) . "</td><td>" . cetakHTML($rpm['indikator']) . "</td>
            <td>" . cetakHTML($rpm['bobot']) . "</td><td>" . cetakHTML($rpm['referensi']) . "</td>
                  <td style='padding:0px'><a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaleditrps'><i class='bx bx-edit-alt me-1'></i></a></td>
                  </tr>";
        } elseif ($p['setting'] == "UAS") {
            echo "<tr class='align-top bg-secondary text-white'><td>$p[kode]</td><td>UAS</td><td>" . cetakHTML($rpm['bahan']) . "</td><td>" . cetakHTML($rpm['metode1']) . "</td><td>" . cetakHTML($rpm['alokasi1']) . "</td>
            <td>" . cetakHTML($rpm['keterangan']) . "</td><td>" . cetakHTML($rpm['tugas']) . "</td><td>" . cetakHTML($rpm['kriteria']) . "</td><td>" . cetakHTML($rpm['indikator']) . "</td>
            <td>" . cetakHTML($rpm['bobot']) . "</td><td>" . cetakHTML($rpm['referensi']) . "</td>
                  <td style='padding:0px'><a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaleditrps'><i class='bx bx-edit-alt me-1'></i></a></td>
                  </tr>";
        } else {
            echo "<tr class='align-top'><td rowspan=3>$p[kode]</td><td rowspan=3>" . cetakHTML($rpm['kemampuan']) . "</td><td rowspan=3>" . cetakHTML($rpm['bahan']) . "</td><td>" . cetakHTML($rpm['metode1']) . "</td><td>" . cetakHTML($rpm['alokasi1']) . "</td>
            <td rowspan=3>" . cetakHTML($rpm['keterangan']) . "</td><td rowspan=3>" . cetakHTML($rpm['tugas']) . "</td><td rowspan=3>" . cetakHTML($rpm['kriteria']) . "</td><td rowspan=3>" . cetakHTML($rpm['indikator']) . "</td>
            <td rowspan=3>" . cetakHTML($rpm['bobot']) . "</td><td rowspan=3>" . cetakHTML($rpm['referensi']) . "</td>
                  <td style='padding:0px;margin:0px' rowspan=3><a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaleditrps'><i class='bx bx-edit-alt me-1'></i></a></td>
                  </tr>";
            echo "<tr><td>" . cetakHTML($rpm['metode2']) . "</td><td>" . cetakHTML($rpm['alokasi2']) . "</td></tr>";
            echo "<tr><td>" . cetakHTML($rpm['metode3']) . "</td><td>" . cetakHTML($rpm['alokasi3']) . "</td></tr>";
        }
    }
    echo '</table>';
}

if ($aksi == "tambah") {
    if ($proses == "updateuserlevel") {
        $rpss = clear(isset($_POST['rps']) ? $_POST['rps'] : '');
        $deskripsi = clear(isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '');
        $capaian = clear(isset($_POST['capaian']) ? $_POST['capaian'] : '');
        $bobot = clear(isset($_POST['bobot']) ? $_POST['bobot'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "UPDATE rps SET deskripsi = '" . $deskripsi . "', capaian = '" . $capaian . "', bobot='" . $bobot . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid='" . $rpss . "' ");
        echo '<div class="alert alert-primary alert-dismissible" role="alert">
        Sukses Update RPS!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
      </div>';
    }
    tampildata($con, $team, "edit");
} elseif ($aksi == "edit") {
    $uuidrps = "";
    if ($proses == "editrps") {
        // var_dump($_POST);
        $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
        $rps = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM rpm WHERE uuid='" . $uuid . "'"));
        $uuidrps = $rps['rps'];

        $kemampuan = clear(isset($_POST['kemampuan']) ? $_POST['kemampuan'] : '');
        $bahan = clear(isset($_POST['bahan']) ? $_POST['bahan'] : '');
        $metode1 = clear(isset($_POST['metode1']) ? $_POST['metode1'] : '');
        $metode2 = clear(isset($_POST['metode2']) ? $_POST['metode2'] : '');
        $metode3 = clear(isset($_POST['metode3']) ? $_POST['metode3'] : '');
        $alokasi1 = clear(isset($_POST['alokasi1']) ? $_POST['alokasi1'] : '');
        $alokasi2 = clear(isset($_POST['alokasi2']) ? $_POST['alokasi2'] : '');
        $alokasi3 = clear(isset($_POST['alokasi3']) ? $_POST['alokasi3'] : '');
        $keterangan = clear(isset($_POST['keterangan']) ? $_POST['keterangan'] : '');
        $tugas = clear(isset($_POST['tugas']) ? $_POST['tugas'] : '');
        $kriteria = clear(isset($_POST['kriteria']) ? $_POST['kriteria'] : '');
        $indikator = clear(isset($_POST['indikator']) ? $_POST['indikator'] : '');
        $bobot = clear(isset($_POST['bobot']) ? $_POST['bobot'] : '');
        $referensi = clear(isset($_POST['referensi']) ? $_POST['referensi'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "UPDATE rpm SET kemampuan = '" . $kemampuan . "', 
                                           bahan = '" . $bahan . "', 
                                           metode1='" . $metode1 . "',
                                           metode2='" . $metode2 . "',
                                           metode3='" . $metode3 . "',
                                           alokasi1='" . $alokasi1 . "',
                                           alokasi2='" . $alokasi2 . "',
                                           alokasi3='" . $alokasi3 . "',
                                           keterangan='" . $keterangan . "',
                                           tugas='" . $tugas . "',
                                           kriteria='" . $kriteria . "',
                                           indikator='" . $indikator . "',
                                           bobot='" . $bobot . "',
                                           referensi='" . $referensi . "',
                                           update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid='" . $uuid . "' ");
    }
    tampiltabel($con, $uuidrps);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-header">Table Manajemen RPS</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                </div>
                <div class="card-body">
                    <?php
                    echo '<form id="proses-data" action="index.php" method="GET">
                                <div class="col-md-12 mb-3">
                                <div class="row">
                                    <div class="mb-3 col-md-10">   
                                    <select class="form-control" id="team" name="team" placeholder="Pilih Matakuliah..." required>';
                    $matakuliah = mysqli_query($con, "SELECT team.*, matakuliah.nama as matakuliah_nama, matakuliah.semester as matakuliah_semester, 
                                                    matakuliah.kode as matakuliah_kode, prodi.nama as prodi_nama, periode.nama as periode_nama
                                                    FROM team 
                                                    LEFT JOIN matakuliah ON team.matakuliah = matakuliah.uuid
                                                    LEFT JOIN periode ON  team.periode = periode.uuid
                                                    LEFT JOIN prodi ON team.prodi = prodi.uuid
                                                    WHERE team.user='" . $_SESSION['uuid'] . "' ");
                    echo "<option value=''>Pilih Matakuliah...</option>";
                    while ($a = mysqli_fetch_array($matakuliah)) {
                        if ($a['uuid'] == $team) {
                            echo '<option value="' . $a['uuid'] . '" selected></i> ' . $a['matakuliah_kode'] . ' ' . $a['matakuliah_nama'] . ' (smt:' . $a['matakuliah_semester'] . ') ' . $a['prodi_nama'] . ' - ' . $a['periode_nama'] . '</option>';
                        } else {
                            echo '<option value="' . $a['uuid'] . '"></i> ' . $a['matakuliah_kode'] . ' ' . $a['matakuliah_nama'] . ' (smt:' . $a['matakuliah_semester'] . ') ' . $a['prodi_nama'] . ' - ' . $a['periode_nama'] . '</option>';
                        }
                    }

                    echo '</select></div>
                                    <div class="mb-3 col-md-2">
                                    <input type="hidden" name="menu" value="userrps">  
                                    <div class="">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="submit" class="btn btn-primary" name="prosesrps" value="lihat"><span id="proses"></span>Proses</button>
                                        <input class="btn btn-success" type="button" onclick="printDiv(\'printableArea\')" value="Cetak" />
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                </div>
                        </form>';

                    if (isset($_GET['prosesrps']) && $team != "") {
                        if ($prosesrps == "edit") {
                            echo '<form id="formuserlevel" class="mb-3" action="" method="POST">';
                            echo '<div class="table tabeluserlevel">';
                            tampildata($con, $team, $prosesrps);
                            echo '</div>';
                            echo '<input type="hidden" name="proses" value="updateuserlevel">';
                            // echo '<input type="hidden" name="team" value="' . $team . '">';
                            echo '<input type="hidden" name="prosesrps" value="edit">';
                            echo '<button type="button" class="btn bg-primary text-white mt-4 float-end" id="tomboluserlevel">
                             <span id="loadingleveluser"></span> &nbsp; Update RPS</button>';
                            echo '</form>';
                        } else {
                            echo '<div class="table tabeluserlevel">';
                            tampildata($con, $team, $prosesrps);
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT DATA RPS -->
    <div class="modal fade" id="modaleditrps" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <form id="formeditrps" action="?method=ajax&menu=userrps&aksi=edit" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modaleditmediatitle">Edit Data RPS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nama" class="form-label">KEMAMPUAN AKHIR TAHAP PEMBELAJARAN</label>
                            <textarea class="form-control mytextarea" id="kemampuan-edit" name="kemampuan" placeholder="Kemampuan Akhir Tahap Pembelajaran" style="height: 420px;" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nama" class="form-label">BAHAN KAJIAN (MATERI PEMBELAJARAN)</label>
                            <textarea class="form-control mytextarea" id="bahan-edit" name="bahan" placeholder="Bahan Kajian (Materi Pembelajaran)" style="height: 420px;" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="nama" class="form-label">METODE PEMBELAJARAN</label>
                            <textarea class="form-control mytextarea" id="metode1-edit" name="metode1" placeholder="Kuliah Tatap Muka" style="height: 150px;" required>Kuliah Tatap Muka</textarea><br>
                            <textarea class="form-control mytextarea" id="metode2-edit" name="metode2" placeholder="Penugasan Terstruktur" style="height: 150px;" required>Penugasan Terstruktur</textarea><br>
                            <textarea class="form-control mytextarea" id="metode3-edit" name="metode3" placeholder="Kegiatan Mandiri" style="height: 150px;" required>Kegiatan Mandiri</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">ALOKASI WAKTU</label>
                            <textarea class="form-control mytextarea" id="alokasi1-edit" name="alokasi1" placeholder="Alokasi Waktu Kuliah Tatap Muka" style="height: 150px;" required></textarea><br>
                            <textarea class="form-control mytextarea" id="alokasi2-edit" name="alokasi2" placeholder="Alokasi Waktu Penugasan Terstruktur" style="height: 150px;" required></textarea><br>
                            <textarea class="form-control mytextarea" id="alokasi3-edit" name="alokasi3" placeholder="Alokasi Waktu Kegiatan Mandiri" style="height: 150px;" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nama" class="form-label">KETERANGAN</label>
                            <textarea class="form-control mytextarea" id="keterangan-edit" name="keterangan" placeholder="Keterangan" style="height: 420px;" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nama" class="form-label">PENGALAMAN BELAJAR (DESKRIPSI TUGAS)</label>
                            <textarea class="form-control mytextarea" id="tugas-edit" name="tugas" placeholder="Pengalaman Belajar (Deskripsi Tugas)" style="height: 420px;" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nama" class="form-label">Kriteria</label>
                            <textarea class="form-control mytextarea" id="kriteria-edit" name="kriteria" placeholder="Kriteria" style="height: 420px;" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nama" class="form-label">Indikator Ketercapaian</label>
                            <textarea class="form-control mytextarea" id="indikator-edit" name="indikator" placeholder="Indikator Ketercapaian" style="height: 420px;" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nama" class="form-label">Bobot (pertahapan)</label>
                            <textarea class="form-control mytextarea" id="bobot-edit" name="bobot" placeholder="Bobot (pertahapan)" style="height: 420px;" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nama" class="form-label">Referensi</label>
                            <textarea class="form-control mytextarea" id="referensi-edit" name="referensi" placeholder="Referensi" style="height: 420px;" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="proses" value="editrps">
                    <input type="hidden" name="uuid" id="uuid" value="">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboleditrps">
                        <span id="loadingeditrps"></span> &nbsp; Edit RPS</button>
                </div>
            </form>
        </div>
    </div>
    <!-- MODAL EDIT DATA RPS -->

    <script>
        $(document).ready(function() {
            tinymce.init({
                selector: '.mytextarea',
                menubar: false,
                toolbar: "bold italic underline",
                plugins: ['powerpaste','paste'],
                images_upload_url: 'acceptor.php',
                images_dataimg_filter: function(img) {
                    return !img.hasAttribute('internal-blob'); // blocks the upload of <img> elements with the attribute "internal-blob".
                },
                image_title: true,
                automatic_uploads: true,
                file_picker_types: 'image',
                file_picker_callback: function(cb, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.onchange = function() {
                        var file = this.files[0];
                        var reader = new FileReader();
                        reader.onload = function() {
                            var id = 'blobid' + (new Date()).getTime();
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);
                            cb(blobInfo.blobUri(), {
                                title: file.name
                            });
                        };
                        reader.readAsDataURL(file);
                    };

                    input.click();
                },
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
            });

            $('#body').on('click', '#tomboluserlevel', function(e) {
                e.preventDefault();
                tinyMCE.triggerSave();
                var xhr;
                if (xhr && xhr.readystate != 4) {
                    xhr.abort();
                }
                $('#loadingleveluser').append(
                    "<div class='spinner-border spinner-border-sm text-white' role='status'><span class='visually-hidden'>Loading...</span></div>"
                );
                $('#tomboluserlevel').prop('disabled', true);
                xhr = $.ajax({
                    type: "POST",
                    url: 'index.php?method=ajax&menu=userrps&prosesrps=edit&aksi=tambah&team=<?php echo $team; ?>',
                    data: new FormData(document.getElementById('formuserlevel')),
                    // data: $('#formuserlevel').serialize(),
                    processData: false,
                    cache: false,
                    contentType: false,
                    success: function(response) {
                        $('#loadingleveluser').html('');
                        Swal.fire(
                            'Sukses!',
                            'Sukses Update Level Dosen',
                            'success'
                        );
                        $('#tomboluserlevel').prop('disabled', false);
                        $('.tabeluserlevel').html(response);
                        tinyMCE.get('deskripsi').remove();
                        tinyMCE.get('capaian').remove();
                        tinyMCE.get('bobot').remove();
                        tinyMCE.execCommand('mceAddEditor', false, 'deskripsi');
                        tinyMCE.execCommand('mceAddEditor', false, 'capaian');
                        tinyMCE.execCommand('mceAddEditor', false, 'bobot');
                    },
                    error: function() {
                        $('#loadingleveluser').html('');
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        });
                        $('#tomboluserlevel').prop('disabled', false);
                    }
                });
                return false;
            });

            $('#body').on('click', '#tomboleditrps', function(e) {
                e.preventDefault();
                tinyMCE.triggerSave();
                var xhr;
                if (xhr && xhr.readystate != 4) {
                    xhr.abort();
                }
                $('#loadingeditrps').append(
                    "<div class='spinner-border spinner-border-sm text-white' role='status'><span class='visually-hidden'>Loading...</span></div>"
                );
                $('#tomboleditrps').prop('disabled', true);
                xhr = $.ajax({
                    type: "POST",
                    url: 'index.php?method=ajax&menu=userrps&aksi=edit',
                    data: new FormData(document.getElementById('formeditrps')),
                    // data: $('#formuserlevel').serialize(),
                    processData: false,
                    cache: false,
                    contentType: false,
                    success: function(response) {
                        $('#loadingeditrps').html('');
                        Swal.fire(
                            'Sukses!',
                            'Sukses Update Level Dosen',
                            'success'
                        );
                        $('#modaleditrps').modal('toggle');
                        $('#tomboleditrps').prop('disabled', false);
                        $('.tabledatarps').html(response);
                        tinymce.get("kemampuan-edit").remove();
                        tinymce.get("bahan-edit").remove();
                        tinymce.get("metode1-edit").remove();
                        tinymce.get("metode2-edit").remove();
                        tinymce.get("metode3-edit").remove();
                        tinymce.get("alokasi1-edit").remove();
                        tinymce.get("alokasi2-edit").remove();
                        tinymce.get("alokasi3-edit").remove();
                        tinymce.get("keterangan-edit").remove();
                        tinymce.get("tugas-edit").remove();
                        tinymce.get("kriteria-edit").remove();
                        tinymce.get("indikator-edit").remove();
                        tinymce.get("bobot-edit").remove();
                        tinymce.get("referensi-edit").remove();
                        tinyMCE.execCommand('mceAddEditor', false, 'kemampuan-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'bahan-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'metode1-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'metode2-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'metode3-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'alokasi1-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'alokasi2-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'alokasi3-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'keterangan-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'tugas-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'kriteria-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'indikator-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'bobot-edit');
                        tinyMCE.execCommand('mceAddEditor', false, 'referensi-edit');

                    },
                    error: function() {
                        $('#loadingeditrps').html('');
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        });
                        $('#modaleditrps').modal('toggle');
                        $('#tomboleditrps').prop('disabled', false);
                    }
                });
                return false;
            });

            var modaleditrps = document.getElementById('modaleditrps');
            modaleditrps.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget
                var modal_data = JSON.parse(button.getAttribute('data-bs-val'))
                var modal_title = modaleditrps.querySelector('.modal-title')
                var kemampuan = modaleditrps.querySelector('.modal-body #kemampuan-edit')
                var bahan = modaleditrps.querySelector('.modal-body #bahan-edit')
                var metode1 = modaleditrps.querySelector('.modal-body #metode1-edit')
                var metode2 = modaleditrps.querySelector('.modal-body #metode2-edit')
                var metode3 = modaleditrps.querySelector('.modal-body #metode3-edit')
                var alokasi1 = modaleditrps.querySelector('.modal-body #alokasi1-edit')
                var alokasi2 = modaleditrps.querySelector('.modal-body #alokasi2-edit')
                var alokasi3 = modaleditrps.querySelector('.modal-body #alokasi3-edit')
                var keterangan = modaleditrps.querySelector('.modal-body #keterangan-edit')
                var tugas = modaleditrps.querySelector('.modal-body #tugas-edit')
                var kriteria = modaleditrps.querySelector('.modal-body #kriteria-edit')
                var indikator = modaleditrps.querySelector('.modal-body #indikator-edit')
                var bobot = modaleditrps.querySelector('.modal-body #bobot-edit')
                var referensi = modaleditrps.querySelector('.modal-body #referensi-edit')
                var uuid = modaleditrps.querySelector('.modal-footer #uuid')
                uuid.value = modal_data['uuid']
                var tombolmodal = modaleditrps.querySelector(
                    '.modal-footer button[id=tomboleditrps]')
                modal_title.textContent = 'Edit RPS : ' + $.htmlentities.decode(modal_data['nama_pertemuan'])
                tinymce.get("kemampuan-edit").setContent($.htmlentities.decode(modal_data['kemampuan']));
                tinymce.get("bahan-edit").setContent($.htmlentities.decode(modal_data['bahan']));
                tinymce.get("metode1-edit").setContent($.htmlentities.decode(modal_data['metode1']));
                tinymce.get("metode2-edit").setContent($.htmlentities.decode(modal_data['metode2']));
                tinymce.get("metode3-edit").setContent($.htmlentities.decode(modal_data['metode3']));
                tinymce.get("alokasi1-edit").setContent($.htmlentities.decode(modal_data['alokasi1']));
                tinymce.get("alokasi2-edit").setContent($.htmlentities.decode(modal_data['alokasi2']));
                tinymce.get("alokasi3-edit").setContent($.htmlentities.decode(modal_data['alokasi3']));
                tinymce.get("keterangan-edit").setContent($.htmlentities.decode(modal_data['keterangan']));
                tinymce.get("tugas-edit").setContent($.htmlentities.decode(modal_data['tugas']));
                tinymce.get("kriteria-edit").setContent($.htmlentities.decode(modal_data['kriteria']));
                tinymce.get("indikator-edit").setContent($.htmlentities.decode(modal_data['indikator']));
                tinymce.get("bobot-edit").setContent($.htmlentities.decode(modal_data['bobot']));
                tinymce.get("referensi-edit").setContent($.htmlentities.decode(modal_data['referensi']));
            })

        });
    </script>
<?php
}
?>