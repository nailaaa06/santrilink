<?php
$conn = mysqli_connect("localhost", "root", "", "santrilinkk");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>