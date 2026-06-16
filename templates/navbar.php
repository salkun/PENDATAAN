<nav class="app-header navbar navbar-expand bg-body shadow-sm">
    <div class="container-fluid">
        <!-- Start Navbar Links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list text-dark fs-5"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-inline-block">
                <a href="#" class="nav-link text-dark fw-semibold">Sistem Pendataan Barang</a>
            </li>
        </ul>

        <!-- End Navbar Links -->
        <ul class="navbar-nav ms-auto">
            <!-- User Menu -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <span class="d-none d-md-inline fw-medium text-dark"><?= isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : 'User' ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end border-0 shadow-sm mt-2">
                    <!-- User Header -->
                    <li class="user-header bg-gradient-primary text-white text-center py-4 rounded-top" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 60px; height: 60px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                            <i class="bi bi-person-fill fs-2 text-indigo"></i>
                        </div>
                        <p class="mt-2 mb-0 fw-bold">
                            <?= isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : 'User' ?>
                        </p>
                        <small class="text-white-50">Role: <?= isset($_SESSION['role']) ? ucfirst(htmlspecialchars($_SESSION['role'])) : '' ?></small>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer bg-light p-3 d-flex justify-content-center">
                        <a href="<?= $base_url ?>/auth/logout.php" class="btn btn-danger btn-sm w-100 rounded-3 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar / Logout
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
