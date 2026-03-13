<?php
    session_start();    
    session_unset();   
    session_destroy();

    header("Location: index.php"); // تحويل المستخدم لصفحة البداية
    exit();
  ?>
