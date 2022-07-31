<?php
defined("BASEPATH") or exit("No direct access allowed");
?>
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>
    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Search..." aria-label="Search..." />
            </div>
        </div>
        <!-- /Search -->
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="#" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online" id="profile-picture">
                        <img src="<?php echo $_SESSION['photo']; ?>" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online" id="profile-picture2">
                                        <img src="<?php echo $_SESSION['photo']; ?>" alt class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block" id="profile-nama"><?php echo $_SESSION['nama']; ?></span>
                                    <small class="text-muted" id="profile-email"><?php echo $_SESSION['email'] ?></small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="index.php?menu=profile">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="index.php?menu=setting">
                            <i class="bx bx-cog me-2"></i>
                            <span class="align-middle">Ganti Password</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <?php
                    foreach ($_SESSION['level'] as $key => $value) {

                        if ($value['level'] == $_SESSION['levelaktif']['level']) {
                            echo '<li>
                            <a class="dropdown-item" href="index.php?level=' . $value['level'] . '">
                                <i class="' . $value['level_icon'] . ' me-2"></i>
                                <span class="align-middle text-primary"><strong>' . $value['level_nama'] . '</strong> <i>(active)</i></span>
                            </a>
                        </li>';
                        } else {
                            echo '<li>
                            <a class="dropdown-item" href="index.php?level=' . $value['level'] . '">
                                <i class="' . $value['level_icon'] . ' me-2"></i>
                                <span class="align-middle">' . $value['level_nama'] . '</span>
                            </a>
                        </li>';
                        }
                    }
                    ?>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="library/logout.php">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>