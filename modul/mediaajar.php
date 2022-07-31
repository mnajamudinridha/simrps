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
        $data = mysqli_query($con, "SELECT * FROM mediaajar WHERE delete_at IS NULL");
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
                        <a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaleditmediaajar'><i class='bx bx-edit-alt me-1'></i> Edit</a>
                        <a class='dropdown-item' onclick=\"confirmdelete(event,'?method=ajax&menu=mediaajar&aksi=delete&uuid=$a[uuid]','yakin delete mediaajar : <b>".htmlspecialchars($a['nama'])."</b>?','.tabelmodulmediaajar','#loadingdeletemediaajar" . $a['uuid'] . "','#tomboldeletemediaajar" . $a['uuid'] . "','.tablestandard')\" class='btn btn-sm btn-danger' id='tomboldeletemediaajar" . $a['uuid'] . "'><span id='loadingdeletemediaajar" . $a['uuid'] . "'></span><i class='bx bx-trash me-1'></i> Delete</a>
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
    if ($proses == "tambahmediaajar") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "INSERT INTO mediaajar (uuid, icon, kode, nama, create_at, create_by) VALUES (UUID(), '" . $icon . "','" . $kode . "','" . $nama . "','" . $date . "','" . $user . "')");
    }
    tampildata($con);
} elseif ($aksi == "edit") {
    if ($proses == "editmediaajar") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "UPDATE mediaajar SET icon = '" . $icon . "', kode = '" . $kode . "', nama = '" . $nama . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    }
    tampildata($con);
} elseif ($aksi == "delete") {
    $uuid = clear(isset($_GET['uuid']) ? $_GET['uuid'] : '');
    $user = $_SESSION['uuid'];
    $date = date('Y-m-d h:i:s');
    mysqli_query($con, "UPDATE mediaajar SET delete_at = '" . $date . "', delete_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    tampildata($con);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-header">Table Media Ajar</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                    <div class="d-flex align-items-start align-items-sm-center gap-4 float-end">
                        <div class="button-wrapper">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modaltambahmediaajar">
                                <span class="tf-icons bx bx-book-add"></span>&nbsp; Tambah Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-striped" class="files" id="previews">

                        <div class="table-responsive">
                            <table class="table tablestandard tabelmodulmediaajar" id="">

                                <?php echo tampildata($con) ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL TAMBAH DATA MEDIA AJAR -->
        <div class="modal fade" id="modaltambahmediaajar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formtambahmediaajar" action="?method=ajax&menu=mediaajar&aksi=tambah" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaltambahmediaajartitle">Tambah Data Media Ajar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Media Ajar</label>
                                <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama Media Ajar" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Media Ajar</label>
                                <input type="text" id="kode" name="kode" class="form-control" placeholder="Kode Media Ajar" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Media Ajar</label>
                                <select class="data-icon-tambah" id="icon" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Media Ajar
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="tambahmediaajar">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboltambahmediaajar">
                            <span id="loadingtambahmediaajar"></span> &nbsp; Tambah mediaajar</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL TAMBAH DATA MEDIA AJAR -->

        <!-- MODAL EDIT DATA MEDIA AJAR -->
        <div class="modal fade" id="modaleditmediaajar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formeditmediaajar" action="?method=ajax&menu=mediaajar&aksi=edit" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaleditmediaajartitle">Edit Data Media Ajar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Media Ajar</label>
                                <input type="text" id="nama-edit" name="nama" class="form-control" placeholder="Nama Media Ajar" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Media Ajar</label>
                                <input type="text" id="kode-edit" name="kode" class="form-control" placeholder="Kode Media Ajar" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Media Ajar</label>
                                <select class="data-icon-edit" id="icon-edit" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Media Ajar
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="editmediaajar">
                        <input type="hidden" name="uuid" id="uuid" value="">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboleditmediaajar">
                            <span id="loadingeditmediaajar"></span> &nbsp; Edit mediaajar</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL EDIT DATA MEDIA AJAR -->


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

                $('#modaltambahmediaajar').on('hidden.bs.modal', function() {
                    $('#modaltambahmediaajar form')[0].reset();
                    selecticonTambah.clear();
                });

                // tambah data
                $('#body').on('click', '#tomboltambahmediaajar', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulmediaajar";
                    run('index.php?method=ajax&menu=mediaajar&aksi=tambah', 'formtambahmediaajar',
                        '#tomboltambahmediaajar', '#modaltambahmediaajar',
                        '#loadingtambahmediaajar', classname, '.tablestandard', 'sukses tambah data');
                    return false;
                });

                // edit data
                $('#body').on('click', '#tomboleditmediaajar', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulmediaajar";
                    run('index.php?method=ajax&menu=mediaajar&aksi=edit', 'formeditmediaajar',
                        '#tomboleditmediaajar', '#modaleditmediaajar',
                        '#loadingeditmediaajar', classname, '.tablestandard', 'sukses edit data');
                    return false;
                });

                var modaleditmediaajar = document.getElementById('modaleditmediaajar');
                modaleditmediaajar.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget
                    var modal_data = JSON.parse(button.getAttribute('data-bs-val'))
                    var modal_title = modaleditmediaajar.querySelector('.modal-title')
                    var nama = modaleditmediaajar.querySelector('.modal-body #nama-edit')
                    var icon = modaleditmediaajar.querySelector('.modal-body #icon-edit')
                    var kode = modaleditmediaajar.querySelector('.modal-body #kode-edit')
                    var uuid = modaleditmediaajar.querySelector('.modal-footer #uuid')
                    selecticonEdit.setValue(modal_data['icon']);
                    var tombolmodal = modaleditmediaajar.querySelector(
                        '.modal-footer button[id=tomboleditmediaajar]')
                    modal_title.textContent = 'Edit Media Ajar : ' + $.htmlentities.decode(modal_data['nama'])
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