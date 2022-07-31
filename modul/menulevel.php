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
    <table class='table' id=''><thead><tr><th>No</th><th>Icon</th><th>Menu</th>";
    while ($a = mysqli_fetch_array($level)) {
        echo "<th class='text-center'><i class='tf-icon $a[icon] bx-xs text-primary me-3 text-center'></i>$a[kode]</th>";
    }
    echo "</tr></thead>";
    echo '<tbody class="table-border-bottom-0">';
    $user =  mysqli_query($con, "SELECT * FROM menu WHERE delete_at IS NULL ORDER BY jenis, urutan");
    $no = 1;
    while ($b = mysqli_fetch_array($user)) {
        echo "<tr>";
        echo "<td>$no</td>";
        echo "<td><i class='tf-icon $b[icon] bx-md text-primary me-3'></i></td>";
        echo "<td><strong>$b[nama]</strong></td>";
        $sublevel = mysqli_query($con, "SELECT * FROM level WHERE delete_at IS NULL");
        while ($c = mysqli_fetch_array($sublevel)) {
            echo "<td><div class='form-check form-switch mb-2'>
                  <input class='form-check-input' type='checkbox' 
                  name='menulevel[]' value=\"" . $c['uuid'] . "," . $b['uuid'] . "\"";
            $cek = mysqli_num_rows(mysqli_query($con, "SELECT * FROM menulevel WHERE menu='" . $b['uuid'] . "' AND level='" . $c['uuid'] . "'"));
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
    if ($proses == "updatemenulevel") {
        $menulevel = isset($_POST['menulevel']) ? $_POST['menulevel'] : array();
        mysqli_query($con, "TRUNCATE TABLE menulevel;");
        foreach ($menulevel as $key => $value) {
            $data = explode(",", $value);
            $query = "INSERT INTO menulevel (menu,level) VALUES ('" . clear($data[1]) . "','" . clear($data[0]) . "');";
            mysqli_query($con, $query);
        }
        echo '<div class="alert alert-primary alert-dismissible" role="alert">
                Sukses Update Setting Menu Level!
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
                <h4 class="card-header">Table Setting Menu Level</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                </div>
                <div class="card-body">
                    <?php
                    echo '<form id="formmenulevel" class="mb-3" action="" method="POST">';
                    echo '<div class="table table-striped tabelmenulevel" class="files" id="previews">';
                    echo tampildata($con);
                    echo '</div>';
                    echo '<input type="hidden" name="proses" value="updatemenulevel">';
                    echo '<button type="button" class="btn bg-primary text-white mt-4 float-end" id="tombolmenulevel">
                         <span id="loadingleveluser"></span> &nbsp; Update Menu Level</button>';
                    echo '</form>';
                    ?>

                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                // tambah data
                $('#body').on('click', '#tombolmenulevel', function(e) {
                    e.preventDefault();
                    var xhr;
                    if (xhr && xhr.readystate != 4) {
                        xhr.abort();
                    }
                    $('#loadingleveluser').append(
                        "<div class='spinner-border spinner-border-sm text-white' role='status'><span class='visually-hidden'>Loading...</span></div>"
                    );
                    $('#tombolmenulevel').prop('disabled', true);
                    xhr = $.ajax({
                        type: "POST",
                        url: 'index.php?method=ajax&menu=menulevel&aksi=tambah',
                        data: new FormData(document.getElementById('formmenulevel')),
                        // data: $('#formmenulevel').serialize(),
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
                            $('#tombolmenulevel').prop('disabled', false);
                            $('.tabelmenulevel').html(response);
                        },
                        error: function() {
                            $('#loadingleveluser').html('');
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!'
                            });
                            $('#tombolmenulevel').prop('disabled', false);
                        }
                    });
                    return false;
                });
            });
        </script>
    <?php
}
    ?>