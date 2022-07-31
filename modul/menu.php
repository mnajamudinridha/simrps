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
            <th>Modul</th>
            <th>Nama</th>
            <th>Jenis</th>
            <th>Url</th>
            <th>Urutan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        <?php
        $data = mysqli_query($con, "SELECT * FROM menu WHERE delete_at IS NULL");
        $no = 1;
        while ($a = mysqli_fetch_array($data)) {
            $paramakses = htmlspecialchars(json_encode($a));
            echo "<tr>";
            echo "<td>$no</td>";
            echo "<td><i class='tf-icon $a[icon] bx-md text-primary me-3'></i></td>";
            echo "<td>$a[modul]</td>";
            echo "<td><strong>$a[nama]</strong></td>";
            echo "<td>$a[jenis]</td>";
            echo "<td>$a[url]</td>";
            echo "<td>$a[urutan]</td>";
            echo "<td>
                  <div class='dropdown'>
                    <button type='button' class='btn p-0 dropdown-toggle hide-arrow' data-bs-toggle='dropdown'><i class='bx bx-dots-vertical-rounded'></i></button>
                    <div class='dropdown-menu'>
                        <a class='dropdown-item' data-bs-toggle='modal' data-bs-val='$paramakses' data-bs-target='#modaleditmenu'><i class='bx bx-edit-alt me-1'></i> Edit</a>
                        <a class='dropdown-item' onclick=\"confirmdelete(event,'?method=ajax&menu=menu&aksi=delete&uuid=$a[uuid]','yakin delete menu : <b>".htmlspecialchars($a['nama'])."</b>?','.tabelmodulmenu','#loadingdeletemenu" . $a['uuid'] . "','#tomboldeletemenu" . $a['uuid'] . "','.tablestandard')\" class='btn btn-sm btn-danger' id='tomboldeletemenu" . $a['uuid'] . "'><span id='loadingdeletemenu" . $a['uuid'] . "'></span><i class='bx bx-trash me-1'></i> Delete</a>
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
    if ($proses == "tambahmenu") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $jenis = clear(isset($_POST['jenis']) ? $_POST['jenis'] : '');
        $url = clear(isset($_POST['url']) ? $_POST['url'] : '');
        $urutan = clear(isset($_POST['urutan']) ? $_POST['urutan'] : '');
        $modul = clear(isset($_POST['modul']) ? $_POST['modul'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "INSERT INTO menu (uuid, icon, modul, jenis, url, urutan, nama, create_at, create_by) VALUES (UUID(), '" . $icon . "', '" . $modul . "','" . $jenis . "','" . $url . "','" . $urutan . "','" . $nama . "','" . $date . "','" . $user . "')");
    }
    tampildata($con);
} elseif ($aksi == "edit") {
    if ($proses == "editmenu") {
        $nama = clear(isset($_POST['nama']) ? $_POST['nama'] : '');
        $icon = clear(isset($_POST['icon']) ? $_POST['icon'] : '');
        $jenis = clear(isset($_POST['jenis']) ? $_POST['jenis'] : '');
        $url = clear(isset($_POST['url']) ? $_POST['url'] : '');
        $modul = clear(isset($_POST['modul']) ? $_POST['modul'] : '');
        $urutan = clear(isset($_POST['urutan']) ? $_POST['urutan'] : '');
        $uuid = clear(isset($_POST['uuid']) ? $_POST['uuid'] : '');
        $user = $_SESSION['uuid'];
        $date = date('Y-m-d h:i:s');
        mysqli_query($con, "UPDATE menu SET icon = '" . $icon . "', modul = '" . $modul . "', jenis = '" . $jenis . "', urutan = '" . $urutan . "', url = '" . $url . "',nama = '" . $nama . "', update_at = '" . $date . "', update_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    }
    tampildata($con);
} elseif ($aksi == "delete") {
    $uuid = clear(isset($_GET['uuid']) ? $_GET['uuid'] : '');
    $user = $_SESSION['uuid'];
    $date = date('Y-m-d h:i:s');
    mysqli_query($con, "UPDATE menu SET delete_at = '" . $date . "', delete_by = '" . $user . "' WHERE uuid = '" . $uuid . "'");
    tampildata($con);
} else {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="card-header">Table Menu</h4>
                <div class="card-body">
                    <p><?php echo getenv('web_desc'); ?></p>
                    <div class="d-flex align-items-start align-items-sm-center gap-4 float-end">
                        <div class="button-wrapper">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modaltambahmenu">
                                <span class="tf-icons bx bx-book-add"></span>&nbsp; Tambah Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table table-striped" class="files" id="previews">

                        <div class="table-responsive text-nowrap">
                            <table class="table tablestandard tabelmodulmenu" id="">

                                <?php echo tampildata($con) ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL TAMBAH DATA MENU -->
        <div class="modal fade" id="modaltambahmenu" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formtambahmenu" action="?method=ajax&menu=menu&aksi=tambah" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaltambahmenutitle">Tambah Data Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Menu</label>
                                <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama Menu" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Jenis Menu</label>
                                <input type="text" id="jenis" name="jenis" class="form-control" placeholder="Jenis Menu" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Modul Menu</label>
                                <input type="text" id="modul" name="modul" class="form-control" placeholder="Modul Menu" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Url Menu</label>
                                <input type="text" id="url" name="url" class="form-control" placeholder="Url Menu" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Urutan Menu</label>
                                <input type="number" id="urutan" name="urutan" class="form-control" placeholder="Urutan Menu" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Menu</label>
                                <select class="data-icon-tambah" id="icon" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Menu
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="tambahmenu">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboltambahmenu">
                            <span id="loadingtambahmenu"></span> &nbsp; Tambah menu</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL TAMBAH DATA MENU -->

        <!-- MODAL EDIT DATA MENU -->
        <div class="modal fade" id="modaleditmenu" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form id="formeditmenu" action="?method=ajax&menu=menu&aksi=edit" method="POST" class="needs-validation modal-content" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modaleditmenutitle">Edit Data Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Nama Menu</label>
                                <input type="text" id="nama-edit" name="nama" class="form-control" placeholder="Nama Menu" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Jenis Menu</label>
                                <input type="text" id="jenis-edit" name="jenis" class="form-control" placeholder="Jenis Menu" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Modul Menu</label>
                                <input type="text" id="modul-edit" name="modul" class="form-control" placeholder="Modul Menu" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Url Menu</label>
                                <input type="text" id="url-edit" name="url" class="form-control" placeholder="Url Menu" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Urutan Menu</label>
                                <input type="number" id="urutan-edit" name="urutan" class="form-control" placeholder="Urutan Menu" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama" class="form-label">Icon Menu</label>
                                <select class="data-icon-edit" id="icon-edit" name="icon" placeholder="Pilih Icon..." required>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM icon");
                                    echo "<option value=''>Pilih Icon...</option>";
                                    while ($a = mysqli_fetch_array($query)) {
                                        echo '<option value="' . $a['kode'] . '" data-src="' . $a['kode'] . '"></i> &nbsp; ' . $a['nama'] . '</option>';
                                    } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih Icon Menu
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="proses" value="editmenu">
                        <input type="hidden" name="uuid" id="uuid" value="">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-primary validasi-form" id="tomboleditmenu">
                            <span id="loadingeditmenu"></span> &nbsp; Edit menu</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- MODAL EDIT DATA MENU -->


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

                $('#modaltambahmenu').on('hidden.bs.modal', function() {
                    $('#modaltambahmenu form')[0].reset();
                    selecticonTambah.clear();
                });

                // tambah data
                $('#body').on('click', '#tomboltambahmenu', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulmenu";
                    run('index.php?method=ajax&menu=menu&aksi=tambah', 'formtambahmenu',
                        '#tomboltambahmenu', '#modaltambahmenu',
                        '#loadingtambahmenu', classname, '.tablestandard', 'sukses tambah data');
                    return false;
                });

                // edit data
                $('#body').on('click', '#tomboleditmenu', function(e) {
                    e.preventDefault();
                    var classname = ".tabelmodulmenu";
                    run('index.php?method=ajax&menu=menu&aksi=edit', 'formeditmenu',
                        '#tomboleditmenu', '#modaleditmenu',
                        '#loadingeditmenu', classname, '.tablestandard', 'sukses edit data');
                    return false;
                });

                var modaleditmenu = document.getElementById('modaleditmenu');
                modaleditmenu.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget
                    var modal_data = JSON.parse(button.getAttribute('data-bs-val'))
                    var modal_title = modaleditmenu.querySelector('.modal-title')
                    var nama = modaleditmenu.querySelector('.modal-body #nama-edit')
                    var icon = modaleditmenu.querySelector('.modal-body #icon-edit')
                    var jenis = modaleditmenu.querySelector('.modal-body #jenis-edit')
                    var modul = modaleditmenu.querySelector('.modal-body #modul-edit')
                    var url = modaleditmenu.querySelector('.modal-body #url-edit')
                    var urutan = modaleditmenu.querySelector('.modal-body #urutan-edit')
                    var uuid = modaleditmenu.querySelector('.modal-footer #uuid')
                    selecticonEdit.setValue(modal_data['icon']);
                    var tombolmodal = modaleditmenu.querySelector(
                        '.modal-footer button[id=tomboleditmenu]')
                    modal_title.textContent = 'Edit Menu : ' + $.htmlentities.decode(modal_data['nama'])
                    nama.value = $.htmlentities.decode(modal_data['nama'])
                    icon.value = $.htmlentities.decode(modal_data['icon'])
                    jenis.value = $.htmlentities.decode(modal_data['jenis'])
                    modul.value = $.htmlentities.decode(modal_data['modul'])
                    url.value = $.htmlentities.decode(modal_data['url'])
                    urutan.value = $.htmlentities.decode(modal_data['urutan'])
                    uuid.value = modal_data['uuid']
                })
            });
        </script>
    <?php
}
    ?>