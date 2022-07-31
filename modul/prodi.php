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
?>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Tanggal Dibuat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        <?php
        $data = mysqli_query($con, "SELECT * FROM prodi WHERE delete_at IS NULL");
        $no = 1;
        while ($a = mysqli_fetch_array($data)) {
            $paramakses = htmlspecialchars(json_encode($a, true));
            echo "<tr>";
            echo "<td>$no</td>";
            echo "<td><strong>".$a['nama']."</strong></td>";
            echo "<td>" . cetakHTML($a['deskripsi']) . "</td>";
            echo "<td>$a[create_at]</td>";
            echo "<td>
                  <div class='dropdown'>
                    <button type='button' class='btn p-0 dropdown-toggle hide-arrow' data-bs-toggle='dropdown'><i class='bx bx-dots-vertical-rounded'></i></button>
                    <div class='dropdown-menu'>
                        <a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaleditprodi'><i class='bx bx-edit-alt me-1'></i> Edit</a>
                        <a class='dropdown-item' onclick=\"confirmdelete(event,'?method=ajax&menu=prodi&aksi=delete&uuid=$a[uuid]','yakin delete prodi : <b>".htmlspecialchars($a['nama'])."</b>?','.tabelmodulprodi','#loadingdeleteprodi" . $a['uuid'] . "','#tomboldeleteprodi" . $a['uuid'] . "','.tablestandard')\" class='btn btn-sm btn-danger' id='tomboldeleteprodi" . $a['uuid'] . "'><span id='loadingdeleteprodi" . $a['uuid'] . "'></span><i class='bx bx-trash me-1'></i> Delete</a>
                    </div>
                </div>
            </td>";
            echo "</tr>";
            $no++;
        }
        ?>
    </tbody>
<?php
}


