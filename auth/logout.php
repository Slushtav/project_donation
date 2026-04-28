<?php
session_start();
session_destroy();
header("Location: /donasi/auth/login.php?msg=Kamu+berhasil+logout");
exit;
