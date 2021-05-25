<?php

session_start();
session_unset();
session_destroy();

setcookie("remember", true, time()-3600, "/");
setcookie("id", 0, time()-3600, "/");
setcookie("username", 0, time()-3600, "/");
setcookie("firstname", 0, time()-3600, "/");
setcookie("lastname", 0, time()-3600, "/");

header("Location: ../index.php?logout=success");
