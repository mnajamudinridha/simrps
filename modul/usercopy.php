<?php
defined("BASEPATH") or exit("No direct access allowed");

use \Gumlet\ImageResize;


$aksi = clear(isset($_GET['aksi']) ? $_GET['aksi'] : '');
$proses = clear(isset($_POST['proses']) ? $_POST['proses'] : '');

/* ****************** **
** FUNGSI TAMPIL DATA **
** ****************** */
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <h4 class="card-header">Table Copy RPS</h4>
            <div class="card-body">
                <p><?php echo getenv('web_desc'); ?></p>
            </div>
            <div class="card-body">
                <?php
                echo '<form id="proses-data" action="index.php?menu=usercopy" method="POST">
                                <div class="col-md-12 mb-3">
                                <div class="row">
                                    <div class="mb-3 col-md-5">
                                       
                                        <select class="form-control" id="matakuliah1" name="matakuliah1" placeholder="Pilih Matakuliah Asal..." required>';
                $matakuliah = mysqli_query($con, "SELECT team.*, matakuliah.nama as matakuliah_nama, matakuliah.semester as matakuliah_semester, 
                                        matakuliah.kode as matakuliah_kode, prodi.nama as prodi_nama, periode.nama as periode_nama
                                        FROM team 
                                        LEFT JOIN matakuliah ON team.matakuliah = matakuliah.uuid
                                        LEFT JOIN periode ON  team.periode = periode.uuid
                                        LEFT JOIN prodi ON team.prodi = prodi.uuid GROUP BY matakuliah.uuid ");
                echo "<option value=''>Pilih Matakuliah Asal...</option>";
                while ($a = mysqli_fetch_array($matakuliah)) {
                    echo '<option value="' . $a['uuid'] . '"></i> ' . $a['matakuliah_kode'] . ' ' . $a['matakuliah_nama'] . ' (smt:' . $a['matakuliah_semester'] . ') ' . $a['prodi_nama'] . ' - ' . $a['periode_nama'] . '</option>';
                }

                echo '</select><div class="invalid-feedback">Pilih Matakuliah Asal</div>
                                    </div>
                                    <div class="mb-3 col-md-5">
                                       
                                        <select class="form-control" id="matakuliah2" name="matakuliah2" placeholder="Pilih Matakuliah Tujuan..." required>';
                $matakuliah2 = mysqli_query($con, "SELECT team.*, matakuliah.nama as matakuliah_nama, matakuliah.semester as matakuliah_semester, 
                                        matakuliah.kode as matakuliah_kode, prodi.nama as prodi_nama, periode.nama as periode_nama
                                        FROM team 
                                        LEFT JOIN matakuliah ON team.matakuliah = matakuliah.uuid
                                        LEFT JOIN periode ON  team.periode = periode.uuid
                                        LEFT JOIN prodi ON team.prodi = prodi.uuid GROUP BY matakuliah.uuid ");
                echo "<option value=''>Pilih Matakuliah Tujuan...</option>";
                while ($a = mysqli_fetch_array($matakuliah2)) {
                    echo '<option value="' . $a['uuid'] . '"></i> ' . $a['matakuliah_kode'] . ' ' . $a['matakuliah_nama'] . ' (smt:' . $a['matakuliah_semester'] . ') ' . $a['prodi_nama'] . ' - ' . $a['periode_nama'] . '</option>';
                }

                echo '</select><div class="invalid-feedback">Pilih Matakuliah Tujuan</div>
                                    </div>
                                    <div class="mb-3 col-md-2">
                                    <input type="hidden" name="menu" value="usercopy">
                                    <button type="submit" class="btn btn-primary" id="submit"><span id="proses"></span>Proses</button>
                                    </div>
                                </div>
                                </div>
                        </form>';
                $submit = clear(isset($_POST['menu']) ? $_POST['menu'] : '');
                if ($submit != "") {
                    $mk1 = clear(isset($_POST['matakuliah1']) ? $_POST['matakuliah1'] : '');
                    $mk2 = clear(isset($_POST['matakuliah2']) ? $_POST['matakuliah2'] : '');
                    $team1 = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM team WHERE uuid = '" . $mk1 . "' "));
                    $team2 = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM team WHERE uuid = '" . $mk2 . "' "));
                    $cekmk1 = mysqli_query($con, "SELECT * FROM rps WHERE periode = '" . $team1['periode'] . "' AND prodi='" . $team1['prodi'] . "' AND matakuliah='" . $team1['matakuliah'] . "'");
                    $user = $_SESSION['uuid'];
                    $date = date('Y-m-d h:i:s');
                    if (mysqli_num_rows($cekmk1) > 0) {
                        $data1 = mysqli_fetch_array($cekmk1);
                        $cekmk2 = mysqli_query($con, "SELECT * FROM rps WHERE periode = '" . $team2['periode'] . "' AND prodi='" . $team2['prodi'] . "' AND matakuliah='" . $team2['matakuliah'] . "'");
                        if (mysqli_num_rows($cekmk2) > 0) {
                            //update
                            $data2 = mysqli_fetch_array($cekmk2);
                            mysqli_query($con, "UPDATE rps SET deskripsi = '" . $data1['deskripsi'] . "', 
                                                               capaian = '" . $data1['capaian'] . "', 
                                                               kemampuan = '" . $data1['kemampuan'] . "', 
                                                               bahan = '" . $data1['bahan'] . "', 
                                                               indikator='" . $data1['indikator'] . "', 
                                                               bobot='" . $data1['bobot'] . "', 
                                                               tugas='" . $data1['tugas'] . "', 
                                                               metode='" . $data1['metode'] . "', 
                                                               alokasi='" . $data1['alokasi'] . "', 
                                                               referensi='" . $data1['referensi'] . "', 
                                                               update_at='" . $date . "',
                                                               update_by='" . $user . "'
                                                               WHERE uuid='" . $data2['uuid'] . "' ");
                            $rpm1 = mysqli_query($con, "SELECT * FROM rpm WHERE rps='" . $data1['uuid'] . "' ");
                            while ($f = mysqli_fetch_array($rpm1)) {
                                mysqli_query($con, "UPDATE rpm SET kemampuan = '" . $f['kemampuan'] . "', 
                                                               bahan = '" . $f['bahan'] . "', 
                                                               mediaajar = '" . $f['mediaajar'] . "', 
                                                               metode1 = '" . $f['metode1'] . "', 
                                                               metode2='" . $f['metode2'] . "', 
                                                               metode3='" . $f['metode3'] . "', 
                                                               alokasi1='" . $f['alokasi1'] . "', 
                                                               alokasi2='" . $f['alokasi2'] . "', 
                                                               alokasi3='" . $f['alokasi3'] . "', 
                                                               keterangan='" . $f['keterangan'] . "', 
                                                               tugas='" . $f['tugas'] . "', 
                                                               kriteria='" . $f['kriteria'] . "', 
                                                               indikator='" . $f['indikator'] . "', 
                                                               bobot='" . $f['bobot'] . "', 
                                                               referensi='" . $f['referensi'] . "', 
                                                               update_at='" . $date . "',
                                                               update_by='" . $user . "'
                                                               WHERE rps='" . $data2['uuid'] . "' AND pertemuan='" . $f['pertemuan'] . "'");
                            }
                            echo '<div class="alert alert-info alert-dismissible" role="alert">
                            Sukses Update RPS Tujuan
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
                        } else {
                            //insert
                            mysqli_query($con, "INSERT INTO rps (uuid, periode, prodi, matakuliah,deskripsi,capaian,kemampuan,bahan,indikator,bobot,tugas,metode,alokasi,referensi,create_at,update_by)
                                                VALUES (UUID(),'" . $data1['periode'] . "','" . $data1['prodi'] . "','" . $team2['matakuliah'] . "','" . $data1['deskripsi'] . "','" . $data1['capaian'] . "','" . $data1['kemampuan'] . "',
                                                        '" . $data1['bahan'] . "','" . $data1['indikator'] . "','" . $data1['bobot'] . "','" . $data1['tugas'] . "','" . $data1['metode'] . "','" . $data1['alokasi'] . "','" . $data1['referensi'] . "',
                                                        '" . $date . "','" . $user . "')");
                            $rps2 = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM rps WHERE periode = '" . $team2['periode'] . "' AND prodi='" . $team2['prodi'] . "' AND matakuliah='" . $team1['matakuliah'] . "'"));
                            $rpm1 = mysqli_query($con, "SELECT * FROM rpm WHERE rps='" . $data1['uuid'] . "' ");
                            while ($f = mysqli_fetch_array($rpm1)) {
                                mysqli_query($con, "INSERT INTO rpm (uuid,rps,pertemuan,kemampuan,bahan,mediaajar,metode1,metode2,metode3,alokasi1,alokasi2,alokasi3,keterangan,tugas,kriteria,indikator,bobot,referensi,create_at,create_by)
                                                    VALUES (UUID(),'".$rps2['uuid']."','" . $f['pertemuan'] . "','" . $f['kemampuan'] . "','" . $f['bahan'] . "','" . $f['mediaajar'] . "','" . $f['metode1'] . "','" . $f['metode2'] . "','" . $f['metode3'] . "',
                                                    '" . $f['alokasi1'] . "','" . $f['alokasi2'] . "','" . $f['alokasi3'] . "','" . $f['keterangan'] . "','" . $f['tugas'] . "','" . $f['kriteria'] . "','" . $f['indikator'] . "','" . $f['bobot'] . "','" . $f['referensi'] . "','".$date."','".$user."')");
                            }
                            echo '<div class="alert alert-primary alert-dismissible" role="alert">
                            Sukses Insert RPS Tujuan
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        Maaf, Tidak ada RPS pada Matakuliah Asal
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>