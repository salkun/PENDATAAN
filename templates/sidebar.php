<?php
$current_page = $_SERVER['REQUEST_URI'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
?>
<aside class="app-sidebar shadow-lg" data-bs-theme="dark" id="mainSidebar">
    <!-- Brand Logo -->
    <div class="sidebar-brand-wrapper">
        <a href="<?= $base_url ?>/<?= $role ?>/dashboard.php" class="sidebar-brand-link">
            <div class="sidebar-logo-icon">
                <img src="<?= $base_url ?>/assets/img/logo-pa.png" alt="Logo PA">
            </div>
            <div class="sidebar-brand-text">
                <span class="brand-title">PENDATAAN</span>
                <span class="brand-subtitle">Pengadilan Agama</span>
            </div>
        </a>
    </div>

    <!-- Sidebar Content -->
    <div class="sidebar-wrapper">
        <!-- User Panel -->
        <div class="sidebar-user-panel">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="user-info">
                <span class="user-name"><?= isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : 'User' ?></span>
                <span class="user-role-badge role-<?= $role ?>"><?= strtoupper($role) ?></span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                
                <?php if ($role === 'admin'): ?>
                    <!-- ADMIN MENU -->
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/dashboard.php" class="sidebar-nav-link <?= strpos($current_page, 'admin/dashboard.php') !== false ? 'active' : '' ?>">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-section-label">Menu Utama</li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/barang/index.php" class="sidebar-nav-link <?= strpos($current_page, 'admin/barang/') !== false ? 'active' : '' ?>">
                            <i class="bi bi-box-seam"></i>
                            <span>Data Barang</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/barang_masuk/index.php" class="sidebar-nav-link <?= strpos($current_page, 'admin/barang_masuk/') !== false ? 'active' : '' ?>">
                            <i class="bi bi-box-arrow-in-down"></i>
                            <span>Barang Masuk</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/barang_keluar/index.php" class="sidebar-nav-link <?= strpos($current_page, 'admin/barang_keluar/') !== false ? 'active' : '' ?>">
                            <i class="bi bi-box-arrow-up"></i>
                            <span>Barang Keluar</span>
                        </a>
                    </li>

                    <li class="sidebar-section-label">Administrasi</li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/user/index.php" class="sidebar-nav-link <?= strpos($current_page, 'admin/user/') !== false ? 'active' : '' ?>">
                            <i class="bi bi-people"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/admin/laporan/index.php" class="sidebar-nav-link <?= strpos($current_page, 'admin/laporan/') !== false ? 'active' : '' ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan</span>
                        </a>
                    </li>

                <?php else: ?>
                    <!-- USER MENU -->
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/user/dashboard.php" class="sidebar-nav-link <?= strpos($current_page, 'user/dashboard.php') !== false ? 'active' : '' ?>">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-section-label">Menu</li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/user/barang.php" class="sidebar-nav-link <?= strpos($current_page, 'user/barang.php') !== false ? 'active' : '' ?>">
                            <i class="bi bi-box-seam"></i>
                            <span>Lihat Barang</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/user/laporan.php" class="sidebar-nav-link <?= strpos($current_page, 'user/laporan.php') !== false ? 'active' : '' ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Laporan Stok</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="sidebar-section-label">Sesi</li>
                
                <li class="nav-item">
                    <a href="<?= $base_url ?>/auth/logout.php" class="sidebar-nav-link logout-link">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<style>
    /* ============================================
       SIDEBAR — Premium Green Theme
       ============================================ */
    
    #mainSidebar.app-sidebar {
        min-height: 100vh;
        background: linear-gradient(180deg, #0d3b13 0%, #145a1e 40%, #1a6b25 100%);
        border-right: 1px solid rgba(255, 255, 255, 0.06);
    }

    /* Brand Logo Area */
    .sidebar-brand-wrapper {
        padding: 18px 18px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(0, 0, 0, 0.15);
    }
    .sidebar-brand-link {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }
    .sidebar-logo-icon {
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        backdrop-filter: blur(4px);
    }
    .sidebar-logo-icon img {
        width: 34px;
        height: 34px;
        object-fit: contain;
    }
    .sidebar-brand-text {
        display: flex;
        flex-direction: column;
    }
    .brand-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: 0.5px;
        line-height: 1.2;
    }
    .brand-subtitle {
        font-size: 0.68rem;
        color: rgba(255, 255, 255, 0.55);
        font-weight: 400;
        letter-spacing: 0.3px;
    }

    /* Sidebar Content Wrapper */
    .sidebar-wrapper {
        padding: 14px 14px 20px;
    }

    /* User Panel */
    .sidebar-user-panel {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        margin-bottom: 16px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.04) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #43a047, #66bb6a);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(67, 160, 71, 0.35);
    }
    .user-avatar i {
        color: #fff;
        font-size: 1.05rem;
    }
    .user-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 0;
    }
    .user-name {
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .user-role-badge {
        font-size: 0.6rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        width: fit-content;
        letter-spacing: 0.8px;
    }
    .user-role-badge.role-admin {
        background: linear-gradient(135deg, #ffd54f, #ffb300);
        color: #4e3800;
    }
    .user-role-badge.role-user {
        background: linear-gradient(135deg, #81c784, #4caf50);
        color: #0d3b13;
    }

    /* Section Label */
    .sidebar-section-label {
        font-size: 0.65rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.35);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 14px 14px 6px;
        list-style: none;
    }

    /* Nav Links */
    .sidebar-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 10px;
        color: rgba(255, 255, 255, 0.65) !important;
        text-decoration: none !important;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
        margin-bottom: 2px;
    }
    .sidebar-nav-link i {
        font-size: 1.15rem;
        width: 22px;
        text-align: center;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .sidebar-nav-link:hover {
        color: #fff !important;
        background: rgba(255, 255, 255, 0.08);
    }
    .sidebar-nav-link:hover i {
        transform: scale(1.1);
    }

    /* Active State */
    .sidebar-nav-link.active {
        color: #fff !important;
        font-weight: 700;
        background: linear-gradient(135deg, rgba(76, 175, 80, 0.35) 0%, rgba(56, 142, 60, 0.25) 100%);
        border: 1px solid rgba(76, 175, 80, 0.25);
        box-shadow: 0 2px 12px rgba(76, 175, 80, 0.15);
    }
    .sidebar-nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3.5px;
        height: 60%;
        border-radius: 0 4px 4px 0;
        background: linear-gradient(180deg, #81c784, #4caf50);
    }

    /* Logout Link */
    .sidebar-nav-link.logout-link {
        color: rgba(244, 67, 54, 0.7) !important;
    }
    .sidebar-nav-link.logout-link:hover {
        color: #ef5350 !important;
        background: rgba(244, 67, 54, 0.1);
    }
</style>
