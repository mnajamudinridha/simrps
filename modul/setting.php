<?php
defined("BASEPATH") or exit("No direct access allowed");

use \Gumlet\ImageResize;

$aksi = clear(isset($_GET['aksi']) ? $_GET['aksi'] : '');
$proses = clear(isset($_POST['proses']) ? $_POST['proses'] : '');


function tampildata($con, $pesan = NULL)
{
    $biodata = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM user WHERE uuid='" . $_SESSION['uuid'] . "'"));
?>

    <h5 class="card-header">Setting Details</h5>
    <!-- Account -->
    <form id="formAccountSettings" method="POST" class="mb-5">
        <div class="card-body">
            <?php
            if ($pesan != "") {
                echo $pesan;
            }
            ?>
            <div class="row">
                <div class="mb-3 col-md-12">
                    <label for="nama" class="form-label">Password Lama</label>
                    <input type="password" id="password1" name="password1" class="form-control" value="" placeholder="Password Lama" required />
                </div>
                <div class="mb-3 col-md-6">
                    <label for="nama" class="form-label">Password Baru</label>
                    <input type="password" id="password2" name="password2" class="form-control" value="" placeholder="Password Baru" required />
                </div>
                <div class="mb-3 col-md-6">
                    <label for="nama" class="form-label">Password Baru (Ulangi)</label>
                    <input type="password" id="password3" name="password3" class="form-control" value="" placeholder="Password Baru (Ulangi)" required />
                </div>
            </div>
            <div class="mt-2">
                <input type="hidden" name="proses" value="editprofile">
                <input type="hidden" name="uuid" id="uuid" value="<?php echo $biodata['uuid']; ?>">
                <button type="button" class="btn btn-primary float-end me-2" id="tomboleditprofile"><span id="loadingeditprofile"></span> &nbsp; Ganti Password</button>
            </div>
        </div>
    </form>
    <!-- /Account -->
<?php
}

if ($aksi == "edit") {
    $pesan = "";
    if ($proses == "editprofile") {
        $password1 = clear(isset($_POST['password1']) ? $_POST['password1'] : '');
        $password2 = clear(isset($_POST['password2']) ? $_POST['password2'] : '');
        $password3 = clear(isset($_POST['password3']) ? $_POST['password3'] : '');
        $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        if ($password2 == $password3) {
            $password = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM user WHERE uuid='".$uuid."' AND delete_at IS NULL"));
            if (password_verify($password1, $password['password'])) {
                mysqli_query($con, "UPDATE user SET password = '" . password_hash($password3, PASSWORD_BCRYPT) . "',
                                           update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
                $pesan = '<div class="alert alert-success alert-dismissible" role="alert">
            Selamat anda berhasil merubah password anda!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
            } else {
                $pesan = '<div class="alert alert-danger alert-dismissible" role="alert">
                maaf password lama yang anda masukkan salah!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
            }
        } else {
            // password baru tidak sama
            $pesan = '<div class="alert alert-warning alert-dismissible" role="alert">
            maaf password baru yang anda masukan tidak sama!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
    }
    tampildata($con, $pesan);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?menu=profile"><i class="bx bx-user me-1"></i> Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="index.php?menu=setting"><i class="bx bx-link-alt me-1"></i> Setting</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?menu=quisioner"><i class="bx bx-equaliser me-1"></i> Quisioner</a>
                </li>
            </ul>
            <div class="card mb-4 tabeluserprofile">
                <?php tampildata($con); ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('#body').on('click', '#tomboleditprofile', function(e) {
                e.preventDefault();
                tinyMCE.triggerSave();
                var xhr;
                if (xhr && xhr.readystate != 4) {
                    xhr.abort();
                }
                $('#loadingeditprofile').append(
                    "<div class='spinner-border spinner-border-sm text-white' role='status'><span class='visually-hidden'>Loading...</span></div>"
                );
                $('#tomboleditprofile').prop('disabled', true);
                xhr = $.ajax({
                    type: "POST",
                    url: 'index.php?method=ajax&menu=setting&aksi=edit',
                    data: new FormData(document.getElementById('formAccountSettings')),
                    // data: $('#formuserlevel').serialize(),
                    processData: false,
                    cache: false,
                    contentType: false,
                    success: function(response) {
                        $('#loadingeditprofile').html('');
                        $('#tomboleditprofile').prop('disabled', false);
                        $('.tabeluserprofile').html(response);
                        selecticonEdit = new TomSelect(".tom-select-standard-profile", {
                            sortField: {
                                field: "text",
                                direction: "asc"
                            }
                        });
                        tinyMCE.get('biodataedit').remove();
                        tinyMCE.execCommand("mceAddEditor", false, 'biodataedit');
                    },
                    error: function() {
                        $('#loadingeditprofile').html('');
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        });
                        $('#tomboleditprofile').prop('disabled', false);
                    }
                });
                return false;
            });
        });
    </script>
<?php
}
?>