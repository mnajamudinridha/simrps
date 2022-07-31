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
            <th>Profile</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Email</th>
            <th>Prodi</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody class="table-border-bottom-0">
        <?php
        $data = mysqli_query($con, "SELECT user.*, prodi.nama as nama_prodi FROM user
                                    LEFT JOIN prodi ON user.prodi = prodi.uuid
                                    WHERE user.delete_at IS NULL");
        $no = 1;
        while ($a = mysqli_fetch_array($data)) {
            $paramakses = htmlspecialchars(json_encode($a));
            $photo = $a['photo'] != "" ? "storage/profile/small_" . $a['photo'] : 'assets/img/avatars/user.png';
            echo "<tr>";
            echo "<td>$no</td>";
            echo "<td>
                    <ul class='list-unstyled users-list m-0 avatar-group d-flex align-items-center'>
                        <li data-bs-toggle='tooltip' data-popup='tooltip-custom' data-bs-placement='top' class='avatar avatar-lg pull-up' title='' data-bs-original-title='" . $a['nama'] . "'>
                            <img src='" . $photo . "' alt='Avatar' class='rounded-circle'>
                        </li>
                    </ul>
                 </td>";
            echo "<td><strong>$a[nama]</strong></td>";
            echo "<td>$a[username]</td>";
            echo "<td>$a[email]</td>";
            echo "<td><strong>$a[nama_prodi]</strong></td>";
            echo "<td>
                  <div class='dropdown'>
                    <button type='button' class='btn p-0 dropdown-toggle hide-arrow' data-bs-toggle='dropdown'><i class='bx bx-dots-vertical-rounded'></i></button>
                    <div class='dropdown-menu'>
                        <a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaledituser'><i class='bx bx-edit-alt me-1'></i> Edit</a>
                        <a class='dropdown-item' onclick=\"confirmdelete(event,'?method=ajax&menu=user&aksi=delete&uuid=$a[uuid]','yakin delete user : <b>".htmlspecialchars($a['nama'])."</b>?','.tabelmoduluser','#loadingdeleteuser" . $a['uuid'] . "','#tomboldeleteuser" . $a['uuid'] . "','.tablestandard')\" class='btn btn-sm btn-danger' id='tomboldeleteuser" . $a['uuid'] . "'><span id='loadingdeleteuser" . $a['uuid'] . "'></span><i class='bx bx-trash me-1'></i> Delete</a>
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
    if ($proses == "tambahuser") {
        $files = "photo";
        $storage = "storage/profile/";
        $namaimages = "";
        $countfiles = count($_FILES[$files]['name']);
        $allowed = array('gif', 'png', 'jpg', 'jpeg');
        for ($i = 0; $i < $countfiles; $i++) {
            if (file_exists($_FILES[$files]['tmp_name'][$i]) || is_uploaded_file($_FILES[$files]['tmp_name'][$i])) {
                $extension = pathinfo($_FILES[$files]["name"][$i], PATHINFO_EXTENSION);
                if (!in_array($extension, $allowed)) {
                    http_response_code(406);
                    exit;
                }
                $original = $_FILES[$files]['tmp_name'][$i];
                $filename = time() . uniqid(rand()) . '.' . $extension;
                $moved = move_uploaded_file($original, $storage . $filename);
                if ($moved) {
                } else {
                    echo "Not uploaded because of error #" . $_FILES[$files]["error"][$i];
                }
                $image[$i] = new ImageResize($storage . $filename);
                $image[$i]->crop(200, 200);
                $image[$i]->save($storage . 'small_' . $filename);
                $image[$i] = new ImageResize($storage . $filename);
                $image[$i]->crop(500, 500);
                $image[$i]->save($storage . 'medium_' . $filename);
                $namaimages = $filename;
            }
        }
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $username = clear(isset($_POST['username']) ? $_POST['username'] : '');
        $password = clear(isset($_POST['password']) ? $_POST['password'] : '');
        $email = clear(isset($_POST['email']) ? $_POST['email'] : '');
        $prodi = clear(isset($_POST['prodi']) ? $_POST['prodi'] : '');
        $biodata = clear(isset($_POST['biodata']) ? $_POST['biodata'] : '');

        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');

        if ($namaimages != "") {
            if ($password != "") {
                mysqli_query($con, "INSERT INTO user (uuid, nama, username, password, email, photo, biodata, prodi, create_at, create_by) 
                VALUES (UUID(), '" . $nama . "','" . $username . "','" . password_hash($password, PASSWORD_BCRYPT) . "','" . $email . "',
                '" . $namaimages . "','" . $biodata . "','" . $prodi . "','" . $date . "','" . $user . "')");
            } else {
                mysqli_query($con, "INSERT INTO user (uuid, nama, username, email, photo, biodata, prodi, create_at, create_by) 
                VALUES (UUID(), '" . $nama . "','" . $username . "','" . $email . "',
                '" . $namaimages . "','" . $biodata . "','" . $prodi . "','" . $date . "','" . $user . "')");
            }
        } else {
            if ($password != "") {
                mysqli_query($con, "INSERT INTO user (uuid, nama, username, password, email, biodata, prodi, create_at, create_by) 
                VALUES (UUID(), '" . $nama . "','" . $username . "','" . password_hash($password, PASSWORD_BCRYPT) . "','" . $email . "',
                '" . $biodata . "','" . $prodi . "','" . $date . "','" . $user . "')");
            } else {
                mysqli_query($con, "INSERT INTO user (uuid, nama, username, email, biodata, prodi, create_at, create_by) 
                VALUES (UUID(), '" . $nama . "','" . $username . "','" . $email . "',
                '" . $biodata . "','" . $prodi . "','" . $date . "','" . $user . "')");
            }
        }
    }
    tampildata($con);
} elseif ($aksi == "edit") {
    if ($proses == "edituser") {
        $files = "photo";
        $storage = "storage/profile/";
        $namaimages = "";
        $countfiles = count($_FILES[$files]['name']);
        $allowed = array('gif', 'png', 'jpg', 'jpeg');
        for ($i = 0; $i < $countfiles; $i++) {
            if (file_exists($_FILES[$files]['tmp_name'][$i]) || is_uploaded_file($_FILES[$files]['tmp_name'][$i])) {
                $extension = pathinfo($_FILES[$files]["name"][$i], PATHINFO_EXTENSION);
                if (!in_array($extension, $allowed)) {
                    http_response_code(406);
                    exit;
                }
                $original = $_FILES[$files]['tmp_name'][$i];
                $filename = time() . uniqid(rand()) . '.' . $extension;
                $moved = move_uploaded_file($original, $storage . $filename);
                if ($moved) {
                } else {
                    echo "Not uploaded because of error #" . $_FILES[$files]["error"][$i];
                }
                $image[$i] = new ImageResize($storage . $filename);
                $image[$i]->crop(200, 200);
                $image[$i]->save($storage . 'small_' . $filename);
                $image[$i] = new ImageResize($storage . $filename);
                $image[$i]->crop(500, 500);
                $image[$i]->save($storage . 'medium_' . $filename);
                $namaimages = $filename;
            }
        }
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $username = clear(isset($_POST['username']) ? $_POST['username'] : '');
        $password = clear(isset($_POST['password']) ? $_POST['password'] : '');
        $email = clear(isset($_POST['email']) ? $_POST['email'] : '');
        $prodi = clear(isset($_POST['prodi']) ? $_POST['prodi'] : '');
        $biodata = clear(isset($_POST['biodata']) ? $_POST['biodata'] : '');
        $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');

        if ($namaimages != "") {
            if ($password != "") {
                mysqli_query($con, "UPDATE user SET nama = '" . $nama . "',
                                                    username = '" . $username . "',
                                                    password = '" . password_hash($password, PASSWORD_BCRYPT) . "',
                                                    email = '" . $email . "',
                                                    photo = '" . $namaimages . "',
                                                    biodata = '" . $biodata . "',
                                                    prodi = '" . $prodi . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
            } else {
                mysqli_query($con, "UPDATE user SET nama = '" . $nama . "',
                                                    username = '" . $username . "',
                                                    email = '" . $email . "',
                                                    photo = '" . $namaimages . "',
                                                    biodata = '" . $biodata . "',
                                                    prodi = '" . $prodi . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
            }
        } else {
            if ($password != "") {
                mysqli_query($con, "UPDATE user SET nama = '" . $nama . "',
                                                    username = '" . $username . "',
                                                    password = '" . password_hash($password, PASSWORD_BCRYPT) . "',
                                                    email = '" . $email . "',
                                                    biodata = '" . $biodata . "',
                                                    prodi = '" . $prodi . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
            } else {
                mysqli_query($con, "UPDATE user SET nama = '" . $nama . "',
                                                    username = '" . $username . "',
                                                    email = '" . $email . "',
                                                    biodata = '" . $biodata . "',
                                                    prodi = '" . $prodi . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
            }
        }
    }
    tampildata($con);
} elseif ($aksi == "delete") {
    $uuid = clear(isset($_GET['uuid']) ? $_GET['uuid'] : '');
    $user = $_SESSION['uuid'];
    $date = date('Y-m-d h:i:s');
    mysqli_query($con, "UPDATE user SET delete_at = '" . $date . "', delete_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    tampildata($con);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-header">Table Dosen</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                    <div class="d-flex align-items-start align-items-sm-center gap-4 float-end">
                        <div class="button-wrapper">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modaltambahuser">
                                <span class="tf-icons bx bx-book-add"></span>&nbsp; Tambah Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-striped" class="files" id="previews">

                        <div class="table-responsive">
                            <table class="table tablestandard tabelmoduluser" id="">

                                <?php echo tampildata($con) ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL TAMBAH DATA USER -->
        <div class="modal fade" id="modaltambahuser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <form id="formtambahuser" action="?method=ajax&menu=user&aksi=tambah" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaltambahusertitle">Tambah Data Dosen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Dosen</label>
                                <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama Dosen" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Username</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="Username" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Email" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Prodi</label>
                                <select class="tom-select-standard" id="prodi" name="prodi" placeholder="Pilih Prodi..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM prodi WHERE delete_at IS NULL");
                                    echo "<option value=''>Pilih Prodi...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['uuid'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Prodi
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Photo Profile</label>
                                <input type="file" id="photo" name="photo[]" class="form-control" accept=".jpg,.jpeg,.png,.gif" />
                                Allow File .jpg,.jpeg,.png,.gif
                                <div class="invalid-feedback">
                                    Pilih File yang didukung
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-message">Biodata</label>
                                <textarea class="form-control mytextarea" id="biodata" name="biodata" placeholder="Biodata Singkat Dosen" style="height: 320px;"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="tambahuser">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboltambahuser">
                            <span id="loadingtambahuser"></span> &nbsp; Tambah user</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL TAMBAH DATA USER -->

        <!-- MODAL EDIT DATA USER -->
        <div class="modal fade" id="modaledituser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <form id="formedituser" action="?method=ajax&menu=user&aksi=edit" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaleditusertitle">Edit Data Dosen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Dosen</label>
                                <input type="text" id="nama-edit" name="nama" class="form-control" placeholder="Nama Dosen" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Username</label>
                                <input type="text" id="username-edit" name="username" class="form-control" placeholder="Username" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Password</label>
                                <input type="password" id="password-edit" name="password" class="form-control" placeholder="Password" />
                                <span>Kosongkan jika tidak diupdate</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Email</label>
                                <input type="email" id="email-edit" name="email" class="form-control" placeholder="Email" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Prodi</label>
                                <select class="tom-select-standard-edit" id="prodi-edit" name="prodi" placeholder="Pilih Prodi..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM prodi WHERE delete_at IS NULL");
                                    echo "<option value=''>Pilih Prodi...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['uuid'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Prodi
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Photo Profile</label>
                                <input type="file" id="photo-edit" name="photo[]" class="form-control" accept=".jpg,.jpeg,.png,.gif" />
                                <span>Allow File .jpg,.jpeg,.png,.gif (Kosongkan jika tidak diupdate)</span>
                                <div class="invalid-feedback">
                                    Pilih File yang didukung
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-message">Biodata</label>
                                <textarea class="form-control mytextarea" id="biodata-edit" name="biodata" placeholder="Biodata Singkat Dosen" style="height: 320px;"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="edituser">
                        <input type="hidden" name="uuid" id="uuid" value="">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboledituser">
                            <span id="loadingedituser"></span> &nbsp; Edit user</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL EDIT DATA USER -->


        <script>
            $(document).ready(function() {
                // init datatables
                $('.tablestandard').DataTable();

                let selecticon = new TomSelect(".tom-select-standard", {
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
                let selecticonEdit = new TomSelect(".tom-select-standard-edit", {
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });

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

                $('#modaltambahuser').on('hidden.bs.modal', function() {
                    $('#modaltambahuser form')[0].reset();
                    selecticon.clear();
                });

                $('#modaltambahuser').on('shown.bs.modal', function() {

                })

                document.addEventListener('focusin', (e) => {
                    if (e.target.closest(".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
                        e.stopImmediatePropagation();
                    }
                });

                // tambah data
                $('#body').on('click', '#tomboltambahuser', function(e) {
                    e.preventDefault();
                    tinyMCE.triggerSave();
                    var classname = ".tabelmoduluser";
                    run('index.php?method=ajax&menu=user&aksi=tambah', 'formtambahuser',
                        '#tomboltambahuser', '#modaltambahuser',
                        '#loadingtambahuser', classname, '.tablestandard', 'sukses tambah data');
                    return false;
                });

                // edit data
                $('#body').on('click', '#tomboledituser', function(e) {
                    e.preventDefault();
                    tinyMCE.triggerSave();                    
                    var classname = ".tabelmoduluser";
                    run('index.php?method=ajax&menu=user&aksi=edit', 'formedituser',
                        '#tomboledituser', '#modaledituser',
                        '#loadingedituser', classname, '.tablestandard', 'sukses edit data');
                    return false;
                });

                var modaledituser = document.getElementById('modaledituser');
                modaledituser.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget
                    var modal_data = JSON.parse(button.getAttribute('data-bs-val'))
                    // var modal_data = JSON.stringify(button.getAttribute('data-bs-val'));
                    var modal_title = modaledituser.querySelector('.modal-title')
                    var nama = modaledituser.querySelector('.modal-body #nama-edit')
                    var username = modaledituser.querySelector('.modal-body #username-edit')
                    var email = modaledituser.querySelector('.modal-body #email-edit')
                    var prodi = modaledituser.querySelector('.modal-body #prodi-edit')
                    var uuid = modaledituser.querySelector('.modal-footer #uuid')
                    var tombolmodal = modaledituser.querySelector(
                        '.modal-footer button[id=tomboledituser]')
                    modal_title.textContent = 'Edit Dosen : ' + $.htmlentities.decode(modal_data['nama'])
                    nama.value = $.htmlentities.decode(modal_data['nama'])
                    username.value = $.htmlentities.decode(modal_data['username'])
                    email.value = $.htmlentities.decode(modal_data['email'])
                    selecticonEdit.setValue(modal_data['prodi']);
                    uuid.value = modal_data['uuid']
                    tinymce.get("biodata-edit").setContent($.htmlentities.decode(modal_data['biodata']));
                })
            });
        </script>
    <?php
}
    ?>