<?php
    session_start();
        if(!isset($_SESSSION))
            $_SESSION['admin']=0;
        if(isset($_POST['g_username'])&&isset($_POST['g_password']))
        {
            $_SESSION['username']=$_POST['g_username'];

            $fptr=fopen("./data","r");
            while($userinfo=fscanf($fptr,"%s\t%s\n"))
            {
                list($name, $password)=$userinfo;
                if(strcmp($_POST['g_username'],$name)==0&&strcmp($_POST['g_password'],$password)==0)
                {

                    $_SESSION['admin']= 1;
                    header('location:./index.php');
                }
            }
            echo "密碼錯誤";
        }

?>
<html>
    <head>
        <title>GAIS Lab NIDB Search Engine System</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>
    <div style="width:275px;height:150px;margin-top:20%;margin-left:40%;background-color: #eeeeee;text-align: center;">
    <p>GAIS Lab 搜尋引擎</p>
    <form action="#" method="POST">
    帳號:<input type="text" name="g_username" value="<?if(isset($_SESSION['username']))echo $_SESSION['username'];?>"><br>
    密碼:<input type="password" name="g_password"><br>
    <input type="submit" value="送出">
    </form>
    </div>
    </body>
</html>

