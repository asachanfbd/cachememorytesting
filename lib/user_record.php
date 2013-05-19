<?php

  require_once("library.php");
  if(isset($_COOKIE['bid']) && isset($_COOKIE['reqid'])){
      $stats->visitordb($_COOKIE['bid'], $_COOKIE['reqid']);
      echo 'update done';
  }
?>
