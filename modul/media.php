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
            <th>Icon</th>
            <th>Kode</th>
            <th>Nama</th>
            <th>Tanggal Dibuat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        <?php
        $data = mysqli_query($con, "SELECT * FROM media WHERE delete_at IS NULL");
        $no = 1;
        while ($a = mysqli_fetch_array($data)) {
            $paramakses = htmlspecialchars(json_encode($a));
            echo "<tr>";
            echo "<td>$no</td>";
            echo "<td><i class='tf-icon $a[icon] bx-md text-primary me-3'></i></td>";
            echo "<td>$a[kode]</td>";
            echo "<td><strong>".$a['nama']."</strong></td>";
            echo "<td>$a[create_at]</td>";
            echo "<td>
                  <div class='dropdown'>
                    <button type='button' class='btn p-0 dropdown-toggle hide-arrow' data-bs-toggle='dropdown'><i class='bx bx-dots-vertical-rounded'></i></button>
                    <div class='dropdown-menu'>
                        <a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaleditmedia'><i class='bx bx-edit-alt me-1'></i> Edit</a>
                        <a class='dropdown-item' onclick=\"confirmdelete(event,'?method=ajax&menu=media&aksi=delete&uuid=$a[uuid]','yakin delete media : <b>".htmlspecialchars($a['nama'])."</b>?','.tabelmodulmedia','#loadingdeletemedia" . $a['uuid'] . "','#tomboldeletemedia" . $a['uuid'] . "','.tablestandard')\" class='btn btn-sm btn-danger' id='tomboldeletemedia" . $a['uuid'] . "'><span id='loadingdeletemedia" . $a['uuid'] . "'></span><i class='bx bx-trash me-1'></i> Delete</a>
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
    if ($proses == "tambahmedia") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "INSERT INTO media (uuid, icon, kode, nama, create_at, create_by) VALUES (UUID(), '" . $icon . "','" . $kode . "','" . $nama . "','" . $date . "','" . $user . "')");
    }
    tampildata($con);
} elseif ($aksi == "edit") {
    if ($proses == "editmedia") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "UPDATE media SET icon = '" . $icon . "', kode = '" . $kode . "', nama = '" . $nama . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    }
    tampildata($con);
} elseif ($aksi == "delete") {
    $uuid = clear(isset($_GET['uuid']) ? $_GET['uuid'] : '');
    $user = $_SESSION['uuid'];
    $date = date('Y-m-d h:i:s');
    mysqli_query($con, "UPDATE media SET delete_at = '" . $date . "', delete_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    tampildata($con);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-header">Table Media</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                    <div class="d-flex align-items-start align-items-sm-center gap-4 float-end">
                        <div class="button-wrapper">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modaltambahmedia">
                                <span class="tf-icons bx bx-book-add"></span>&nbsp; Tambah Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-striped" class="files" id="previews">

                        <div class="table-responsive">
                            <table class="table tablestandard tabelmodulmedia" id="">

                                <?php echo tampildata($con) ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL TAMBAH DATA MEDIA -->
        <div class="modal fade" id="modaltambahmedia" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formtambahmedia" action="?method=ajax&menu=media&aksi=tambah" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaltambahmediatitle">Tambah Data Media</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Media</label>
                                <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama Media" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Media</label>
                                <input type="text" id="kode" name="kode" class="form-control" placeholder="Kode Media" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Media</label>
                                <select class="data-icon-tambah" id="icon" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Media
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="tambahmedia">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboltambahmedia">
                            <span id="loadingtambahmedia"></span> &nbsp; Tambah media</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL TAMBAH DATA MEDIA -->

        <!-- MODAL EDIT DATA MEDIA -->
        <div class="modal fade" id="modaleditmedia" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formeditmedia" action="?method=ajax&menu=media&aksi=edit" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaleditmediatitle">Edit Data Media</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Media</label>
                                <input type="text" id="nama-edit" name="nama" class="form-control" placeholder="Nama Media" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Media</label>
                                <input type="text" id="kode-edit" name="kode" class="form-control" placeholder="Kode Media" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Media</label>
                                <select class="data-icon-edit" id="icon-edit" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Media
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="editmedia">
                        <input type="hidden" name="uuid" id="uuid" value="">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboleditmedia">
                            <span id="loadingeditmedia"></span> &nbsp; Edit media</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL EDIT DATA MEDIA -->


        <script>
            $(document).ready(function() {
                // init datatables
                $('.tablestandard').DataTable();

                // select config
                let selecticonTambah = new TomSelect(".data-icon-tambah", {
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    render: {
                        option: function(data, escape) {
                            return `<div><i class="${data.src} mb-2"></i> ${data.text}</div>`;
                        },
                        item: function(item, escape) {
                            return `<div><i class="${item.src} mb-2"></i> ${item.text}</div>`;
                        }
                    }
                });

                let selecticonEdit = new TomSelect(".data-icon-edit", {
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    render: {
                        option: function(data, escape) {
                            return `<div><i class="${data.src} mb-2"></i> ${data.text}</div>`;
                        },
                        item: function(item, escape) {
                            return `<div><i class="${item.src} mb-2"></i> ${item.text}</div>`;
                        }
                    }
                });

                $('#modaltambahmedia').on('hidden.bs.modal', function() {
                    $('#modaltambahmedia form')[0].reset();
                    selecticonTambah.clear();
                });

                // tambah data
                $('#body').on('click', '#tomboltambahmedia', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulmedia";
                    run('index.php?method=ajax&menu=media&aksi=tambah', 'formtambahmedia',
                        '#tomboltambahmedia', '#modaltambahmedia',
                        '#loadingtambahmedia', classname, '.tablestandard', 'sukses tambah data');
                    return false;
                });

                // edit data
                $('#body').on('click', '#tomboleditmedia', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulmedia";
                    run('index.php?method=ajax&menu=media&aksi=edit', 'formeditmedia',
                        '#tomboleditmedia', '#modaleditmedia',
                        '#loadingeditmedia', classname, '.tablestandard', 'sukses edit data');
                    return false;
                });

                var modaleditmedia = document.getElementById('modaleditmedia');
                modaleditmedia.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget
                    var modal_data = JSON.parse(button.getAttribute('data-bs-val'))
                    var modal_title = modaleditmedia.querySelector('.modal-title')
                    var nama = modaleditmedia.querySelector('.modal-body #nama-edit')
                    var icon = modaleditmedia.querySelector('.modal-body #icon-edit')
                    var kode = modaleditmedia.querySelector('.modal-body #kode-edit')
                    var uuid = modaleditmedia.querySelector('.modal-footer #uuid')
                    selecticonEdit.setValue(modal_data['icon']);
                    var tombolmodal = modaleditmedia.querySelector(
                        '.modal-footer button[id=tomboleditmedia]')
                    modal_title.textContent = 'Edit Media : ' + $.htmlentities.decode(modal_data['nama'])
                    nama.value = $.htmlentities.decode(modal_data['nama'])
                    icon.value = modal_data['icon']
                    kode.value = $.htmlentities.decode(modal_data['kode'])
                    uuid.value = modal_data['uuid']
                })
            });
        </script>
    <?php
}
    ?>