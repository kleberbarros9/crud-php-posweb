<?php
    session_start();
    $_SESSION['logado'] = 0;
    header("Location: index.php");
?>