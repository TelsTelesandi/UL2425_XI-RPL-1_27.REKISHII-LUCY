<?php
require_once '../config/functions.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sistem Peminjaman Barang'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-brand">Aplikasi Peminjaman Barang</h2>
            </div>
            <ul class="sidebar-nav">
                <?php if (isAdmin()): ?>
                    <!-- Admin Menu -->
                    <li class="sidebar-nav-item">
                        <a href="../admin/dashboard.php" class="sidebar-nav-link <?php echo $activePage == 'dashboard' ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>   
                    <li class="sidebar-nav-item">
                        <a href="../admin/approval.php" class="sidebar-nav-link <?php echo $activePage == 'approval' ? 'active' : ''; ?>">
                            <i class="fas fa-check-circle"></i>
                            <span>Approval Peminjaman</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="../admin/return.php" class="sidebar-nav-link <?php echo $activePage == 'return' ? 'active' : ''; ?>">
                            <i class="fas fa-check-circle"></i>
                            <span>Approval Pengembalian</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="../admin/items.php" class="sidebar-nav-link <?php echo $activePage == 'items' ? 'active' : ''; ?>">
                            <i class="fas fa-box"></i>
                            <span>Kelola Barang</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="../admin/reports.php" class="sidebar-nav-link <?php echo $activePage == 'reports' ? 'active' : ''; ?>">
                            <i class="fas fa-file-alt"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                <?php else: ?>
                    <!-- User Menu -->
                    <li class="sidebar-nav-item">
                        <a href="../user/dashboard.php" class="sidebar-nav-link <?php echo $activePage == 'dashboard' ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="../user/borrow.php" class="sidebar-nav-link <?php echo $activePage == 'borrow' ? 'active' : ''; ?>">
                            <i class="fas fa-arrow-right"></i>
                            <span>Peminjaman Barang</span>
                        </a>
                    </li>
                    <li class="sidebar-nav-item">
                        <a href="../user/return.php" class="sidebar-nav-link <?php echo $activePage == 'return' ? 'active' : ''; ?>">
                            <i class="fas fa-arrow-right"></i>
                            <span>Pengembalian Barang</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="sidebar-nav-item">
                    <a href="../logout.php" class="sidebar-nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Header -->
            <div class="header">
                <div class="header-left">
                    <button class="sidebar-toggle" type="button" aria-label="Toggle Sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="header-title"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                </div>
                <div class="user-dropdown">
                    <div class="user-dropdown-toggle">
                        <i class="fas fa-user"></i>
                        <span><?php echo $_SESSION['nama_lengkap']; ?></span>
                        <i class="fas fa-caret-down ml-1"></i>
                    </div>
                    <div class="user-dropdown-menu">
                        <a href="../logout.php" class="user-dropdown-item">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="main-content">
