<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: index.php?error=unauthorized");
        exit();
    }
}

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function getUserById($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $query = "SELECT * FROM users WHERE USER_ID = '$id'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function getItemById($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $query = "SELECT * FROM sarana WHERE SARANA_ID = '$id'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function formatDate($date) {
    return date("d-m-Y", strtotime($date));
}

function getStatusLabel($status) {
    switch ($status) {
        case 'menunggu':
            return '<span class="badge pending">Menunggu Persetujuan</span>';
        case 'disetujui':
            return '<span class="badge approved">Disetujui</span>';
        case 'ditolak':
            return '<span class="badge rejected">Ditolak</span>';
        case 'dipinjam':
            return '<span class="badge returned">Sedang Dipinjam</span>';
        case 'dikembalikan_konfirmasi':
            return '<span class="badge pending">Menunggu Persetujuan</span>';
        case 'selesai':
            return '<span class="badge completed">Selesai</span>';
        default:
            return '<span class="badge">' . $status . '</span>';
    }
}
?>
