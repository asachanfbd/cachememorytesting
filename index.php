<?php
  require_once('lib/library.php');
  $pass = 'mtech';
  if(isset($_POST['password']) && $_POST['password'] == $pass){
      setcookie('ls', md5($pass));
  }else{
    if(!(isset($_COOKIE['ls']) && $_COOKIE['ls'] == md5($pass))){
          ?>
          <div>
          <form method="post" action="">
            Enter password: <input type="password" name="password" required="required">
            <br><input type="submit" value="Login">
            </form>
          </div>
        <?php
                exit();
      }    
  }
  // code to fill blocks
  
    $mainmemory = array();
    for($i = 1; $i <= 1000; $i++){
        $mainmemory['block'.$i] = 'data'.$i;
    }
    $re = $db->querydb("SELECT * FROM memconfig");
    if($re->num_rows){
        while($ro = $re->fetch_object()){
            $level[$ro->memname]['type'] = json_decode($ro->memtype);
            $level[$ro->memname]['data'] = json_decode($ro->memdata);
            $level[$ro->memname]['length'] = $ro->memlength;
        }
    }
  if(isset($_GET['addblock'])){
      $db->insert('testcases', array('name' => $_GET['addblock']));
  }
 $q = "SELECT * FROM testcases";
 $re = $db->querydb($q);
 if($re->num_rows){
     while($ro = $re->fetch_object()){
          $testcases[] = $ro->name;
     }
 }
 $output = '';
    if(isset($_REQUEST['runtest'])){
        foreach($testcases as $v){
            $cache->get($v);
        }
        $output = '
        <div class="results">
            <h1>Results on running test cases:</h1>
            <div>'.$cache->output().'</div>
        </div>
        ';
        $cache->savetodb();
    }
?>
<!doctype html>
<html>
<head>
    <title>Cache memory Configuration</title>
    <link rel="stylesheet" type="text/css" href="styles/common.css">
    <script src="js/jquery.min.js"></script>
</head>
<body>
<nav>
    <ul>
        <li><a href="configure.php">Configure</a></li>
        <li><a href="index.php">View</a></li>
    </ul>
</nav>
<div class="container">
    <?php
        echo $output;
    ?>
    <div class="testcases memblocks">
        <h1>Test Cases</h1>
        <form action="index.php" method="POST">
            <input type="submit" name="runtest" value="Run Test Case" onclick="return confirm('You are about to run the test case. Continue?');">
        </form>
        <ul>
        <?php
        if(is_array($testcases)){
            foreach($testcases as $k=>$v){
                echo "<li>".$v."</li>";
            }
        }
         ?>
         </ul>
    </div>
    <div style="width: 46%; float: left;">
    <div class="level1 memblocks">
        <h1>Cache L1</h1>
        <ul>
        <?php
            if(is_array($cache->level['l1']['data'])){
                 foreach($cache->level['l1']['data'] as $k=>$v){
                     echo "<li>".$v."</li>";
                 }
            }
         ?>
         </ul>
    </div>
    <div class="level2 memblocks">
        <h1><br>L2</h1>
        <ul>
        <?php
            if(is_array($cache->level['l2']['data'])){
                 foreach($cache->level['l2']['data'] as $k=>$v){
                     echo "<li>".$v."</li>";
                 }
            }
         ?>
         </ul>
    </div>
    <div class="level3 memblocks">
        <h1><br>L3</h1>
        <ul>
        <?php
            if(is_array($cache->level['l3']['data'])){
                 foreach($cache->level['l3']['data'] as $k=>$v){
                     echo "<li>".$v."</li>";
                 }
            }
         ?>
         </ul>
    </div>
    </div>
    <div class="mainmem memblocks">
        <h1>Main Memory</h1>
        <div style="font-size: 11px; color:#999;">Click the blocks to add them to the test cases.</div>
        <ul>
        <?php
             foreach($mainmemory as $k=>$v){
                 echo "<li id='".$k."'>".$k."<div><a href='?addblock=".$k."' class='blocks'>+</a></div></li>";
             }
         ?>
         </ul>
    </div>

</div>
</body>
</html>