<?php
$pesan = clear(isset($_GET['pesan']) ? $_GET['pesan'] : '');
if($pesan == "sukses"){
    echo '<div class="alert alert-primary alert-dismissible" role="alert">
                Selamat datang di halaman Utama SIM-RPS!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                </button>
          </div>';
}
if($pesan == "sukseslevel"){
    echo '<div class="alert alert-success alert-dismissible" role="alert">
                Sukses Ganti Level Akses SIM-RPS!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                </button>
          </div>';
}
if($pesan == "gagallevel"){
    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                Mohon Maaf, anda tidak memiliki hak akses level ini
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                </button>
          </div>';
}