if ($aksi == "tambah") {
    if ($proses == "tambahprodi") {
        $nama = clear(isset($_POST['nama']) ? clear($_POST['nama']) : '');
        $deskripsi = clear(isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "INSERT INTO prodi (uuid, deskripsi, nama, create_at, create_by) VALUES (UUID(), '" . $deskripsi . "','" . $nama . "','" . $date . "','" . $user . "')");
        echo "INSERT INTO prodi (uuid, deskripsi, nama, create_at, create_by) VALUES (UUID(), '" . $deskripsi . "','" . $nama . "','" . $date . "','" . $user . "')";
    }
    tampildata($con);
} elseif ($aksi == "edit") {
    if ($proses == "editprodi") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $deskripsi = clear(isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '');
        $uuid = clear(isset($_POST['uuid']) ? clear($_POST['uuid']) : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "UPDATE prodi SET deskripsi = '" . $deskripsi . "', nama = '" . $nama . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    }
    tampildata($con);
} elseif ($aksi == "delete") {
    $uuid = clear(isset($_GET['uuid']) ? clear($_GET['uuid']) : '');
    $user = $_SESSION['uuid'];
    $date = date('Y-m-d h:i:s');
    mysqli_query($con, "UPDATE prodi SET delete_at = '" . $date . "', delete_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    tampildata($con);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-header">Table Prodi</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                    <div class="d-flex align-items-start align-items-sm-center gap-4 float-end">
                        <div class="button-wrapper">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modaltambahprodi">
                                <span class="tf-icons bx bx-book-add"></span>&nbsp; Tambah Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-striped" class="files" id="previews">

                        <div class="table-responsive">
                            <table class="table tablestandard tabelmodulprodi" id="">

                                <?php echo tampildata($con) ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL TAMBAH DATA PRODI -->
        <div class="modal fade" id="modaltambahprodi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formtambahprodi" action="?method=ajax&menu=prodi&aksi=tambah" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaltambahprodititle">Tambah Data Prodi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Prodi</label>
                                <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama Prodi" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-message">Desc Prodi</label>
                                <textarea class="form-control mytextarea" id="deskripsitambah" name="deskripsi" placeholder="Deskripsi Singkat Prodi" style="height: 320px;" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="tambahprodi">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboltambahprodi">
                            <span id="loadingtambahprodi"></span> &nbsp; Tambah prodi</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL TAMBAH DATA PRODI -->

        <!-- MODAL EDIT DATA PRODI -->
        <div class="modal fade" id="modaleditprodi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formeditprodi" action="?method=ajax&menu=prodi&aksi=edit" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaleditprodititle">Edit Data Prodi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Prodi</label>
                                <input type="text" id="nama-edit" name="nama" class="form-control" placeholder="Nama Prodi" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-message">Desc Prodi</label>
                                <textarea class="form-control mytextarea" id="deskripsiedit" name="deskripsi" placeholder="Deskripsi Singkat Prodi" style="height: 320px;" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="editprodi">
                        <input type="hidden" name="uuid" id="uuid" value="">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboleditprodi">
                            <span id="loadingeditprodi"></span> &nbsp; Edit prodi</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL EDIT DATA PRODI -->


        <script>
            $(document).ready(function() {
                // init datatables
                $('.tablestandard').DataTable();

                // tinymce.init({
                //     selector: '.mytextarea',
                //     plugins: 'image code',
                //     toolbar: 'undo redo | link image | code',
                // });

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

                document.addEventListener('focusin', function(e) {
                    if (e.target.closest('.tox-tinymce-aux, .moxman-window, .tam-assetmanager-root') !== null) {
                        e.stopImmediatePropagation();
                    }
                });

                $('#modaltambahprodi').on('hidden.bs.modal', function() {
                    $('#modaltambahprodi form')[0].reset();
                });

                $('#modaltambahprodi').on('shown.bs.modal', function() {

                })

                document.addEventListener('focusin', (e) => {
                    if (e.target.closest(".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
                        e.stopImmediatePropagation();
                    }
                });

                // tambah data
                $('#body').on('click', '#tomboltambahprodi', function(e) {
                    e.preventDefault();
                    tinyMCE.triggerSave();
                    var classname = ".tabelmodulprodi";
                    run('index.php?method=ajax&menu=prodi&aksi=tambah', 'formtambahprodi',
                        '#tomboltambahprodi', '#modaltambahprodi',
                        '#loadingtambahprodi', classname, '.tablestandard', 'sukses tambah data');
                    return false;
                });

                // edit data
                $('#body').on('click', '#tomboleditprodi', function(e) {
                    e.preventDefault();
                    tinyMCE.triggerSave();
                    var classname = ".tabelmodulprodi";
                    run('index.php?method=ajax&menu=prodi&aksi=edit', 'formeditprodi',
                        '#tomboleditprodi', '#modaleditprodi',
                        '#loadingeditprodi', classname, '.tablestandard', 'sukses edit data');
                    return false;
                });

                var modaleditprodi = document.getElementById('modaleditprodi');
                modaleditprodi.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget
                    var modal_data = JSON.parse(button.getAttribute('data-bs-val'))
                    // var modal_data = JSON.stringify(button.getAttribute('data-bs-val'));
                    var modal_title = modaleditprodi.querySelector('.modal-title')
                    var nama = modaleditprodi.querySelector('.modal-body #nama-edit')
                    var uuid = modaleditprodi.querySelector('.modal-footer #uuid')
                    var tombolmodal = modaleditprodi.querySelector(
                        '.modal-footer button[id=tomboleditprodi]')
                    modal_title.textContent = 'Edit Prodi : ' + $.htmlentities.decode(modal_data['nama'])
                    nama.value = $.htmlentities.decode(modal_data['nama'])
                    uuid.value = modal_data['uuid']
                    tinymce.get("deskripsiedit").setContent($.htmlentities.decode(modal_data['deskripsi']));
                    // tinyMCE.triggerSave();
                })
            });
        </script>
    <?php
}
    ?>