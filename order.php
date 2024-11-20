<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['nama'])) {

    header("location:login.php?error=access-failed");
}

// munculkan / pilih sebuah atau semua kolom dari table user
$queryTrans = mysqli_query($koneksi, "SELECT customer.customer_name, trans_order.* FROM trans_order LEFT JOIN customer ON customer.id = trans_order.id_customer ORDER BY id DESC");
// mysqli_fetch_assoc($query) = unhtuk menjadikan hasil query menjadi sebuah data (object, array)

// jika parameternya ada ?delete=nilai parameter
if (isset($_GET['delete'])) {
    $id = $_GET['delete']; // mengambil nilai parameter

    // query / perintah hapus
    $delete = mysqli_query($koneksi, "DELETE FROM trans_order WHERE id ='$id'");
    header("location:order.php?hapus=berhasil");
}
?>


<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dashboard - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

    <meta name="description" content="" />

    <?php include 'inc/head.php'; ?>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <?php include 'inc/sidebar.php'; ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <?php include 'inc/navbar.php'; ?>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                            <div class="card-header fw-bold" style="font-size: x-large;">Transaksi Laundry</div>
                                <div class="card-body">
                                    <div class="table table-responsive">
                                        <?php if ($_SESSION['id_level'] == 1) : ?>
                                            <div class="mt-2 mb-3" align="right">
                                                <a href="tambah-order.php" class="btn btn-primary">Tambah</a>
                                            </div>
                                        <?php endif ?>
                                        <?php if(isset($_GET['hapus'])): ?>
                                            <div class="alert alert-primary" role="alert">Data berhasil dihapus</div>
                                        <?php endif ?>
                                        <table class=" table table-bordered bg-transparent">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode Transaksi</th>
                                                    <th>Nama Costumer</th>
                                                    <th>Tanggal Laundry</th>
                                                    <th>Status</th>
                                                    <?php if ($_SESSION['id_level'] == 1) : ?>
                                                        <th>Settings</th>
                                                    <?php endif ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $no = 1;
                                                while ($rowTrans = mysqli_fetch_assoc($queryTrans)): ?>
                                                    <tr>
                                                        <td><?php echo $no++ ?></td>
                                                        <td><?php echo $rowTrans['order_code'] ?></td>
                                                        <td><?php echo $rowTrans['customer_name'] ?></td>
                                                        <td><?php echo $rowTrans['order_date'] ?></td>
                                                        <td>
                                                            <?php 
                                                            switch ($rowTrans['order_status']) {
                                                                case '1':
                                                                    $badge = "<span class='badge bg-success '>Sudah Dikembalikan</span>";
                                                                    break; 
                                                                    
                                                                default:
                                                                    $badge = "<span class='badge bg-warning '>Baru</span>";
                                                                    break;
                                                            }

                                                            echo $badge;
                                                            ?>
                                                        </td>
                                                        <?php if ($_SESSION['id_level'] == 1) : ?>
                                                            <td>
                                                            <a class="btn btn-primary btn-sm"
                                                                href="tambah-order.php?detail=<?php echo $rowTrans['id'] ?>"><span class="tf-icon bx bx-show">
                                                            </a>
                                                            <a target="_blank" class="btn btn-warning btn-sm"
                                                                    href="print.php?id=<?php echo $rowTrans['id'] ?>"><span class="tf-icon bx bx-printer"></a>       
                                                            <a class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Apakah Anda Yakin untuk Menghapus Data Ini?')"
                                                                href="order.php?delete=<?php echo $rowTrans['id'] ?>"><span class="tf-icon bx bx-trash"></a>
                                                            </td>
                                                        <?php endif ?>
                                                    </tr>
                                                <?php endwhile ?>
    
                                            </tbody>
                                        </table>
                                        <?php if ($_SESSION['id_level'] == 2) : ?>
                                            <td>
                                                <a href="print-semua.php" type="submit" class="btn-sm btn-primary text-white px-4" name="simpan" value="Hitung"
                                                    style="border-radius: 20px; margin-top: 50px; margin-left: 600px;">Print</a>
                                            </td>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

   <?php include 'inc/footer.php' ?>
</body>

</html>