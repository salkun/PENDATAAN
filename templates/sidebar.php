<?php
$current_page = $_SERVER['REQUEST_URI'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
?>
<aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">
    <!-- Brand Logo -->
    <div class="sidebar-brand d-flex align-items-center justify-content-between p-3 border-bottom border-secondary">
        <a href="<?= $base_url ?>/<?= $role ?>/dashboard.php" class="brand-link text-decoration-none d-flex align-items-center gap-2">
            <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-box-seam fs-4"></i>
            </div>
            <div>
                <span class="brand-text fw-bold text-white fs-5 d-block" style="letter-spacing: -0.5px;">PENDATAAN</span>
                <span class="text-white-50 small d-block" style="font-size: 0.7rem; margin-top: -3px;">Inventaris v1.0</span>
            </div>
        </a>
    </div>

    <!-- Sidebar Content -->
    <div class="sidebar-wrapper p-3">
        <div class="user-panel mb-4 d-flex align-items-center gap-2 p-2 rounded bg-secondary-subtle bg-opacity-10 border border-secondary border-opacity-20">
            <div class="bg-indigo text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #6366f1;">
                <i class="bi bi-person-badge-fill fs-5"></i>
            </div>
            <div class="info">
                <span class="d-block text-white fw-semibold small"><?= isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : 'User' ?></span>
                <span class="badge bg-success text-dark fw-bold" style="font-size: 0.65rem;"><?= strtoupper($role) ?></span>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav flex-column gap-1" data-lte-toggle="treeview" role="menu" data-accordion="false">
                
                <?php if ($role === 'admin'): ?>
                    <!-- ADMIN MENU -->
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/dashboard.php" class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded text-white text-opacity-75 <?= strpos($current_page, 'admin/dashboard.php') !== false ? 'active bg-primary text-opacity-100 fw-bold' : 'hover-bg' ?>">
                            <i class="bi bi-speedometer2 fs-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-header text-uppercase text-white-50 fw-semibold mt-3 mb-2" style="font-size: 0.75rem;">Menu Utama</li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/barang/index.php" class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded text-white text-opacity-75 <?= strpos($current_page, 'admin/barang/') !== false ? 'active bg-primary text-opacity-100 fw-bold' : 'hover-bg' ?>">
                            <i class="bi bi-box-seam fs-5"></i>
                            <span>Data Barang</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/barang_masuk/index.php" class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded text-white text-opacity-75 <?= strpos($current_page, 'admin/barang_masuk/') !== false ? 'active bg-primary text-opacity-100 fw-bold' : 'hover-bg' ?>">
                            <i class="bi bi-box-arrow-in-down fs-5"></i>
                            <span>Barang Masuk</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/barang_keluar/index.php" class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded text-white text-opacity-75 <?= strpos($current_page, 'admin/barang_keluar/') !== false ? 'active bg-primary text-opacity-100 fw-bold' : 'hover-bg' ?>">
                            <i class="bi bi-box-arrow-up fs-5"></i>
                            <span>Barang Keluar</span>
                        </a>
                    </li>

                    <li class="nav-header text-uppercase text-white-50 fw-semibold mt-3 mb-2" style="font-size: 0.75rem;">Administrasi</li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/user/index.php" class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded text-white text-opacity-75 <?= strpos($current_page, 'admin/user/') !== false ? 'active bg-primary text-opacity-100 fw-bold' : 'hover-bg' ?>">
                            <i class="bi bi-people fs-5"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/laporan/index.php" class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded text-white text-opacity-75 <?= strpos($current_page, 'admin/laporan/') !== false ? 'active bg-primary text-opacity-100 fw-bold' : 'hover-bg' ?>">
                            <i class="bi bi-file-earmark-bar-graph fs-5"></i>
                            <span>Laporan</span>
                        </a>
                    </li>

                <?php else: ?>
                    <!-- USER MENU -->
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/user/dashboard.php" class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded text-white text-opacity-75 <?= strpos($current_page, 'user/dashboard.php') !== false ? 'active bg-primary text-opacity-100 fw-bold' : 'hover-bg' ?>">
                            <i class="bi bi-speedometer2 fs-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-header text-uppercase text-white-50 fw-semibold mt-3 mb-2" style="font-size: 0.75rem;">Menu</li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/user/barang.php" class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded text-white text-opacity-75 <?= strpos($current_page, 'user/barang.php') !== false ? 'active bg-primary text-opacity-100 fw-bold' : 'hover-bg' ?>">
                            <i class="bi bi-box-seam fs-5"></i>
                            <span>Lihat Barang</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/user/laporan.php" class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded text-white text-opacity-75 <?= strpos($current_page, 'user/laporan.php') !== false ? 'active bg-primary text-opacity-100 fw-bold' : 'hover-bg' ?>">
                            <i class="bi bi-file-earmark-bar-graph fs-5"></i>
                            <span>Laporan Stok</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-header text-uppercase text-white-50 fw-semibold mt-3 mb-2" style="font-size: 0.75rem;">Sesi</li>
                
                <li class="nav-item">
                    <a href="<?= $base_url ?>/auth/logout.php" class="nav-link d-flex align-items-center gap-3 py-2 px-3 rounded text-danger text-opacity-75 hover-bg">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<style>
    .hover-bg:hover {
        background-color: rgba(255, 255, 255, 0.08);
        text-decoration: none;
        color: #ffffff !important;
    }
    .app-sidebar {
        min-height: 100vh;
    }
</style>
