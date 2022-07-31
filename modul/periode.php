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
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        <?php
        $data = mysqli_query($con, "SELECT * FROM periode WHERE delete_at IS NULL");
        $no = 1;
        while ($a = mysqli_fetch_array($data)) {
            $paramakses = htmlspecialchars(json_encode($a));
            echo "<tr>";
            echo "<td>$no</td>";
            echo "<td><i class='tf-icon $a[icon] bx-md text-primary me-3'></i></td>";
            echo "<td>$a[kode]</td>";
            echo "<td><strong>$a[nama]</strong></td>";
            echo "<td>$a[create_at]</td>";
            $status = $a['status'] == 0 ? "Tidak Aktif" : "Aktif";
            echo "<td>$status</td>";
            echo "<td>
                  <div class='dropdown'>
                    <button type='button' class='btn p-0 dropdown-toggle hide-arrow' data-bs-toggle='dropdown'><i class='bx bx-dots-vertical-rounded'></i></button>
                    <div class='dropdown-menu'>
                        <a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaleditperiode'><i class='bx bx-edit-alt me-1'></i> Edit</a>
                        <a class='dropdown-item' onclick=\"confirmdelete(event,'?method=ajax&menu=periode&aksi=delete&uuid=$a[uuid]','yakin delete periode : <b>" . htmlspecialchars($a['nama']) . "</b>?','.tabelmodulperiode','#loadingdeleteperiode" . $a['uuid'] . "','#tomboldeleteperiode" . $a['uuid'] . "','.tablestandard')\" class='btn btn-sm btn-danger' id='tomboldeleteperiode" . $a['uuid'] . "'><span id='loadingdeleteperiode" . $a['uuid'] . "'></span><i class='bx bx-trash me-1'></i> Delete</a>
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
    if ($proses == "tambahperiode") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $status = clear(isset($_POST['status']) ? $_POST['status'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "INSERT INTO periode (uuid, icon, kode, nama, status, create_at, create_by) VALUES (UUID(), '" . $icon . "','" . $kode . "','" . $nama . "','" . $status . "','" . $date . "','" . $user . "')");
    }
    tampildata($con);
} elseif ($aksi == "edit") {
    if ($proses == "editperiode") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $status = clear(isset($_POST['status']) ? $_POST['status'] : '');
        $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "UPDATE periode SET icon = '" . $icon . "', kode = '" . $kode . "', status = '" . $status . "', nama = '" . $nama . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    }
    tampildata($con);
} elseif ($aksi == "delete") {
    $uuid = clear(isset($_GET['uuid']) ? $_GET['uuid'] : '');
    $user = $_SESSION['uuid'];
    $date = date('Y-m-d h:i:s');
    mysqli_query($con, "UPDATE periode SET delete_at = '" . $date . "', delete_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    tampildata($con);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-header">Table Periode</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                    <div class="d-flex align-items-start align-items-sm-center gap-4 float-end">
                        <div class="button-wrapper">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modaltambahperiode">
                                <span class="tf-icons bx bx-book-add"></span>&nbsp; Tambah Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-striped" class="files" id="previews">

                        <div class="table-responsive">
                            <table class="table tablestandard tabelmodulperiode" id="">

                                <?php echo tampildata($con) ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL TAMBAH DATA PERIODE -->
        <div class="modal fade" id="modaltambahperiode" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formtambahperiode" action="?method=ajax&menu=periode&aksi=tambah" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaltambahperiodetitle">Tambah Data Periode</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Periode</label>
                                <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama Periode" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Periode</label>
                                <input type="text" id="kode" name="kode" class="form-control" placeholder="Kode Periode" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Periode</label>
                                <select class="data-icon-tambah" id="icon" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Periode
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Status Periode</label>
                                <select class="data-icon-tambah form-control" id="status" name="status"  placeholder="Pilih Status..." required>
                                    <option value="0"> &nbsp; Tidak Aktif</option>
                                    <option value="1"> &nbsp; Aktif</option>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Periode
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="tambahperiode">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboltambahperiode">
                            <span id="loadingtambahperiode"></span> &nbsp; Tambah periode</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL TAMBAH DATA PERIODE -->

        <!-- MODAL EDIT DATA PERIODE -->
        <div class="modal fade" id="modaleditperiode" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formeditperiode" action="?method=ajax&menu=periode&aksi=edit" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaleditperiodetitle">Edit Data Periode</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Periode</label>
                                <input type="text" id="nama-edit" name="nama" class="form-control" placeholder="Nama Periode" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Periode</label>
                                <input type="text" id="kode-edit" name="kode" class="form-control" placeholder="Kode Periode" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Periode</label>
                                <select class="data-icon-edit" id="icon-edit" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Periode
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Status Periode</label>
                                <select class="data-icon-edit form-control" id="status-edit" name="status" placeholder="Pilih Status..." required>
                                    <?php
                                    if ($a['status'] == "1") {
                                        echo '<option value="0"> &nbsp; Tidak Aktif</option>
                                              <option value="1" selected> &nbsp; Aktif</option>';
                                    } else {
                                        echo '<option value="0"> &nbsp; Tidak Aktif</option>
                                              <option value="1"> &nbsp; Aktif</option>';
                                    }
                                    ?>

                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Periode
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="editperiode">
                        <input type="hidden" name="uuid" id="uuid" value="">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboleditperiode">
                            <span id="loadingeditperiode"></span> &nbsp; Edit periode</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL EDIT DATA PERIODE -->


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

                $('#modaltambahperiode').on('hidden.bs.modal', function() {
                    $('#modaltambahperiode form')[0].reset();
                    selecticonTambah.clear();
                });

                // tambah data
                $('#body').on('click', '#tomboltambahperiode', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulperiode";
                    run('index.php?method=ajax&menu=periode&aksi=tambah', 'formtambahperiode',
                        '#tomboltambahperiode', '#modaltambahperiode',
                        '#loadingtambahperiode', classname, '.tablestandard', 'sukses tambah data');
                    return false;
                });

                // edit data
                $('#body').on('click', '#tomboleditperiode', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulperiode";
                    run('index.php?method=ajax&menu=periode&aksi=edit', 'formeditperiode',
                        '#tomboleditperiode', '#modaleditperiode',
                        '#loadingeditperiode', classname, '.tablestandard', 'sukses edit data');
                    return false;
                });

                var modaleditperiode = document.getElementById('modaleditperiode');
                modaleditperiode.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget
                    var modal_data = JSON.parse(button.getAttribute('data-bs-val'))
                    var modal_title = modaleditperiode.querySelector('.modal-title')
                    var nama = modaleditperiode.querySelector('.modal-body #nama-edit')
                    var icon = modaleditperiode.querySelector('.modal-body #icon-edit')
                    var kode = modaleditperiode.querySelector('.modal-body #kode-edit')
                    var status = modaleditperiode.querySelector('.modal-body #status-edit')
                    var uuid = modaleditperiode.querySelector('.modal-footer #uuid')
                    selecticonEdit.setValue(modal_data['icon']);
                    var tombolmodal = modaleditperiode.querySelector(
                        '.modal-footer button[id=tomboleditperiode]')
                    modal_title.textContent = 'Edit Periode : ' + $.htmlentities.decode(modal_data['nama'])
                    nama.value = $.htmlentities.decode(modal_data['nama'])
                    icon.value = $.htmlentities.decode(modal_data['icon'])
                    status.value = $.htmlentities.decode(modal_data['status'])
                    kode.value = $.htmlentities.decode(modal_data['kode'])
                    uuid.value = modal_data['uuid']
                })
            });
        </script>
    <?php
}
    ?>