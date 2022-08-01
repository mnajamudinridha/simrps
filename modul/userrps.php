<?php
defined("BASEPATH") or exit("No direct access allowed");

use \Gumlet\ImageResize;


$aksi = clear(isset($_GET['aksi']) ? $_GET['aksi'] : '');
$proses = clear(isset($_POST['proses']) ? $_POST['proses'] : '');
$team = clear(isset($_GET['team']) ? $_GET['team'] : '');

/* ****************** **
** FUNGSI TAMPIL DATA **
** ****************** */
function tampildata($con, $team)
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
        $cekrps = mysqli_num_rows($queryrps);
        if ($cekrps > 0) {
            //update
            $rps = mysqli_fetch_array($queryrps);

            echo '<h4>Rencana Pembelajaran Semester (RPS)</h4><table class="table">
            <tr><th>1.</th><th>Nama Program Studi</th><td>:</td><th>' . $prodi['nama'] . '</th></tr>
            <tr><th>2.</th><th>Nama Matakuliah</th><td>:</td><th>' . $matakuliah['nama'] . '</th></tr>
            <tr><th>3.</th><th>Kode</th><td>:</td><th>' . $matakuliah['kode'] . '</th></tr>
            <tr><th>4.</th><th>Semester</th><td>:</td><th>' . $matakuliah['semester'] . '</th></tr>
            <tr><th>5.</th><th>S K S</th><td>:</td><th>' . $matakuliah['sks'] . '(' . $matakuliah['skst'] . ',' . $matakuliah['sksp'] . ')</th></tr>
            <tr><th>6.</th><th>Nama Dosen Pengampu</th><td>:</td><th>';
            echo '<ol>';
            while ($n = mysqli_fetch_array($anggota)) {
                echo '<li>' . $n['nama_user'] . '</li>';
            }
            echo '</ol>';
            echo '</th></tr>';
            echo '<tr><th rowspan=2>7.</th><th colspan=3>Deskripsi Singkat Mata Kuliah</th></tr>';
            echo '<tr><th colspan=3><textarea class="form-control mytextarea" id="biodata-edit" name="biodata" placeholder="Biodata Singkat Dosen" style="height: 320px;"></textarea></th></tr>';
            echo '<tr><th rowspan=2>8.</th><th colspan=3>8.	Capaian Pembelajaran Lulusan (CPL)</th></tr>';
            echo '<tr><th colspan=3><textarea class="form-control mytextarea" id="biodata-edit" name="biodata" placeholder="Biodata Singkat Dosen" style="height: 320px;"></textarea></th></tr>';
            echo '</table>';
        } else {
            //create
            $user = $_SESSION['uuid'];
            $date = date('Y-m-d h:i:s');
            mysqli_query($con, "INSERT INTO rps (uuid,periode,prodi,matakuliah,create_at,create_by) VALUES
                                (UUID(),'" . $data['periode'] . "','" . $data['prodi'] . "','" . $data['matakuliah'] . "','" . $date . "','" . $user . "')");
            $rps = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM rps WHERE periode='" . $data['periode'] . "' AND prodi='" . $data['prodi'] . "' AND matakuliah='" . $data['matakuliah'] . "' "));
            //rpm
            mysqli_query($con, "INSERT INTO rpm (uuid,rps,create_at,create_by) VALUES
                                (UUID(),'" . $rps['uuid'] . "','" . $date . "','" . $user . "')");
            //uki
            mysqli_query($con, "INSERT INTO uki (uuid,rps,create_at,create_by) VALUES
                                (UUID(),'" . $rps['uuid'] . "','" . $date . "','" . $user . "')");
        }
    }
}

function loaddata()
{
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
                <h4 class="card-header">Table Setting Level Dosen</h4>
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
                                    <button type="submit" class="btn btn-primary" name="prosesrps"><span id="proses"></span>Proses</button>
                                    </div>
                                </div>
                                </div>
                        </form>';

                    if (isset($_GET['prosesrps']) && $team != "") {
                        echo '<form id="formuserlevel" class="mb-3" action="" method="POST">';
                        echo '<div class="table table-striped tabeluserlevel" class="files" id="previews">';
                        tampildata($con, $team);
                        echo '</div>';
                        echo '<input type="hidden" name="proses" value="updateuserlevel">';
                        echo '<input type="hidden" name="team" value="' . $team . '">';
                        echo '<button type="button" class="btn bg-primary text-white mt-4 float-end" id="tomboluserlevel">
                         <span id="loadingleveluser"></span> &nbsp; Update RPS</button>';
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
            tinymce.init({
                selector: '.mytextarea',
                plugins: [
                    'a11ychecker', 'advlist', 'advcode', 'advtable', 'autolink', 'checklist', 'export',
                    'lists', 'link', 'image', 'code', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks',
                    'powerpaste', 'fullscreen', 'formatpainter', 'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | a11ycheck casechange blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify |' +
                    'bullist numlist checklist outdent indent | removeformat | code table help | link image',
                images_upload_url: 'acceptor.php',
                images_dataimg_filter: function(img) {
                    return !img.hasAttribute('internal-blob'); // blocks the upload of <img> elements with the attribute "internal-blob".
                },
                /* enable title field in the Image dialog*/
                image_title: true,
                /* enable automatic uploads of images represented by blob or data URIs*/
                automatic_uploads: true,
                /*
                  URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
                  images_upload_url: 'postAcceptor.php',
                  here we add custom filepicker only to Image dialog
                */
                file_picker_types: 'image',
                /* and here's our custom image picker*/
                file_picker_callback: function(cb, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    /*
                      Note: In modern browsers input[type="file"] is functional without
                      even adding it to the DOM, but that might not be the case in some older
                      or quirky browsers like IE, so you might want to add it to the DOM
                      just in case, and visually hide it. And do not forget do remove it
                      once you do not need it anymore.
                    */

                    input.onchange = function() {
                        var file = this.files[0];

                        var reader = new FileReader();
                        reader.onload = function() {
                            /*
                              Note: Now we need to register the blob in TinyMCEs image blob
                              registry. In the next release this part hopefully won't be
                              necessary, as we are looking to handle it internally.
                            */
                            var id = 'blobid' + (new Date()).getTime();
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);

                            /* call the callback and populate the Title field with the file name */
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