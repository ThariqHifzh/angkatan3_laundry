<?php
include 'koneksi.php';
session_start();

if (isset($_POST['simpan'])) {
    $id_customer   = $_POST['id_customer'];
    $order_code   = $_POST['order_code'];
    $order_date   = $_POST['order_date'];
    $status = $_POST['order_status'];

    // $_POST: form input name=''
    // $_GET: url ?param='nilai'
    // $_FILES: ngambil nilai dari input type file
    if (!empty($_FILES['foto']['name'])) {
        $nama_foto = $_FILES['foto']['name'];
        $ukuran_foto = $_FILES['foto']['size'];

        // png, jpg, jpeg
        $ext = array('png', 'jpg', 'jpeg');
        $extFoto = pathinfo($nama_foto, PATHINFO_EXTENSION);

        // JIKA EXTESI FOTO TIDAK ADA YANG TERDAFTAR DI ARRAY EXTENSI
        if (!in_array($extFoto, $ext)) {
            echo "Ext foto tidak ditemukan";
            die;
        } else {
            // Pindahkan gambar dari tmp folder ke folder yang telah kita buat
            move_uploaded_file($_FILES['foto']['tmp_name'], 'upload/' . $nama_foto);

            $insert = mysqli_query($koneksi, "INSERT INTO trans_order (id_customer, order_code, order_date, order_status, foto) VALUES
            ( '$id_customer', '$order_code', '$order_date', '$status', '$nama_foto')");
        }
    } else {
        $insert = mysqli_query($koneksi, "INSERT INTO trans_order (id_customer, order_code, order_date, order_status) VALUES
            ('$id_customer', '$order_code', '$order_date', '$status')");
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

$queryKodeOrder = mysqli_query($koneksi, "SELECT MAX(id) AS id_order FROM trans_order");
$rowOrder = mysqli_fetch_assoc($queryKodeOrder);
$id_order = $rowOrder['id_order'];
$id_order++;

$kode_order = "TR/" . date('dmy') . "/" . sprintf("%03s", $id_order);

$queryCustomer = mysqli_query($koneksi, "SELECT * FROM customer");
$queryPaket = mysqli_query($koneksi, "SELECT * FROM service");
while($data = mysqli_fetch_assoc($queryPaket)) {
    $rowPaket[] = $data;
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

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <legend class="float-none w-auto px-3 fw-bold">
                                            <?php echo isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Transaksi</legend>
                                        <form action="" method="post" enctype="multipart/form-data">
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
                                                    <label for="" class="form-label">Kode Order</label>
                                                    <input type="text" class="form-control" name="order_code"
                                                        value="<?php echo isset($_GET['detail']) ? $rowOrder['order_code'] : $kode_order ?>"
                                                        readonly>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="" class="form-label">Tanggal Order</label>
                                                    <input type="date" class="form-control" name="order_date" required value="<?php echo isset($_GET['edit']) ? $rowEdit['order_date'] : '' ?>">
                                                </div>

                                            </div>
                                    </div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary ms-4" name="<?php echo isset($_GET['edit']) ? 'edit' : 'simpan' ?>" type="submit">Simpan</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <legend class="float-none w-auto px-3 fw-bold">Detail Transaksi</legend>
                                        <form action="" method="post" enctype="multipart/form-data">
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
                                                <input type="text" class="form-control" name="order_code">
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
                                                <input type="text" class="form-control" name="order_code">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <button class="btn btn-primary ms-4" name="<?php echo isset($_GET['edit']) ? 'edit' : 'simpan' ?>" type="submit">Simpan</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <div
                        class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                        <div class="mb-2 mb-md-0">
                            ©
                            <script>
                                document.write(new Date().getFullYear());
                            </script>
                            , made with ❤️ by
                            <a href="https://themeselection.com" target="_blank"
                                class="footer-link fw-bolder">ThemeSelection</a>
                        </div>
                        <div>
                            <a href="https://themeselection.com/license/" class="footer-link me-4"
                                target="_blank">License</a>
                            <a href="https://themeselection.com/" target="_blank" class="footer-link me-4">More
                                Themes</a>

                            <a href="https://themeselection.com/demo/sneat-bootstrap-html-admin-template/documentation/"
                                target="_blank" class="footer-link me-4">Documentation</a>

                            <a href="https://github.com/themeselection/sneat-html-admin-template-free/issues"
                                target="_blank" class="footer-link me-4">Support</a>
                        </div>
                    </div>
                </footer>
                <!-- / Footer -->

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