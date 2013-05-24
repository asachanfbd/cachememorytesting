<?php
  /**
  * file name:      library.php
  * date:           14-09-2012
  * company:        Coravity Infotech
  * developer:      Sudhanshu Mishra
  * description:    This is php page where all the important classes of the project are included and corresponding object instantiation process takes place.
  */
  $testcases = array();
  date_default_timezone_set('Asia/Calcutta');
  
  /*PHP class inclusions*/  
  require_once('db.php');
  require_once("cache.php");
  
  $host='chachetest.db.11027291.hostedresource.com'; /*host name*/
  $uname='chachetest'; /*user name*/
  $pass='FG56@kl09'; /*password*/
  $dbname=$uname; /*database name*/  
  

  $db=new db($dbname, $host, $uname, $pass);/*initialization in case of server is not on localhost*/
  
  /*Object Instantiation for classes*/
  $cache = new cache();
?>