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
        $data = mysqli_query($con, "SELECT * FROM pertemuan WHERE delete_at IS NULL");
        $no = 1;
        while ($a = mysqli_fetch_array($data)) {
            $paramakses = htmlspecialchars(json_encode($a));
            echo "<tr>";
            echo "<td>$no</td>";
            echo "<td><i class='tf-icon $a[icon] bx-md text-primary me-3'></i></td>";
            echo "<td>$a[kode]</td>";
            echo "<td><strong>$a[nama]</strong></td>";
            echo "<td>$a[create_at]</td>";
            echo "<td>
                  <div class='dropdown'>
                    <button type='button' class='btn p-0 dropdown-toggle hide-arrow' data-bs-toggle='dropdown'><i class='bx bx-dots-vertical-rounded'></i></button>
                    <div class='dropdown-menu'>
                        <a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaleditpertemuan'><i class='bx bx-edit-alt me-1'></i> Edit</a>
                        <a class='dropdown-item' onclick=\"confirmdelete(event,'?method=ajax&menu=pertemuan&aksi=delete&uuid=$a[uuid]','yakin delete pertemuan : <b>".htmlspecialchars($a['nama'])."</b>?','.tabelmodulpertemuan','#loadingdeletepertemuan" . $a['uuid'] . "','#tomboldeletepertemuan" . $a['uuid'] . "','.tablestandard')\" class='btn btn-sm btn-danger' id='tomboldeletepertemuan" . $a['uuid'] . "'><span id='loadingdeletepertemuan" . $a['uuid'] . "'></span><i class='bx bx-trash me-1'></i> Delete</a>
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
    if ($proses == "tambahpertemuan") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "INSERT INTO pertemuan (uuid, icon, kode, nama, create_at, create_by) VALUES (UUID(), '" . $icon . "','" . $kode . "','" . $nama . "','" . $date . "','" . $user . "')");
    }
    tampildata($con);
} elseif ($aksi == "edit") {
    if ($proses == "editpertemuan") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "UPDATE pertemuan SET icon = '" . $icon . "', kode = '" . $kode . "', nama = '" . $nama . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    }
    tampildata($con);
} elseif ($aksi == "delete") {
    $uuid = clear(isset($_GET['uuid']) ? $_GET['uuid'] : '');
    $user = $_SESSION['uuid'];
    $date = date('Y-m-d h:i:s');
    mysqli_query($con, "UPDATE pertemuan SET delete_at = '" . $date . "', delete_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    tampildata($con);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-header">Table Pertemuan</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                    <div class="d-flex align-items-start align-items-sm-center gap-4 float-end">
                        <div class="button-wrapper">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modaltambahpertemuan">
                                <span class="tf-icons bx bx-book-add"></span>&nbsp; Tambah Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-striped" class="files" id="previews">

                        <div class="table-responsive">
                            <table class="table tablestandard tabelmodulpertemuan" id="">

                                <?php echo tampildata($con) ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL TAMBAH DATA PERTEMUAN -->
        <div class="modal fade" id="modaltambahpertemuan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formtambahpertemuan" action="?method=ajax&menu=pertemuan&aksi=tambah" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaltambahpertemuantitle">Tambah Data Pertemuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Pertemuan</label>
                                <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama Pertemuan" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Pertemuan</label>
                                <input type="text" id="kode" name="kode" class="form-control" placeholder="Kode Pertemuan" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Pertemuan</label>
                                <select class="data-icon-tambah" id="icon" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Pertemuan
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="tambahpertemuan">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboltambahpertemuan">
                            <span id="loadingtambahpertemuan"></span> &nbsp; Tambah pertemuan</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL TAMBAH DATA PERTEMUAN -->

        <!-- MODAL EDIT DATA PERTEMUAN -->
        <div class="modal fade" id="modaleditpertemuan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formeditpertemuan" action="?method=ajax&menu=pertemuan&aksi=edit" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaleditpertemuantitle">Edit Data Pertemuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Pertemuan</label>
                                <input type="text" id="nama-edit" name="nama" class="form-control" placeholder="Nama Pertemuan" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Pertemuan</label>
                                <input type="text" id="kode-edit" name="kode" class="form-control" placeholder="Kode Pertemuan" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Pertemuan</label>
                                <select class="data-icon-edit" id="icon-edit" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Pertemuan
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="editpertemuan">
                        <input type="hidden" name="uuid" id="uuid" value="">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboleditpertemuan">
                            <span id="loadingeditpertemuan"></span> &nbsp; Edit pertemuan</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL EDIT DATA PERTEMUAN -->


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

                $('#modaltambahpertemuan').on('hidden.bs.modal', function() {
                    $('#modaltambahpertemuan form')[0].reset();
                    selecticonTambah.clear();
                });

                // tambah data
                $('#body').on('click', '#tomboltambahpertemuan', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulpertemuan";
                    run('index.php?method=ajax&menu=pertemuan&aksi=tambah', 'formtambahpertemuan',
                        '#tomboltambahpertemuan', '#modaltambahpertemuan',
                        '#loadingtambahpertemuan', classname, '.tablestandard', 'sukses tambah data');
                    return false;
                });

                // edit data
                $('#body').on('click', '#tomboleditpertemuan', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulpertemuan";
                    run('index.php?method=ajax&menu=pertemuan&aksi=edit', 'formeditpertemuan',
                        '#tomboleditpertemuan', '#modaleditpertemuan',
                        '#loadingeditpertemuan', classname, '.tablestandard', 'sukses edit data');
                    return false;
                });

                var modaleditpertemuan = document.getElementById('modaleditpertemuan');
                modaleditpertemuan.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget
                    var modal_data = JSON.parse(button.getAttribute('data-bs-val'))
                    var modal_title = modaleditpertemuan.querySelector('.modal-title')
                    var nama = modaleditpertemuan.querySelector('.modal-body #nama-edit')
                    var icon = modaleditpertemuan.querySelector('.modal-body #icon-edit')
                    var kode = modaleditpertemuan.querySelector('.modal-body #kode-edit')
                    var uuid = modaleditpertemuan.querySelector('.modal-footer #uuid')
                    selecticonEdit.setValue(modal_data['icon']);
                    var tombolmodal = modaleditpertemuan.querySelector(
                        '.modal-footer button[id=tomboleditpertemuan]')
                    modal_title.textContent = 'Edit Pertemuan : ' + $.htmlentities.decode(modal_data['nama'])
                    nama.value = $.htmlentities.decode(modal_data['nama'])
                    icon.value = $.htmlentities.decode(modal_data['icon'])
                    kode.value = $.htmlentities.decode(modal_data['kode'])
                    uuid.value = modal_data['uuid']
                })
            });
        </script>
    <?php
}
    ?>