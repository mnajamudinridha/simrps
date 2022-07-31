<?php
defined("BASEPATH") or exit("No direct access allowed");

use \Gumlet\ImageResize;


$aksi = clear(isset($_GET['aksi']) ? $_GET['aksi'] : '');
$proses = clear(isset($_POST['proses']) ? $_POST['proses'] : '');

/* ****************** **
** FUNGSI TAMPIL DATA **
** ****************** */
function tampildata($con)
{

    $level = mysqli_query($con, "SELECT * FROM level WHERE delete_at IS NULL");
    echo "<div class='table-responsive'>
    <table class='table' id=''><thead><tr><th>No</th><th>Profile</th><th>Dosen</th>";
    while ($a = mysqli_fetch_array($level)) {
        echo "<th class='text-center'><i class='tf-icon $a[icon] bx-xs text-primary me-3 text-center'></i></th>";
    }
    echo "<th class='text-center'><i class='tf-icon bx bx-abacus bx-xs text-primary me-3 text-center'></i></th>";
    echo "<th class='text-center'><i class='tf-icon BX bx-add-to-queue bx-xs text-primary me-3 text-center'></i></th>";
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
        $sublevel = mysqli_query($con, "SELECT * FROM level WHERE delete_at IS NULL");
        while ($c = mysqli_fetch_array($sublevel)) {
            echo "<td><div class='form-check form-switch mb-2'>
                  <input class='form-check-input' type='checkbox' 
                  name='userlevel[]' value=\"" . $c['uuid'] . "," . $b['uuid'] . "\"";
            $cek = mysqli_num_rows(mysqli_query($con, "SELECT * FROM userlevel WHERE user='" . $b['uuid'] . "' AND level='" . $c['uuid'] . "'"));
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
        $userlevel = isset($_POST['userlevel']) ? $_POST['userlevel'] : array();
        mysqli_query($con, "TRUNCATE TABLE userlevel;");
        foreach ($userlevel as $key => $value) {
            $data = explode(",", $value);
            $query = "INSERT INTO userlevel (user,level) VALUES ('" . clear($data[1]) . "','" . clear($data[0]) . "');";
            mysqli_query($con, $query);
        }
        echo '<div class="alert alert-primary alert-dismissible" role="alert">
        Sukses Update Setting Level Dosen!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
      </div>';
    }
    tampildata($con);
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
                    echo '<div class="table table-striped tabeluserlevel" class="files" id="previews">';
                    echo '<form id="formAccountSettings" method="POST">
                                <div class="col-md-12 mb-5 pb-5">
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                       
                                        <select class="form-control" id="icon" name="icon" placeholder="Pilih Icon..." required>';
                    $query = mysqli_query($con, "SELECT * FROM icon");
                    echo "<option value=''>Pilih Icon...</option>";
                    while ($a = mysqli_fetch_array($query)) {
                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                    }

                    echo '</select><div class="invalid-feedback">Pilih Icon Periode</div>
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <input type="password" id="password3" name="password3" class="form-control" value="" placeholder="Password Baru (Ulangi)" required />
                                    </div>
                                    <div class="mb-3 col-md-2">
                                    <select class="form-control" id="icon" name="icon" placeholder="Pilih Icon..." required>';
                    $query = mysqli_query($con, "SELECT * FROM icon");
                    echo "<option value=''>Pilih Icon...</option>";
                    while ($a = mysqli_fetch_array($query)) {
                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                    }

                    echo '</select>
                                    </div>
                                    <div class="mb-3 col-md-2">
                                    <button type="button" class="btn btn-primary" id="tomboleditprofile"><span id="loadingeditprofile"></span>Proses</button>
                                    </div>
                                </div>
                                <div class="">
                                    <input type="hidden" name="proses" value="editprofile">
                                    
                                </div>
                                </div>
                            
                        </form>';
                    ?>

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
                        url: 'index.php?method=ajax&menu=userlevel&aksi=tambah',
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