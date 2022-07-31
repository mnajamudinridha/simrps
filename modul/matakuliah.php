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
            <th>Kode</th>
            <th>Nama</th>
            <th>SKS</th>
            <th>SMT</th>
            <th>Prodi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        <?php
        $data = mysqli_query($con, "SELECT matakuliah.*, prodi.nama as nama_prodi FROM matakuliah 
                                    LEFT JOIN prodi ON matakuliah.prodi = prodi.uuid
                                    WHERE matakuliah.delete_at IS NULL ORDER BY matakuliah.prodi, matakuliah.semester, matakuliah.nama");
        $no = 1;
        while ($a = mysqli_fetch_array($data)) {
            $paramakses = htmlspecialchars(json_encode($a));
            echo "<tr>";
            echo "<td>$no</td>";
            echo "<td>$a[kode]</td>";
            echo "<td><strong>$a[nama]</strong></td>";
            echo "<td>$a[sks]</td>";
            echo "<td>$a[semester]</td>";
            echo "<td>$a[nama_prodi]</td>";
            echo "<td>
                  <div class='dropdown'>
                    <button type='button' class='btn p-0 dropdown-toggle hide-arrow' data-bs-toggle='dropdown'><i class='bx bx-dots-vertical-rounded'></i></button>
                    <div class='dropdown-menu'>
                        <a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaleditmatakuliah'><i class='bx bx-edit-alt me-1'></i> Edit</a>
                        <a class='dropdown-item' onclick=\"confirmdelete(event,'?method=ajax&menu=matakuliah&aksi=delete&uuid=$a[uuid]','yakin delete matakuliah : <b>".htmlspecialchars($a['nama'])."</b>?','.tabelmodulmatakuliah','#loadingdeletematakuliah" . $a['uuid'] . "','#tomboldeletematakuliah" . $a['uuid'] . "','.tablestandard')\" class='btn btn-sm btn-danger' id='tomboldeletematakuliah" . $a['uuid'] . "'><span id='loadingdeletematakuliah" . $a['uuid'] . "'></span><i class='bx bx-trash me-1'></i> Delete</a>
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
    if ($proses == "tambahmatakuliah") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $sks = clear(isset($_POST['sks']) ? $_POST['sks'] : '');
        $skst = clear(isset($_POST['skst']) ? $_POST['skst'] : '');
        $sksp = clear(isset($_POST['sksp']) ? $_POST['sksp'] : '');
        $semester = clear(isset($_POST['semester']) ? $_POST['semester'] : '');
        $prodi = clear(isset($_POST['prodi']) ? $_POST['prodi'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "INSERT INTO matakuliah (uuid, kode, nama, sks, skst, sksp,prodi, semester, create_at, create_by) 
                            VALUES (UUID(), '" . $kode . "','" . $nama . "','" . $sks . "','" . $skst . "','" . $sksp . "','" . $prodi . "','" . $semester. "','" . $date . "','" . $user . "')");
    }
    tampildata($con);
} elseif ($aksi == "edit") {
    if ($proses == "editmatakuliah") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $kode = clear(isset($_POST['kode']) ? $_POST['kode'] : '');
        $sks = clear(isset($_POST['sks']) ? $_POST['sks'] : '');
        $skst = clear(isset($_POST['skst']) ? $_POST['skst'] : '');
        $sksp = clear(isset($_POST['sksp']) ? $_POST['sksp'] : '');
        $semester = clear(isset($_POST['semester']) ? $_POST['semester'] : '');
        $prodi = clear(isset($_POST['prodi']) ? $_POST['prodi'] : '');
        $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "UPDATE matakuliah SET kode = '" . $kode . "', nama = '" . $nama . "',
                            sks = '" . $sks . "',skst = '" . $skst . "',sksp = '" . $sksp . "',
                            semester = '" . $semester . "', prodi = '" . $prodi . "',
                            update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    }
    tampildata($con);
} elseif ($aksi == "delete") {
    $uuid = clear(isset($_GET['uuid']) ? $_GET['uuid'] : '');
    $user = $_SESSION['uuid'];
    $date = date('Y-m-d h:i:s');
    mysqli_query($con, "UPDATE matakuliah SET delete_at = '" . $date . "', delete_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    tampildata($con);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-header">Table Matakuliah</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                    <div class="d-flex align-items-start align-items-sm-center gap-4 float-end">
                        <div class="button-wrapper">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modaltambahmatakuliah">
                                <span class="tf-icons bx bx-book-add"></span>&nbsp; Tambah Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-striped" class="files" id="previews">

                        <div class="table-responsive">
                            <table class="table tablestandard tabelmodulmatakuliah" id="">

                                <?php echo tampildata($con) ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL TAMBAH DATA MATAKULIAH -->
        <div class="modal fade" id="modaltambahmatakuliah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formtambahmatakuliah" action="?method=ajax&menu=matakuliah&aksi=tambah" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaltambahmatakuliahtitle">Tambah Data Matakuliah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Matakuliah</label>
                                <input type="text" id="kode" name="kode" class="form-control" placeholder="Kode Matakuliah" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Matakuliah</label>
                                <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama Matakuliah" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">SKS</label>
                                <input type="number" id="sks" name="sks" class="form-control" placeholder="SKS" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">SKS Teori</label>
                                <input type="number" id="skst" name="skst" class="form-control" placeholder="SKS Teori" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">SKS Praktek</label>
                                <input type="number" id="sksp" name="sksp" class="form-control" placeholder="SKS Praktek" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Semester</label>
                                <input type="number" id="semester" name="semester" class="form-control" placeholder="Semester" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Prodi</label>
                                <select class="data-prodi-tambah" id="prodi" name="prodi" placeholder="Pilih Prodi..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM prodi WHERE delete_at IS NULL");
                                    echo "<option value=''>Pilih Prodi...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['uuid'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Prodi Matakuliah
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="tambahmatakuliah">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboltambahmatakuliah">
                            <span id="loadingtambahmatakuliah"></span> &nbsp; Tambah matakuliah</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL TAMBAH DATA MATAKULIAH -->

        <!-- MODAL EDIT DATA MATAKULIAH -->
        <div class="modal fade" id="modaleditmatakuliah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formeditmatakuliah" action="?method=ajax&menu=matakuliah&aksi=edit" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaleditmatakuliahtitle">Edit Data Matakuliah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Kode Matakuliah</label>
                                <input type="text" id="kode-edit" name="kode" class="form-control" placeholder="Kode Matakuliah" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Matakuliah</label>
                                <input type="text" id="nama-edit" name="nama" class="form-control" placeholder="Nama Matakuliah" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">SKS</label>
                                <input type="number" id="sks-edit" name="sks" class="form-control" placeholder="SKS" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">SKS Teori</label>
                                <input type="number" id="skst-edit" name="skst" class="form-control" placeholder="SKS Teori" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">SKS Praktek</label>
                                <input type="number" id="sksp-edit" name="sksp" class="form-control" placeholder="SKS Praktek" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Semester</label>
                                <input type="number" id="semester-edit" name="semester" class="form-control" placeholder="Semester" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Prodi</label>
                                <select class="data-prodi-edit" id="prodi-edit" name="prodi" placeholder="Pilih Prodi..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM prodi WHERE delete_at IS NULL");
                                    echo "<option value=''>Pilih Prodi...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['uuid'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Prodi Matakuliah
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="editmatakuliah">
                        <input type="hidden" name="uuid" id="uuid" value="">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboleditmatakuliah">
                            <span id="loadingeditmatakuliah"></span> &nbsp; Edit matakuliah</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL EDIT DATA MATAKULIAH -->


        <script>
            $(document).ready(function() {
                // init datatables
                $('.tablestandard').DataTable();

                // select config
                let selecticonTambah = new TomSelect(".data-prodi-tambah", {
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });

                let selecticonEdit = new TomSelect(".data-prodi-edit", {
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });

                $('#modaltambahmatakuliah').on('hidden.bs.modal', function() {
                    $('#modaltambahmatakuliah form')[0].reset();
                    selecticonTambah.clear();
                });

                // tambah data
                $('#body').on('click', '#tomboltambahmatakuliah', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulmatakuliah";
                    run('index.php?method=ajax&menu=matakuliah&aksi=tambah', 'formtambahmatakuliah',
                        '#tomboltambahmatakuliah', '#modaltambahmatakuliah',
                        '#loadingtambahmatakuliah', classname, '.tablestandard', 'sukses tambah data');
                    return false;
                });

                // edit data
                $('#body').on('click', '#tomboleditmatakuliah', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulmatakuliah";
                    run('index.php?method=ajax&menu=matakuliah&aksi=edit', 'formeditmatakuliah',
                        '#tomboleditmatakuliah', '#modaleditmatakuliah',
                        '#loadingeditmatakuliah', classname, '.tablestandard', 'sukses edit data');
                    return false;
                });

                var modaleditmatakuliah = document.getElementById('modaleditmatakuliah');
                modaleditmatakuliah.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget
                    var modal_data = JSON.parse(button.getAttribute('data-bs-val'))
                    var modal_title = modaleditmatakuliah.querySelector('.modal-title')
                    var nama = modaleditmatakuliah.querySelector('.modal-body #nama-edit')
                    var kode = modaleditmatakuliah.querySelector('.modal-body #kode-edit')
                    var sks = modaleditmatakuliah.querySelector('.modal-body #sks-edit')
                    var skst = modaleditmatakuliah.querySelector('.modal-body #skst-edit')
                    var sksp = modaleditmatakuliah.querySelector('.modal-body #sksp-edit')
                    var prodi = modaleditmatakuliah.querySelector('.modal-body #prodi-edit')
                    var semester = modaleditmatakuliah.querySelector('.modal-body #semester-edit')
                    var uuid = modaleditmatakuliah.querySelector('.modal-footer #uuid')
                    selecticonEdit.setValue(modal_data['prodi']);
                    var tombolmodal = modaleditmatakuliah.querySelector(
                        '.modal-footer button[id=tomboleditmatakuliah]')
                    modal_title.textContent = 'Edit Matakuliah : ' + $.htmlentities.decode(modal_data['nama'])
                    nama.value = $.htmlentities.decode(modal_data['nama'])
                    kode.value = $.htmlentities.decode(modal_data['kode'])
                    sks.value = $.htmlentities.decode(modal_data['sks'])
                    skst.value = $.htmlentities.decode(modal_data['skst'])
                    sksp.value = $.htmlentities.decode(modal_data['sksp'])
                    prodi.value = $.htmlentities.decode(modal_data['prodi'])
                    semester.value = $.htmlentities.decode(modal_data['semester'])
                    uuid.value = modal_data['uuid']
                })
            });
        </script>
    <?php
}
    ?>