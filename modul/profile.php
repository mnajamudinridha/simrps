<?php
defined("BASEPATH") or exit("No direct access allowed");

use \Gumlet\ImageResize;

$aksi = clear(isset($_GET['aksi']) ? $_GET['aksi'] : '');
$proses = clear(isset($_POST['proses']) ? $_POST['proses'] : '');


function tampildata($con)
{
    $biodata = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM user WHERE uuid='" . $_SESSION['uuid'] . "'"));
?>

    <h5 class="card-header">Profile Details</h5>
    <!-- Account -->
    <form id="formAccountSettings" method="POST" class="mb-5">
        <div class="card-body">
            <div class="d-flex align-items-start align-items-sm-center gap-4">
                <img src="<?php echo $_SESSION['photo']; ?>" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                <div class="row">
                    <div class="col mb-3">
                        <label for="nama" class="form-label">Photo Profile</label>
                        <input type="file" id="photo" name="photo[]" class="form-control" accept=".jpg,.jpeg,.png,.gif" />
                        <span>Allow File .jpg,.jpeg,.png,.gif (Kosongkan jika tidak diupdate)</span>
                        <div class="invalid-feedback">
                            Pilih File yang didukung
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-0" />
        <div class="card-body">
            <div class="row">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="nama" class="form-label">Nama Dosen</label>
                        <input type="text" id="nama" name="nama" class="form-control" value="<?php echo $biodata['nama'] ?>" placeholder="Nama Dosen" required />
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="nama" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?php echo $biodata['username'] ?>" placeholder="Username" required />
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="nama" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $biodata['email'] ?>" placeholder="Email" />
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="nama" class="form-label">Prodi</label>
                        <select class="tom-select-standard-profile" id="prodi" name="prodi" placeholder="Pilih Prodi..." required>
                            <?php
                            $query = mysqli_query($con, "SELECT * FROM prodi WHERE delete_at IS NULL");
                            echo "<option value=''>Pilih Prodi...</option>";
                            while ($a = mysqli_fetch_array($query)) {
                                if ($a['uuid'] == $biodata['prodi']) {
                                    echo '<option value="' . $a['uuid'] . '" selected></i> &nbsp; ' . $a['nama'] . '</option>';
                                } else {
                                    echo '<option value="' . $a['uuid'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                }
                            } ?>
                        </select>
                        <div class="invalid-feedback">
                            Pilih Prodi
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-message">Biodata</label>
                        <textarea class="form-control mytextarea" id="biodataedit" name="biodata" placeholder="Biodata Singkat Dosen" style="height: 320px;"><?php echo $biodata['biodata']; ?></textarea>
                    </div>
                </div>

            </div>
            <div class="mt-2">
                <input type="hidden" name="proses" value="editprofile">
                <input type="hidden" name="uuid" id="uuid" value="<?php echo $biodata['uuid']; ?>">
                <button type="button" class="btn btn-primary float-end me-2" id="tomboleditprofile"><span id="loadingeditprofile"></span> &nbsp; Edit Profile</button>
            </div>
        </div>
    </form>
    <!-- /Account -->
<?php
}

if ($aksi == "edit") {
    if ($proses == "editprofile") {
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
        $email = clear(isset($_POST['email']) ? $_POST['email'] : '');
        $prodi = clear(isset($_POST['prodi']) ? $_POST['prodi'] : '');
        $biodata = clear(isset($_POST['biodata']) ? $_POST['biodata'] : '');
        $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        if ($namaimages != "") {
            mysqli_query($con, "UPDATE user SET nama = '" . $nama . "',
                                                    username = '" . $username . "',
                                                    email = '" . $email . "',
                                                    photo = '" . $namaimages . "',
                                                    biodata = '" . $biodata . "',
                                                    prodi = '" . $prodi . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
            $_SESSION['username'] = $username;
            $_SESSION['nama'] = $nama;
            $_SESSION['email'] = $email;
            $_SESSION['photo'] = $namaimages != "" ? "storage/profile/small_" . $namaimages : 'assets/img/avatars/user.png';
        } else {
            mysqli_query($con, "UPDATE user SET nama = '" . $nama . "',
                                                    username = '" . $username . "',
                                                    email = '" . $email . "',
                                                    biodata = '" . $biodata . "',
                                                    prodi = '" . $prodi . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
            $_SESSION['username'] = $username;
            $_SESSION['nama'] = $nama;
            $_SESSION['email'] = $email;
        }
    }
    tampildata($con);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php?menu=profile"><i class="bx bx-user me-1"></i> Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?menu=setting"><i class="bx bx-link-alt me-1"></i> Setting</a>
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
            let selecticonEdit = new TomSelect(".tom-select-standard-profile", {
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });

            tinymce.init({
                selector: '#biodataedit',
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
                    url: 'index.php?method=ajax&menu=profile&aksi=edit',
                    data: new FormData(document.getElementById('formAccountSettings')),
                    // data: $('#formuserlevel').serialize(),
                    processData: false,
                    cache: false,
                    contentType: false,
                    success: function(response) {
                        $('#loadingeditprofile').html('');
                        $('#profile-nama').html($('#nama').val());                             
                        $('#profile-email').html($('#email').val());                             
                        $('#profile-picture').html('<img src="'+ $('#uploadedAvatar').attr('src') +'" alt class="w-px-40 h-auto rounded-circle" />');
                        $('#profile-picture2').html('<img src="'+ $('#uploadedAvatar').attr('src') +'" alt class="w-px-40 h-auto rounded-circle" />');
                        Swal.fire(
                            'Sukses!',
                            'Sukses Update Level Dosen',
                            'success'
                        );
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