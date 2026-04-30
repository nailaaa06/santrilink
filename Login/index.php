<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <!-- POSTER -->
    <div class="left">
        <img src="poster.jpeg" alt="Poster">
    </div>

    <!-- LOGIN -->
    <div class="right">

        <form class="login-box" action="login.php" method="POST">

    <img src="logo.png" class="logo">


            <h2>Masuk dan Verifikasi</h2>

            <p class="subtitle">
                Nikmati kemudahan sistem autentikasi untuk mengakses semua layanan
            </p>

            <div class="input-group">
                <label>NIS</label>

                <input 
                    type="text" 
                    name="nis" 
                    placeholder="Masukkan NIS"
                    required
                >
            </div>

            <div class="input-group">

                <label>Password</label>

                <div class="password-box">

                    <input 
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Masukkan password"
                        required
                    >

                    <span 
                        class="toggle-password"
                        onclick="togglePassword()"
                    >
                        👁️
                    </span>

                </div>

            </div>

            <button type="submit" class="login-btn">
                Login
            </button>

        </form>

    </div>

</div>

<script>

function togglePassword(){

    const password = document.getElementById("password");

    if(password.type === "password"){
        password.type = "text";
    }else{
        password.type = "password";
        
        <?php if (isset($_GET['error'])): ?>
    <?php if ($_GET['error'] == 'password'): ?>
        <p style="color:red;">Password salah!</p>
    <?php elseif ($_GET['error'] == 'akun'): ?>
        <p style="color:red;">Akun tidak terdaftar!</p>
    <?php endif; ?>
<?php endif; ?>
    }

}


</script>

</body>
</html>