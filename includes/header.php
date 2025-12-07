<?php
session_start(); // WAJIB ADA DI PALING ATAS

// 1. CEK LOGIN (Middleware Sederhana)
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, redirect ke halaman login
    // Kita harus mendeteksi path relatif
    $in_subfolder = strpos($_SERVER['REQUEST_URI'], '/auth/') === false;
    $login_path = $in_subfolder ? '/ERP/auth/login.php' : 'login.php';
    
    // Perbaikan path jika menggunakan Laragon Virtual Host atau Localhost
    $host = $_SERVER['HTTP_HOST'];
    if ($host == 'localhost') {
        header("Location: /ERP/auth/login.php");
    } else {
        header("Location: /auth/login.php");
    }
    exit;
}

// 2. CONFIG PATH
$host = $_SERVER['HTTP_HOST'];
$base_url = ($host == 'localhost') ? '/ERP' : '';

$uri = $_SERVER['REQUEST_URI'];
$is_sales = strpos($uri, '/sales/') !== false;
$is_mfg   = strpos($uri, '/manufacturing/') !== false;
$is_pur   = strpos($uri, '/purchasing/') !== false;
$is_acc   = strpos($uri, '/accounting/') !== false;
$is_hr    = strpos($uri, '/hr/') !== false;

// Cek Role
$role = $_SESSION['role'] ?? 'staff';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System - <?= $_SESSION['full_name'] ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css"> 
</head>
<body class="bg-light">

<div class="d-flex">
    
    <<nav class="sidebar">
    <div class="sidebar-header">
        <a href="<?= $base_url ?>/index.php" class="sidebar-brand">
            <i class="bi bi-box-seam me-2"></i>NOITIO ERP
        </a>
        <div class="user-info">Hi, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Guest') ?></div>
        <div class="user-role"><?= htmlspecialchars($_SESSION['role'] ?? 'Guest') ?></div>
    </div>
    
    <a href="<?= $base_url ?>/index.php" class="sidebar-link <?= ($uri == $base_url.'/index.php' || $uri == $base_url.'/') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
    </a>

    <div class="sidebar-heading">OPERATIONS</div>

    <a href="#menuSales" data-bs-toggle="collapse" class="sidebar-link <?= $is_sales ? 'active' : '' ?>" aria-expanded="<?= $is_sales ? 'true' : 'false' ?>">
        <i class="bi bi-cart3"></i> <span>Sales</span>
        <i class="bi bi-chevron-down ms-auto dropdown-toggle-icon"></i>
    </a>
    <div class="collapse <?= $is_sales ? 'show' : '' ?>" id="menuSales">
        <div class="sidebar-submenu">
            <a href="<?= $base_url ?>/sales/orders.php" class="sidebar-link">Quotations / Orders</a>
            <a href="<?= $base_url ?>/sales/customer_list.php" class="sidebar-link">Customers</a>
        </div>
    </div>

    <a href="#menuPur" data-bs-toggle="collapse" class="sidebar-link <?= $is_pur ? 'active' : '' ?>" aria-expanded="<?= $is_pur ? 'true' : 'false' ?>">
        <i class="bi bi-bag"></i> <span>Purchasing</span>
        <i class="bi bi-chevron-down ms-auto dropdown-toggle-icon"></i>
    </a>
    <div class="collapse <?= $is_pur ? 'show' : '' ?>" id="menuPur">
        <div class="sidebar-submenu">
            <a href="<?= $base_url ?>/purchasing/rfq_list.php" class="sidebar-link">Requests for Quotation</a>
            <a href="<?= $base_url ?>/purchasing/vendors.php" class="sidebar-link">Vendors</a>
        </div>
    </div>

    <a href="#menuMfg" data-bs-toggle="collapse" class="sidebar-link <?= $is_mfg ? 'active' : '' ?>" aria-expanded="<?= $is_mfg ? 'true' : 'false' ?>">
        <i class="bi bi-tools"></i> <span>Manufacturing</span>
        <i class="bi bi-chevron-down ms-auto dropdown-toggle-icon"></i>
    </a>
    <div class="collapse <?= $is_mfg ? 'show' : '' ?>" id="menuMfg">
        <div class="sidebar-submenu">
            <a href="<?= $base_url ?>/manufacturing/mo_list.php" class="sidebar-link">Manufacturing Orders</a>
            <a href="<?= $base_url ?>/manufacturing/products.php" class="sidebar-link">Products</a>
            <a href="<?= $base_url ?>/manufacturing/bom.php" class="sidebar-link">Bills of Materials</a>
        </div>
    </div>

    <a href="#menuAcc" data-bs-toggle="collapse" class="sidebar-link <?= $is_acc ? 'active' : '' ?>" aria-expanded="<?= $is_acc ? 'true' : 'false' ?>">
        <i class="bi bi-wallet2"></i> <span>Accounting</span>
        <i class="bi bi-chevron-down ms-auto dropdown-toggle-icon"></i>
    </a>
    <div class="collapse <?= $is_acc ? 'show' : '' ?>" id="menuAcc">
        <div class="sidebar-submenu">
            <a href="<?= $base_url ?>/manufacturing/bills.php" class="sidebar-link">Vendor Bills</a>
        </div>
    </div>

    <div class="logout-area">
        <a href="<?= $base_url ?>/auth/logout.php" class="btn btn-logout">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
    </div>
</nav>
    
    <div class="main-content w-100">