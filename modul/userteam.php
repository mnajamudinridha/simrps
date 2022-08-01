<?php
defined("BASEPATH") or exit("No direct access allowed");

use \Gumlet\ImageResize;


$aksi = clear(isset($_GET['aksi']) ? $_GET['aksi'] : '');
$proses = clear(isset($_POST['proses']) ? $_POST['proses'] : '');
$prodi = clear(isset($_GET['prodi']) ? $_GET['prodi'] : '');
$periode = clear(isset($_GET['periode']) ? $_GET['periode'] : '');
$semester = clear(isset($_GET['semester']) ? $_GET['semester'] : '');
$pprodi = clear(isset($_POST['prodi']) ? $_POST['prodi'] : '');
$pperiode = clear(isset($_POST['periode']) ? $_POST['periode'] : '');
$psemester = clear(isset($_POST['semester']) ? $_POST['semester'] : '');

/* ****************** **
** FUNGSI TAMPIL DATA **
** ****************** */
function tampildata($con, $prodi, $periode, $semester)
{

    $level = mysqli_query($con, "SELECT * FROM matakuliah WHERE delete_at IS NULL AND prodi = '" . $prodi . "' AND semester='" . $semester . "'");
    $levelcetak = mysqli_query($con, "SELECT matakuliah.*, prodi.nama as nama_prodi FROM matakuliah 
                                      LEFT JOIN prodi ON matakuliah.prodi = prodi.uuid
                                      WHERE matakuliah.delete_at IS NULL AND matakuliah.prodi = '" . $prodi . "' AND matakuliah.semester='" . $semester . "'");
    echo "<div class='table-responsive mb-5'><table class='table table-hover'><tr><th>No</th><th>Kode</th><th>Matakuliah</th><th>SMT</th><th>SKS</th><th>Prodi</th></tr>";
    $no = 1;
    while ($g = mysqli_fetch_array($levelcetak)) {
        echo "<tr><th>$no</th><th>" . $g['kode'] . "</th><th>" . $g['nama'] . "</th><th>" . $g['semester'] . "</th><th>" . $g['sks'] . " (T:" . $g['skst'] . ",P:" . $g['sksp'] . ")</th><th>" . $g['nama_prodi'] . "</th></tr>";
        $no++;
    }
    echo "</table></div>";
    echo "<div class='table-responsive'>
    <table class='table' id=''><thead><tr><th>No</th><th>Profile</th><th>Dosen</th>";
    while ($a = mysqli_fetch_array($level)) {
        echo "<th class='text-center'>$a[kode]</th>";
    }
    echo "</tr></thead>";
    echo '<tbody class="table-border-bottom-0">';
    $user =  mysqli_query($con, "SELECT * FROM user WHERE delete_at IS NULL");
    $no = 1;
    while ($b = mysqli_fetch_array($user)) {
        $photo = $b['photo'] != "" ? "storage/profile/small_" . $b['photo'] : 'assets/img/avatars/user.png';
        echo "<tr>";
        echo "<td>$no</td>";
        echo "<td>
                    <ul class='list-unstyled users-list m-0 avatar-group d-flex align-items-center'>
                        <li data-bs-toggle='tooltip' data-popup='tooltip-custom' data-bs-placement='top' class='avatar avatar-md pull-up' title='' data-bs-original-title='" . $b['nama'] . "'>
                            <img src='" . $photo . "' alt='Avatar' class='rounded-circle'>
                        </li>
                    </ul>
                 </td>";
        echo "<td><strong>$b[nama]</strong></td>";
        $sublevel = mysqli_query($con, "SELECT * FROM matakuliah WHERE delete_at IS NULL AND prodi = '" . $prodi . "' AND semester='" . $semester . "'");
        while ($c = mysqli_fetch_array($sublevel)) {
            echo "<td><div class='form-check form-switch mb-2'>
                  <input class='form-check-input' type='checkbox' 
                  name='userteam[]' value=\"" . $c['uuid'] . "," . $b['uuid'] . "\"";
            $cek = mysqli_num_rows(mysqli_query($con, "SELECT * FROM team WHERE periode='" . $periode . "' AND prodi='" . $prodi . "' AND user='" . $b['uuid'] . "' AND matakuliah='" . $c['uuid'] . "'"));
            if ($cek > 0) {
                echo "checked";
            }
            echo "></div></td>";
        }
        echo "</tr>";
        $no++;
    }
    echo '</tbody></table>
    </div>';
}


if ($aksi == "tambah") {
    if ($proses == "updateuserlevel") {
        $userlevel = isset($_POST['userteam']) ? $_POST['userteam'] : array();
        $getmk = mysqli_query($con, "SELECT * FROM matakuliah WHERE prodi='".$pprodi."' AND semester='".$psemester."'");
        $totmk = mysqli_num_rows($getmk);
        $querymk = "";
        $cekfor = 1;
        while($h = mysqli_fetch_array($getmk)){
            if($cekfor == $totmk){
                $querymk .= " matakuliah = '".$h['uuid']."' ";
            }else{
                $querymk .= " matakuliah = '".$h['uuid']."' OR ";
            }
            $cekfor++;
        }
        mysqli_query($con, "DELETE FROM team WHERE periode='".$pperiode."' AND prodi='".$pprodi."' AND (".$querymk.");");
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        foreach ($userlevel as $key => $value) {
            $data = explode(",", $value);
            $query = "INSERT INTO team (uuid,periode,prodi,user,matakuliah,create_at,create_by) 
                      VALUES (UUID(),'".$pperiode."','".$pprodi."','" . clear($data[1]) . "','" . clear($data[0]) . "','".$date."','".$user."');";
            mysqli_query($con, $query);
        }
        echo '<div class="alert alert-primary alert-dismissible" role="alert">
        Sukses Update Setting Team Teaching!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
      </div>';
    }
    tampildata($con, $pprodi, $pperiode, $psemester);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-header">Table Setting Team Teaching</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                </div>
                <div class="card-body">
                    <?php
                    echo '<form id="proses-data" action="index.php" method="GET">
                                <div class="col-md-12 mb-3">
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                       
                                        <select class="form-control" id="prodi" name="prodi" placeholder="Pilih Prodi..." required>';
                    $query = mysqli_query($con, "SELECT * FROM prodi WHERE delete_at IS NULL");
                    echo "<option value=''>Pilih Prodi...</option>";
                    while ($a = mysqli_fetch_array($query)) {
                        if($a['uuid'] == $prodi){
                            echo '<option value="' . $a['uuid'] . '" data-src="' . $a['kode'] . '" selected></i> &nbsp; ' . $a['nama'] . '</option>';
                        }else{
                            echo '<option value="' . $a['uuid'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                        }
                    }

                    echo '</select><div class="invalid-feedback">Pilih Prodi</div>
                                    </div>
                                    <div class="mb-3 col-md-4">
                                       
                                        <select class="form-control" id="periode" name="periode" placeholder="Pilih Periode..." required>';
                    $query = mysqli_query($con, "SELECT * FROM periode WHERE delete_at IS NULL");
                    echo "<option value=''>Pilih Periode...</option>";
                    while ($a = mysqli_fetch_array($query)) {
                        if($a['uuid'] == $periode){
                            echo '<option value="' . $a['uuid'] . '" data-src="' . $a['kode'] . '" selected></i> &nbsp; ' . $a['nama'] . '</option>';
                        }else{
                            echo '<option value="' . $a['uuid'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                        }
                    }

                    echo '</select><div class="invalid-feedback">Pilih Periode</div>
                                    </div>
                                    <div class="mb-3 col-md-2">
                                    <select class="form-control" id="semester" name="semester" placeholder="Pilih Semester..." required>
                                    <option value="1" '.($semester==1 ? 'selected' : '').'>Smt 1</option>
                                    <option value="2" '.($semester==2 ? 'selected' : '').'>Smt 2</option>
                                    <option value="3" '.($semester==3 ? 'selected' : '').'>Smt 3</option>
                                    <option value="4" '.($semester==4 ? 'selected' : '').'>Smt 4</option>
                                    <option value="5" '.($semester==5 ? 'selected' : '').'>Smt 5</option>
                                    <option value="6" '.($semester==6 ? 'selected' : '').'>Smt 6</option>';
                    echo '</select>
                                    </div>
                                    <div class="mb-3 col-md-2">
                                    <input type="hidden" name="menu" value="userteam">  
                                    <button type="submit" class="btn btn-primary" id="submit"><span id="proses"></span>Proses</button>
                                    </div>
                                </div>
                                </div>
                        </form>';
                    if ($prodi != "" && $periode != "" && $semester != "") {
                        echo '<form id="formuserlevel" class="mb-3" action="" method="POST">';
                        echo '<div class="table table-striped tabeluserlevel" class="files" id="previews">';
                        tampildata($con, $prodi, $periode, $semester);
                        echo '</div>';
                        echo '<input type="hidden" name="proses" value="updateuserlevel">';
                        echo '<input type="hidden" name="prodi" value="'.$prodi.'">';
                        echo '<input type="hidden" name="periode" value="'.$periode.'">';
                        echo '<input type="hidden" name="semester" value="'.$semester.'">';
                        echo '<button type="button" class="btn bg-primary text-white mt-4 float-end" id="tomboluserlevel">
                         <span id="loadingleveluser"></span> &nbsp; Update Team Teaching</button>';
                        echo '</form>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // tambah data
            $('#body').on('click', '#tomboluserlevel', function(e) {
                e.preventDefault();
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
                    url: 'index.php?method=ajax&menu=userteam&aksi=tambah',
                    data: new FormData(document.getElementById('formuserlevel')),
                    // data: $('#formuserlevel').serialize(),
                    processData: false,
                    cache: false,
                    contentType: false,
                    success: function(response) {
                        $('#loadingleveluser').html('');
                        Swal.fire(
                            'Sukses!',
                            'Sukses Update Team Teaching',
                            'success'
                        );
                        $('#tomboluserlevel').prop('disabled', false);
                        $('.tabeluserlevel').html(response);
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
        });
    </script>
<?php
}
?>