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
  
    $l1 = array(
            'block1' => 'data1',
            'block2' => 'data2',
            'block3' => 'data3',
            'block4' => 'data4'
    );
    $l2 = array(
            'block1' => 'data1',
            'block2' => 'data2',
            'block3' => 'data3',
            'block4' => 'data4'
    );
    $l3 = array(
            'block1' => 'data1',
            'block2' => 'data2',
            'block3' => 'data3',
            'block4' => 'data4'
    );
    
    $mainmemory = array();
    for($i = 1; $i <= 50; $i++){
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
 
        foreach($testcases as $v){
            $cache->get($v);
        }
        $cache->finalize();
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
    <div class="testcases memblocks">
        <h1>Test Cases</h1>
        <ul>
        <?php
            foreach($testcases as $k=>$v){
                echo "<li>".$v."</li>";
            }
         ?>
         </ul>
    </div>
    <div style="width: 46%; float: left;">
    <div class="level1 memblocks">
        <h1>Cache L1</h1>
        <ul>
        <?php
             foreach($cache->level['l1']['data'] as $k=>$v){
                 echo "<li>".$v."</li>";
             }
         ?>
         </ul>
    </div>
    <div class="level2 memblocks">
        <h1><br> L2</h1>
        <ul>
        <?php
             foreach($cache->level['l2']['data'] as $k=>$v){
                 echo "<li>".$v."</li>";
             }
         ?>
         </ul>
    </div>
    <div class="level3 memblocks">
        <h1><br> L3</h1>
        <ul>
        <?php
             foreach($cache->level['l3']['data'] as $k=>$v){
                 echo "<li>".$v."</li>";
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
<div class="results">
<h1>Results on running test case:</h1>
<div>
    <?php
        foreach($testcases as $v){
            $cache->get($v);
        }
        echo "Total Hits: ".$cache->hit()."<br>";
        echo "Total Miss: ".$cache->miss()."<br>";
        echo "Total Conflict Miss: ".$cache->conflictmiss()."<br>";
        echo "Total Access time: ".$cache->accesstime()."<br>";
    ?>
</div>
</div>
</body>
</html>