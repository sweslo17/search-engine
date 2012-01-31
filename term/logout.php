<?php
    session_start();
    if(isset($_SESSION['admin'])&&$_SESSION['admin']==1)
    {
        $tmp = $_SESSION['username'];
        session_destroy();
        session_start();
        $_SESSION['username']= $tmp;
    }
    header("location:./index.php");
?>

