<?php
include 'koneksi.php';
session_start();

$queryCustomer = mysqli_query($koneksi, "SELECT * FROM customer");
$id = isset($_GET['detail']) ? $_GET['detail'] : '';
$queryTransDetail = mysqli_query($koneksi, "SELECT customer.customer_name, customer.phone, customer.address, trans_order.order_code, trans_order.order_date, trans_order.order_status, service.service_name, service.price, trans_order_detail.* FROM trans_order_detail 
LEFT JOIN service ON service.id = trans_order_detail.id_service 
LEFT JOIN trans_order ON trans_order.id = trans_order_detail.id_order 
LEFT JOIN customer ON trans_order.id_customer = customer.id 
WHERE trans_order_detail.id_order = '$id'");
$row = [];
while ($dataTrans = mysqli_fetch_assoc($queryTransDetail)) {
    $row[] = $dataTrans;
}

$queryOrder = mysqli_query($koneksi, "SELECT * FROM trans_order");
$rowOrder = [];
while ($dataOrder = mysqli_fetch_assoc($queryOrder)) {
    $rowOrder[] = $dataOrder;
}

$queryPaket = mysqli_query($koneksi, "SELECT * FROM service");
$rowPaket = [];
while ($data = mysqli_fetch_assoc($queryPaket)) {
    $rowPaket[] = $data;
}

if (isset($_POST['simpan'])) {
    $id_customer   = $_POST['id_customer'];
    $order_code   = $_POST['order_code'];
    $order_date   = $_POST['order_date'];

    // mengambil nilai lebih dari satu, looping dengan foreach
    $id_service   = $_POST['id_service'];

    $insertTransOrder = mysqli_query($koneksi, "INSERT INTO trans_order (id_customer, order_code, order_date) VALUES ('$id_customer', '$order_code', '$order_date')");
    $last_id = mysqli_insert_id($koneksi);

    foreach ($id_service as $key => $value) {
            $id_service = array_filter($_POST['id_service']);
            $qty = array_filter($_POST['qty']);
            $id_service = $_POST['id_service'][$key];
            $qty = $_POST['qty'][$key];
    
            // query untuk mengambil harga dari table paket
            $queryService = mysqli_query($koneksi, "SELECT id, price FROM service WHERE id='$id_service'");
            $rowService = mysqli_fetch_assoc($queryService);
            $price = isset($rowService['price']) ? $rowService['price'] : '';
            // sub total
            $subTotal = (int)$qty * (int)$price;
    
            if ($id_service > 0) {
                $insertTransDetail = mysqli_query($koneksi, "INSERT INTO trans_order_detail (id_order, id_service, qty, subtotal) VALUES ('$last_id', '$id_service', '$qty', '$subTotal')");
            }
    }
    header("location:order.php?tambah=berhasil");
}

$id = isset($_GET['edit']) ? $_GET['edit'] : '';
$editUser = mysqli_query(
    $koneksi,
    "SELECT * FROM trans_order WHERE id = '$id'"
);
$rowEdit = mysqli_fetch_assoc($editUser);

if (isset($_POST['edit'])) {
    $id_customer   = $_POST['id_customer'];
    $order_code   = $_POST['order_code'];
    $order_date   = $_POST['order_date'];
    $status = $_POST['order_status'];

    // ubah user kolom apa yang mau di ubah (SET), yang mau di ubah id ke berapa
    $update = mysqli_query($koneksi, "UPDATE trans_order SET id_customer='$id_customer', order_code='$order_code', order_date='$order_date', order_status='$status' WHERE id='$id'");
    header("location:order.php?ubah=berhasil");
}

// jika parameternya ada ?delete=nilai parameter
if (isset($_GET['delete'])) {
    $id = $_GET['delete']; // mengambil nilai parameter

    // query / perintah hapus
    $delete = mysqli_query($koneksi, "DELETE FROM trans_order WHERE id ='$id'");
    header("location:order.php?hapus=berhasil");
}


// No Invoice
// 001, jika ada auto increment id = 1 = 002, selain itu 001
// MAX : terbesar MIN : terkecil
$queryInvoice = mysqli_query($koneksi, "SELECT MAX(id) AS no_invoice FROM trans_order");
// Jika didalam table trans order ada datanya
$str_unique = "TR";
$date_now = date("dmy");
if (mysqli_num_rows($queryInvoice) > 0) {
    $rowInvoice = mysqli_fetch_assoc($queryInvoice);
    $incrementPlus = $rowInvoice['no_invoice'] + 1;
    $code = $str_unique . "/" . $date_now . "/" . "000" . $incrementPlus;
} else {
    $code = $str_unique . "/" . $date_now . "/" . "000" . "0001";
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
                <div class="content-wrapper">
                    <!-- Content -->
                     <?php if(isset($_GET['detail'])): ?>
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">  
                            <div class="col-sm-12 mb-3"></div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Data Transaksi</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered table-striped">
                                                <tr>
                                                    <th>No Invoice</th>
                                                    <td><?php echo $row[0]['order_code'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Tanggal Laundry</th>
                                                    <td><?php echo $row[0]['order_date'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Status</th>
                                                    <td><?php 
                                                            switch ($row[0]['order_status']) {
                                                                case '1':
                                                                    $badge = "<span class='badge bg-success '>Sudah Dikembalikan</span>";
                                                                    break; 
                                                                    
                                                                default:
                                                                    $badge = "<span class='badge bg-warning '>Baru</span>";
                                                                    break;
                                                            }

                                                            echo $badge;
                                                            ?></td>
                                                </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            <div class="card">
                                    <div class="card-header">
                                        <h5>Data Customer</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered table-striped">
                                        
                                                <tr>
                                                    <th>Nama</th>
                                                    <td><?php echo $row[0]['customer_name'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Telpon</th>
                                                    <td><?php echo $row[0]['phone'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Alamat</th>
                                                    <td><?php echo $row[0]['address'] ?></td>
                                                </tr>
                                           
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mt-4">
                            <div class="card">
                                    <div class="card-header">
                                        <h5>Detail Transaksi</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered table-striped">
                                            <thead> 
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Paket</th>
                                                    <th>Qty</th>
                                                    <th>Harga</th>
                                                    <th>Sub Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php $no = 1; foreach ($row as $key => $value): ?>
                                                <tr>
                                                    <td><?php echo $no++ ?></td>
                                                    <td><?php echo $value['service_name'] ?></td>
                                                    <td><?php echo $value['qty'] ?></td>
                                                    <td><?php echo $value['price'] ?></td>
                                                    <td><?php echo $value['subtotal'] ?></td>
                                                </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                        
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <legend class="float-none w-auto px-3 fw-bold">
                                                <?php echo isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Transaksi</legend>

                                            <div class="col-sm-12">
                                                <label for="" class="form-label">Kategori</label>
                                                <select name="id_customer" id="" class="form-control">
                                                    <option value="">-- Pilih Customer --</option>

                                                    <!-- option yang datanya di ambil dari table kategori -->
                                                    <?php while ($rowCustomer = mysqli_fetch_assoc($queryCustomer)): ?>
                                                        <option value="<?php echo $rowCustomer['id'] ?>">
                                                            <?php echo $rowCustomer['customer_name'] ?></option>
                                                    <?php endwhile ?>
                                                </select>
                                            </div>
                                            <div class="mb-3 mt-3 row">
                                                <div class="col-sm-6">
                                                    <label for="" class="form-label">No Invoice</label>
                                                    <input type="text" class="form-control" name="order_code"
                                                        value="<?php echo $code ?>"
                                                        readonly>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="" class="form-label">Tanggal Order</label>
                                                    <input type="date" class="form-control" name="order_date" required value="<?php echo isset($_GET['edit']) ? $rowEdit['order_date'] : '' ?>">
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <legend class="float-none w-auto px-3 fw-bold">Detail Transaksi</legend>

                                            <div class="mb-3 row">
                                                <div class="col-sm-3">
                                                    <label for="" class="form-label">Paket</label>
                                                </div>
                                                <div class="col-sm-7">
                                                    <select name="id_service[]" id="" class="form-control">
                                                        <option value="">-- Pilih Paket --</option>
                                                        <?php foreach ($rowPaket as $key => $value) { ?>

                                                            <option value="<?php echo $value['id'] ?>"><?php echo $value['service_name'] ?></option>

                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3 mt-3 row">
                                                <div class="col-sm-3">
                                                    <label for="" class="form-label">Qty</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" placeholder="Qty" name="qty[]">
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <div class="col-sm-3">
                                                    <label for="" class="form-label">Paket</label>
                                                </div>
                                                <div class="col-sm-7">
                                                    <select name="id_service[]" id="" class="form-control">
                                                        <option value="">-- Pilih Paket --</option>
                                                        <?php foreach ($rowPaket as $key => $value) { ?>

                                                            <option value="<?php echo $value['id'] ?>"><?php echo $value['service_name'] ?></option>

                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3 mt-3 row">
                                                <div class="col-sm-3">
                                                    <label for="" class="form-label">Qty</label>
                                                </div>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" placeholder="Qty" name="qty[]">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <button class="btn btn-primary ms-4" name="<?php echo isset($_GET['edit']) ? 'edit' : 'simpan' ?>" type="submit">Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endif ?>
                </div>
                <!-- / Content -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <?php include "inc/footer.php"; ?>
</body>

</html>