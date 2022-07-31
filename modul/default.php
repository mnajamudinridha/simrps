   <div class="row">
       <div class="col-lg-8 mb-4 order-0">
           <div class="card">
               <div class="d-flex align-items-end row">
                   <div class="col-sm-7">
                       <div class="card-body">
                           <h5 class="card-title text-primary">Selamat Datang <strong><?php echo $_SESSION['nama']; ?>!</strong> 🎉</h5>
                           <p class="mb-4">
                               Selesaikan <span class="fw-bold">Rencana Pembelajaran Semester</span> anda dengan Team Teching Lainnya
                           </p>

                           <a href="index.php?menu=profile" class="btn btn-sm btn-outline-primary">Lihat Profile</a>
                       </div>
                   </div>
                   <div class="col-sm-5 text-center text-sm-left">
                       <div class="card-body pb-0 px-0 px-md-4">
                           <img src="assets/img/illustrations/man-with-laptop-light.png" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png" />
                       </div>
                   </div>
               </div>
           </div>
       </div>
       <div class="col-lg-4 col-md-4 order-1">
           <div class="row">
               <div class="col-lg-6 col-md-12 col-6 mb-4">
                   <div class="card">
                       <div class="card-body">
                           <div class="card-title d-flex align-items-start justify-content-between">
                               <div class="avatar flex-shrink-0">
                                   <i class="bx bx-news text-success bx-md rounded" alt="chart success"></i>
                               </div>
                               <div class="dropdown">
                                   <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="bx bx-dots-vertical-rounded"></i>
                                   </button>
                                   <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                       <a class="dropdown-item" href="index.php?menu=matakuliah">Lihat</a>
                                   </div>
                               </div>
                           </div>
                           <span>Matakuliah</span>
                           <h3 class="card-title text-nowrap mb-1">
                               <?php
                                echo mysqli_num_rows(mysqli_query($con, "SELECT * FROM matakuliah WHERE delete_at IS NULL"));
                                ?>
                           </h3>
                           <small class="text-success fw-semibold"><a href="index.php?menu=matakuliah">Lihat Matakuliah</a></small>
                       </div>
                   </div>
               </div>
               <div class="col-lg-6 col-md-12 col-6 mb-4">
                   <div class="card">
                       <div class="card-body">
                           <div class="card-title d-flex align-items-start justify-content-between">
                               <div class="avatar flex-shrink-0">
                                   <i class="bx bx-user-check text-warning bx-md rounded" alt="chart success"></i>
                               </div>
                               <div class="dropdown">
                                   <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="bx bx-dots-vertical-rounded"></i>
                                   </button>
                                   <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                       <a class="dropdown-item" href="index.php?menu=user">Lihat</a>
                                   </div>
                               </div>
                           </div>
                           <span>Dosen</span>
                           <h3 class="card-title text-nowrap mb-1">
                               <?php
                                echo mysqli_num_rows(mysqli_query($con, "SELECT * FROM user WHERE delete_at IS NULL"));
                                ?>
                           </h3>
                           <small class="text-success fw-semibold"><a href="index.php?menu=user">Lihat Dosen</a></small>
                       </div>
                   </div>
               </div>
           </div>
       </div>
       <!-- Total Revenue -->
       <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
           <div class="card">
               <div class="container-xxl container-p-y">
                   <div class="misc-wrapper">
                       <div class="row">
                               <div class="col-md-6 pt-4">
                                   <img src="assets/img/illustrations/girl-doing-yoga-light.png" alt="girl-doing-yoga-light" width="420" class="img-fluid" data-app-dark-img="illustrations/girl-doing-yoga-dark.png" data-app-light-img="illustrations/girl-doing-yoga-light.png" />
                               </div>
                               <div class="col-md-6 pt-5">
                               <h4 class="text-primary">POLITEKNIK NEGERI TANAH LAUT</h4>
                                <p>Politeknik Negeri Tanah Laut adalah Perguruan Tinggi Negeri di Kabupaten Tanah Laut, Provinsi Kalimantan Selatan. Politeknik ini berdiri pada tanggal 25 September 2009</p>
                                <p>Jl. A. Yani No.Km.06, Pemuda, Kec. Pelaihari, Kabupaten Tanah Laut, Kalimantan Selatan 70815<br>(0512) 2021065<br>mail@politala.ac.id</p>
                               </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
       <!--/ Total Revenue -->

       <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
           <div class="row">
               <div class="col-6 mb-4">
                   <div class="card">
                       <div class="card-body">
                           <div class="card-title d-flex align-items-start justify-content-between">
                               <div class="avatar flex-shrink-0">
                                   <i class="bx bx-group text-danger bx-md rounded" alt="chart success"></i>
                               </div>
                               <div class="dropdown">
                                   <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="bx bx-dots-vertical-rounded"></i>
                                   </button>
                                   <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                       <a class="dropdown-item" href="javascript:void(0);">Lihat</a>
                                   </div>
                               </div>
                           </div>
                           <span class="d-block mb-1">Team Teaching</span>
                           <h3 class="card-title text-nowrap mb-2">0</h3>
                           <small class="text-danger fw-semibold"><a>Lihat Team Teaching</a></small>
                       </div>
                   </div>
               </div>
               <div class="col-6 mb-4">
                   <div class="card">
                       <div class="card-body">
                           <div class="card-title d-flex align-items-start justify-content-between">
                               <div class="avatar flex-shrink-0">
                                   <i class="bx bx-archive text-info bx-md rounded" alt="chart success"></i>
                               </div>
                               <div class="dropdown">
                                   <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="bx bx-dots-vertical-rounded"></i>
                                   </button>
                                   <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                       <a class="dropdown-item" href="javascript:void(0);">Lihat</a>
                                   </div>
                               </div>
                           </div>
                           <span class="fw-semibold d-block mb-1">Data RPS</span>
                           <h3 class="card-title mb-2">0</h3>
                           <small class="text-success fw-semibold"><a>Lihat Data RPS</a></small>
                       </div>
                   </div>
               </div>
               <!-- </div>
    <div class="row"> -->
               <div class="col-12 mb-4">
                   <div class="card">
                       <div class="card-body">
                           <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                               <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                   <div class="card-title">
                                       <h5 class="text-nowrap mb-2"><?php echo $_SESSION['nama']; ?></h5>
                                       <span class="badge bg-label-warning rounded-pill"><?php echo $_SESSION['email']; ?></span>
                                   </div>
                                   <div class="mt-sm-auto">
                                       <?php
                                        $biodata = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM user WHERE uuid = '" . $_SESSION['uuid'] . "';"));
                                        echo cetakHTML($biodata['biodata']);
                                        ?>
                                   </div>
                               </div>
                               <div id="profileReportChart"></div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>