<?php

    session_start();

    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $servidor = mysqli_connect("localhost", "root", "", "torcedores");
    $tabela = mysqli_query($servidor, "SELECT * FROM usuario where login='$login'
    and senha='$senha'");

    if(mysqli_num_rows($tabela) > 0) {
        $_SESSION['logado'] = 1;
        header("Location: dashboard.php");
    } else {
        $_SESSION['logado'] = 0;
        header("Location: index.php");
    }



?>