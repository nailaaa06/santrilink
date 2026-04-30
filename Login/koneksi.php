<?php
$conn = mysqli_connect("localhost", "root", "", "santrilink");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>