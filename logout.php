<?php
session_start();
session_unset();
session_destroy();
header('Location: /APP_COMMUN/Frontend/index.html');
exit();
?>