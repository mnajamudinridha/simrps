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
            <th>Pertanyaan</th>
            <th>Periode</th>
            <th>Tipe</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        <?php
        $data = mysqli_query($con, "SELECT pertanyaan.*, periode.nama as nama_periode FROM pertanyaan
                                    LEFT JOIN periode ON pertanyaan.periode = periode.uuid
                                    WHERE pertanyaan.delete_at IS NULL ORDER BY pertanyaan.id");
        $no = 1;
        while ($a = mysqli_fetch_array($data)) {
            $paramakses = htmlspecialchars(json_encode($a));
            echo "<tr>";
            echo "<td>$no</td>";
            echo "<td><i class='tf-icon $a[icon] bx-md text-primary me-3'></i></td>";
            echo "<td>$a[kode]</td>";
            echo "<td><strong>".$a['nama']."</strong></td>";
            echo "<td>$a[nama_periode]</td>";
            echo "<td>$a[tipe]</td>";
            echo "<td>
                  <div class='dropdown'>
                    <button type='button' class='btn p-0 dropdown-toggle hide-arrow' data-bs-toggle='dropdown'><i class='bx bx-dots-vertical-rounded'></i></button>
                    <div class='dropdown-menu'>
                        <a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaleditpertanyaan'><i class='bx bx-edit-alt me-1'></i> Edit</a>
                        <a class='dropdown-item' onclick=\"confirmdelete(event,'?method=ajax&menu=pertanyaan&aksi=delete&uuid=$a[uuid]','yakin delete pertanyaan : <b>".htmlspecialchars($a['nama'])."</b>?','.tabelmodulpertanyaan','#loadingdeletepertanyaan" . $a['uuid'] . "','#tomboldeletepertanyaan" . $a['uuid'] . "','.tablestandard')\" class='btn btn-sm btn-danger' id='tomboldeletepertanyaan" . $a['uuid'] . "'><span id='loadingdeletepertanyaan" . $a['uuid'] . "'></span><i class='bx bx-trash me-1'></i> Delete</a>
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
    if ($proses == "tambahpertanyaan") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $tipe = clear(isset($_POST['tipe']) ? $_POST['tipe'] : '');
        $periode = clear(isset($_POST['periode']) ? $_POST['periode'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "INSERT INTO pertanyaan (uuid, icon, kode, nama, tipe, periode, create_at, create_by) 
                            VALUES (UUID(), '" . $icon . "','" . $kode . "','" . $nama . "','" . $tipe . "','" . $periode . "','" . $date . "','" . $user . "')");
    }
    tampildata($con);
} elseif ($aksi == "edit") {
    if ($proses == "editpertanyaan") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $tipe = clear(isset($_POST['tipe']) ? $_POST['tipe'] : '');
        $periode = clear(isset($_POST['periode']) ? $_POST['periode'] : '');
        $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "UPDATE pertanyaan SET icon = '" . $icon . "', tipe = '" . $tipe . "',kode = '" . $kode . "',periode = '" . $periode . "', nama = '" . $nama . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    }
    tampildata($con);
} elseif ($aksi == "delete") {
    $uuid = clear(isset($_GET['uuid']) ? $_GET['uuid'] : '');
    $user = $_SESSION['uuid'];
    $date = date('Y-m-d h:i:s');
    mysqli_query($con, "UPDATE pertanyaan SET delete_at = '" . $date . "', delete_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    tampildata($con);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-header">Table Pertanyaan</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                    <div class="d-flex align-items-start align-items-sm-center gap-4 float-end">
                        <div class="button-wrapper">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modaltambahpertanyaan">
                                <span class="tf-icons bx bx-book-add"></span>&nbsp; Tambah Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-striped" class="files" id="previews">

                        <div class="table-responsive">
                            <table class="table tablestandard tabelmodulpertanyaan" id="">

                                <?php echo tampildata($con) ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL TAMBAH DATA PERTANYAAN -->
        <div class="modal fade" id="modaltambahpertanyaan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formtambahpertanyaan" action="?method=ajax&menu=pertanyaan&aksi=tambah" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaltambahpertanyaantitle">Tambah Data Pertanyaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Pertanyaan</label>
                                <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama Pertanyaan" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Pertanyaan</label>
                                <input type="text" id="kode" name="kode" class="form-control" placeholder="Kode Pertanyaan" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Tipe Pertanyaan</label>
                                <input type="text" id="tipe" name="tipe" class="form-control" placeholder="Tipe Pertanyaan" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Pertanyaan</label>
                                <select class="data-icon-tambah" id="icon" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Pertanyaan
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Periode Pertanyaan</label>
                                <select class="data-pertanyaan-tambah" id="periode" name="periode" placeholder="Pilih Periode..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM periode");
                                    echo "<option value=''>Pilih Periode...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        $status = $a['status'] == 1 ? "Aktif" : "Tidak Aktif";
                                        echo '<option value="' . $a['uuid'] . '"></i> &nbsp; ' . $a['nama'] . '('.$status.')</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Status Pertanyaan
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="tambahpertanyaan">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboltambahpertanyaan">
                            <span id="loadingtambahpertanyaan"></span> &nbsp; Tambah pertanyaan</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL TAMBAH DATA PERTANYAAN -->

        <!-- MODAL EDIT DATA PERTANYAAN -->
        <div class="modal fade" id="modaleditpertanyaan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formeditpertanyaan" action="?method=ajax&menu=pertanyaan&aksi=edit" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaleditpertanyaantitle">Edit Data Pertanyaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Pertanyaan</label>
                                <input type="text" id="nama-edit" name="nama" class="form-control" placeholder="Nama Pertanyaan" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Pertanyaan</label>
                                <input type="text" id="kode-edit" name="kode" class="form-control" placeholder="Kode Pertanyaan" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Tipe Pertanyaan</label>
                                <input type="text" id="tipe-edit" name="tipe" class="form-control" placeholder="Tipe Pertanyaan" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Pertanyaan</label>
                                <select class="data-icon-edit" id="icon-edit" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Pertanyaan
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Periode Pertanyaan</label>
                                <select class="data-pertanyaan-edit" id="periode-edit" name="periode" placeholder="Pilih Periode..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM periode");
                                    echo "<option value=''>Pilih Periode...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        $status = $a['status'] == 1 ? "Aktif" : "Tidak Aktif";
                                        echo '<option value="' . $a['uuid'] . '"></i> &nbsp; ' . $a['nama'] . '('.$status.')</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Status Pertanyaan
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="editpertanyaan">
                        <input type="hidden" name="uuid" id="uuid" value="">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboleditpertanyaan">
                            <span id="loadingeditpertanyaan"></span> &nbsp; Edit pertanyaan</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL EDIT DATA PERTANYAAN -->


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

                let selecticonTambahPertanyaan = new TomSelect(".data-pertanyaan-tambah", {
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                });
                let selecticonEditPertanyaan = new TomSelect(".data-pertanyaan-edit", {
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                });

                $('#modaltambahpertanyaan').on('hidden.bs.modal', function() {
                    $('#modaltambahpertanyaan form')[0].reset();
                    selecticonTambah.clear();
                    selecticonTambahPertanyaan.clear();
                });

                // tambah data
                $('#body').on('click', '#tomboltambahpertanyaan', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulpertanyaan";
                    run('index.php?method=ajax&menu=pertanyaan&aksi=tambah', 'formtambahpertanyaan',
                        '#tomboltambahpertanyaan', '#modaltambahpertanyaan',
                        '#loadingtambahpertanyaan', classname, '.tablestandard', 'sukses tambah data');
                    return false;
                });

                // edit data
                $('#body').on('click', '#tomboleditpertanyaan', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulpertanyaan";
                    run('index.php?method=ajax&menu=pertanyaan&aksi=edit', 'formeditpertanyaan',
                        '#tomboleditpertanyaan', '#modaleditpertanyaan',
                        '#loadingeditpertanyaan', classname, '.tablestandard', 'sukses edit data');
                    return false;
                });

                var modaleditpertanyaan = document.getElementById('modaleditpertanyaan');
                modaleditpertanyaan.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget
                    var modal_data = JSON.parse(button.getAttribute('data-bs-val'))
                    var modal_title = modaleditpertanyaan.querySelector('.modal-title')
                    var nama = modaleditpertanyaan.querySelector('.modal-body #nama-edit')
                    var icon = modaleditpertanyaan.querySelector('.modal-body #icon-edit')
                    var kode = modaleditpertanyaan.querySelector('.modal-body #kode-edit')
                    var tipe = modaleditpertanyaan.querySelector('.modal-body #tipe-edit')
                    var periode = modaleditpertanyaan.querySelector('.modal-body #periode-edit')
                    var uuid = modaleditpertanyaan.querySelector('.modal-footer #uuid')
                    selecticonEdit.setValue(modal_data['icon']);
                    var tombolmodal = modaleditpertanyaan.querySelector(
                        '.modal-footer button[id=tomboleditpertanyaan]')
                    modal_title.textContent = 'Edit Pertanyaan : ' + $.htmlentities.decode(modal_data['nama'])
                    nama.value = $.htmlentities.decode(modal_data['nama'])
                    icon.value = modal_data['icon']
                    kode.value = $.htmlentities.decode(modal_data['kode'])
                    tipe.value = $.htmlentities.decode(modal_data['tipe'])
                    periode.value = $.htmlentities.decode(modal_data['periode'])
                    uuid.value = modal_data['uuid']
                })
            });
        </script>
    <?php
}
    ?>