<?php
defined("BASEPATH") or exit("No direct access allowed");

use \Gumlet\ImageResize;

$aksi = clear(isset($_GET['aksi']) ? $_GET['aksi'] : '');
$proses = clear(isset($_POST['proses']) ? $_POST['proses'] : '');


function tampildata($con)
{
    $biodata = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM user WHERE uuid='" . $_SESSION['uuid'] . "'"));
?>

    <h5 class="card-header">Quisioner Details</h5>
    <!-- Account -->



    <?php
    $pertanyaan = mysqli_query($con, "SELECT pertanyaan.*, periode.nama as nama_periode, periode.status as status_periode FROM pertanyaan 
                                                      LEFT JOIN periode ON pertanyaan.periode = periode.uuid
                                                      WHERE pertanyaan.delete_at IS NULL AND periode.status = 1 ORDER BY pertanyaan.urutan");
    $total = mysqli_num_rows($pertanyaan);
    if ($total > 0) {
        echo '<form id="formAccountSettings" method="POST" class="mb-5">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                              <tr><th>No</th><th>Pertanyaan</th><th>Jawaban</th></tr>';
        $no = 1;
        while ($a = mysqli_fetch_array($pertanyaan)) {
            $querycheck = mysqli_query($con, "SELECT * FROM jawaban WHERE user = '" . $biodata['uuid'] . "' AND pertanyaan = '" . $a['uuid'] . "' ");
            $cekdata = mysqli_num_rows($querycheck);
            $get = array();
            $get['jawaban'] = null;
            if ($cekdata > 0) {
                $get = mysqli_fetch_array($querycheck);
            }
            if ($a['tipe'] == 'star') {
                echo "<tr><td>$no</td><td>" . $a['nama'] . "</td><td>
                            <div class='rating-" . $a['kode'] . "' data-rating='".$get['jawaban']."'></div>
                            <input type='hidden' name='nilai-" . $a['kode'] . "' id='nilai-" . $a['kode'] . "' value='".$get['jawaban']."'>
                            </td></tr>";
            } elseif ($a['tipe'] == "esai") {
                echo "<tr><td rowspan='2'>$no</td><td colspan='2'>" . $a['nama'] . "</td></tr><tr><td colspan='2'>
                                    <textarea class='form-control mytextarea' id='nilai-" . $a['kode'] . "' name='nilai-" . $a['kode'] . "' placeholder='' style='height: 320px;'>" . $get['jawaban'] . "</textarea>
                                    </div></td></tr>";
            }
            $no++;
        }
        echo '</table>
                    <div class="mt-2">
                    <input type="hidden" name="proses" value="editquisioner">
                    <input type="hidden" name="uuid" id="uuid" value="' . $biodata['uuid'] . '">
                    <button type="button" class="btn btn-primary float-end mt-4" id="tomboleditquisioner"><span id="loadingeditquisioner"></span> &nbsp; Simpan Quisioner</button>
                </div>
            </div>
    </form>';
    } else {
        echo '<div class="card-body"><h3>Belum ada Quisioner Pada Periode Ini</h3></div>';
    }
}

if ($aksi == "edit") {
    if ($proses == "editquisioner") {
        $quis = mysqli_query($con, "SELECT pertanyaan.*, periode.nama as nama_periode, periode.status as status_periode FROM pertanyaan 
                                    LEFT JOIN periode ON pertanyaan.periode = periode.uuid
                                    WHERE pertanyaan.delete_at IS NULL AND periode.status = 1 ORDER BY pertanyaan.urutan");
        while ($f = mysqli_fetch_array($quis)) {
            $data = clear(isset($_POST['nilai-' . $f['kode']]) ? $_POST['nilai-' . $f['kode']] : '');
            $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
            $tanya = $f['uuid'];
            $user = $_SESSION['uuid'];
            $date = date('Y-m-d h:i:s');
            $querycheck = mysqli_query($con, "SELECT * FROM jawaban WHERE user = '" . $user . "' AND pertanyaan = '" . $tanya . "' ");
            $cekdata = mysqli_num_rows($querycheck);
            if ($cekdata > 0) {
                //update
                $get = mysqli_fetch_array($querycheck);
                mysqli_query($con, "UPDATE jawaban SET user='".$user."', pertanyaan='".$tanya."', jawaban='".$data."', 
                                    update_at='".$date."', update_by='".$uuid."' WHERE id='".$get['id']."';");
            } else {
                //insert
                mysqli_query($con, "INSERT INTO jawaban (user,pertanyaan,jawaban,create_at,create_by) 
                                    VALUES ('" . $user . "','" . $tanya . "','" . $data . "','" . $date . "','" . $uuid . "');");
            }
        }
    }
    tampildata($con);
} else {
    ?>
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?menu=profile"><i class="bx bx-user me-1"></i> Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?menu=setting"><i class="bx bx-link-alt me-1"></i> Setting</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="index.php?menu=quisioner"><i class="bx bx-equaliser me-1"></i> Quisioner</a>
                </li>
            </ul>
            <div class="card mb-4 tabeluserquisioner">
                <?php tampildata($con); ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            <?php
            $pertanyaanjs = mysqli_query($con, "SELECT pertanyaan.*, periode.nama as nama_periode, periode.status as status_periode FROM pertanyaan 
            LEFT JOIN periode ON pertanyaan.periode = periode.uuid
            WHERE pertanyaan.delete_at IS NULL AND pertanyaan.tipe = 'star' AND periode.status = 1  ORDER BY pertanyaan.urutan");
            $totaljs = mysqli_num_rows($pertanyaanjs);
            if ($totaljs > 0) {
                while ($c = mysqli_fetch_array($pertanyaanjs)) {
                    echo '$(".rating-' . $c['kode'] . '").starRating({';
                    echo "totalStars: 5,
                        emptyColor: 'lightgray',
                        hoverColor: 'salmon',
                        activeColor: 'cornflowerblue',
                        strokeWidth: 0,
                        useGradient: false,
                        minRating: 1,
                        callback: function(currentRating, el){
                            $(\"#nilai-$c[kode]\").val(currentRating)
                            console.log('DOM element ', el);
                        }
                      });\n";
                }
            }

            $pertanyaantiny = mysqli_query($con, "SELECT pertanyaan.*, periode.nama as nama_periode, periode.status as status_periode FROM pertanyaan 
                                        LEFT JOIN periode ON pertanyaan.periode = periode.uuid
                                        WHERE pertanyaan.delete_at IS NULL AND pertanyaan.tipe = 'esai' AND periode.status = 1  ORDER BY pertanyaan.urutan");
            $totaltiny = mysqli_num_rows($pertanyaantiny);
            if ($totaltiny > 0) {
                while ($c = mysqli_fetch_array($pertanyaantiny)) {
            ?>
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
                            return !img.hasAttribute('internal-blob');
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
            <?php
                }
            }
            ?>



            $('#body').on('click', '#tomboleditquisioner', function(e) {
                e.preventDefault();
                tinyMCE.triggerSave();
                var xhr;
                if (xhr && xhr.readystate != 4) {
                    xhr.abort();
                }
                $('#loadingeditquisioner').append(
                    "<div class='spinner-border spinner-border-sm text-white' role='status'><span class='visually-hidden'>Loading...</span></div>"
                );
                $('#tomboleditquisioner').prop('disabled', true);
                xhr = $.ajax({
                    type: "POST",
                    url: 'index.php?method=ajax&menu=quisioner&aksi=edit',
                    data: new FormData(document.getElementById('formAccountSettings')),
                    processData: false,
                    cache: false,
                    contentType: false,
                    success: function(response) {
                        $('#loadingeditquisioner').html('');
                        Swal.fire(
                            'Sukses!',
                            'Sukses Update Level Dosen',
                            'success'
                        );
                        $('#tomboleditquisioner').prop('disabled', false);
                        $('.tabeluserquisioner').html(response);
                        <?php
                        $tampiltiny = mysqli_query($con, "SELECT pertanyaan.*, periode.nama as nama_periode, periode.status as status_periode FROM pertanyaan 
                                                            LEFT JOIN periode ON pertanyaan.periode = periode.uuid
                                                            WHERE pertanyaan.delete_at IS NULL AND pertanyaan.tipe = 'esai' AND periode.status = 1 ORDER BY pertanyaan.urutan");
                        $totaltampiltiny = mysqli_num_rows($tampiltiny);
                        if ($totaltampiltiny > 0) {
                            while ($d = mysqli_fetch_array($tampiltiny)) {
                                echo "tinyMCE.get('nilai-$d[kode]').remove();
                                      tinyMCE.execCommand('mceAddEditor', false, 'nilai-$d[kode]');";
                            }
                        }
                        $pertanyaanjs1 = mysqli_query($con, "SELECT pertanyaan.*, periode.nama as nama_periode, periode.status as status_periode FROM pertanyaan 
                                                            LEFT JOIN periode ON pertanyaan.periode = periode.uuid
                                                            WHERE pertanyaan.delete_at IS NULL AND pertanyaan.tipe = 'star' AND periode.status = 1  ORDER BY pertanyaan.urutan");
                        $totaljs1 = mysqli_num_rows($pertanyaanjs1);
                        if ($totaljs1 > 0) {
                            while ($e = mysqli_fetch_array($pertanyaanjs1)) {
                                echo '$(".rating-' . $e['kode'] . '").starRating({';
                                echo "totalStars: 5,
                                        emptyColor: 'lightgray',
                                        hoverColor: 'salmon',     
                                        activeColor: 'cornflowerblue',
                                        strokeWidth: 0,
                                        useGradient: false,
                                        minRating: 1,
                                        callback: function(currentRating, el){
                                            $(\"#nilai-$e[kode]\").val(currentRating)
                                            console.log('DOM element ', el);
                                        }
                                    });\n";
                            }
                        }
                        ?>
                    },
                    error: function() {
                        $('#loadingeditquisioner').html('');
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        });
                        $('#tomboleditquisioner').prop('disabled', false);
                    }
                });
                return false;
            });

        });
    </script>
<?php
}
?>