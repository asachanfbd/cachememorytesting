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
      if(isset($_GET['deletetestcases'])){
          $db->querydb('TRUNCATE TABLE testcases');
      }
  }
  
  $ifinc = false;
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
<a href="?deletetestcases">Delete all test cases</a>
<div style="overflow: auto;">
    <div class="col3">
        <h1>L1</h1>
        <?php
            if(isset($_REQUEST['deletelonedata'])){
                $db->update('memconfig', array('memdata'=>''), 'memname = "l1"');
            }
            if(isset($_REQUEST['lonesubmit'])){
                if($_REQUEST['memtype'] == 'inc'){
                    foreach ($_REQUEST['memincwith'] as $so){
                        $incd[] = $so;
                    }
                }
                $r = $cache->configcache('l1', $_REQUEST['memtype'], $_REQUEST['memlength'], $incd, $_REQUEST['memaccesstime']);
                if($r){
                    echo '<div style="position:fixed; left: 0; right: 0; bottom: 0; background: yellow; height: 30px; font-weight: bold; text-align:center;">Changes Saved in '.$r.'</div>';
                }
            }
            $q = "SELECT * FROM memconfig WHERE memname='l1'";
            $re = $db->querydb($q, true);
            $memaccesstime = $re->memaccesstime;
            $memincwith = $re->memincwith;
            $memlength = $re->memlength;
            $memtype = $re->memtype;
            if($re->memdata == ''){
                echo 'Cache L1 is Empty!';
            }else{
                ?>
                <form action="" method="POST">
                    <input type="submit" name="deletelonedata" value="Empty Level 1 Cache" onclick="return confirm('Are you sure you want to empty Level 1 ?');">
                </form>
                <?php
            }
        ?>
        <form action="configure.php" method="POST">
        <table>
            <tr>
                <td>Access Time</td><td>:</td><td><input type="text" name="memaccesstime" value="<?php if(isset($memaccesstime)){echo $memaccesstime;}?>"></td>
            </tr>
            <tr>
                    <td>Size/Capacity</td><td>:</td><td><input type="text" name="memlength" value="<?php if(isset($memlength) && $memlength != ''){echo $memlength;}else{ echo '8';}?>"></td>
            </tr>
            <tr style="display: none;">
                <td>Level Type</td>
                <td>:</td>
                <td>
                    <select name="memtype" onchange="togglebox(this, 'loneincwith')">
                    <?php 
                    
                        $arr = array('exc'=>'Exclusive', 'inc' => 'Inclusive');
                        foreach($arr as $k=>$v){
                            echo '<option value="'.$k.'"';
                            if(isset($memtype) && $memtype == $k){
                                echo ' selected="selected"';
                                if($memtype == 'inc'){
                                    $ifinc =true;
                                }
                            }
                            echo '>'.$v.'</option>';
                        }
                    ?>
                    </select>
                </td>
            </tr>
            <tr style="display: none;" class="loneincwith" <?php if($ifinc){ echo 'style="display:block;"'; $ifinc = false;} ?>>
                <td>Inclusive to</td><td>:</td><td>
                <select name="memincwith[]" multiple="multiple">
                    <?php
                        $arr = array('l1'=>'Level 1', 'l2' => 'Level 2', 'l3' => 'Level 3');
                        foreach($arr as $k=>$v){
                            echo '<option value="'.$k.'"';
                            if(isset($memincwith) && strstr($memincwith, $k)){
                                echo ' selected="selected"';
                            }
                            echo '>'.$v.'</option>';
                        }
                    ?>
                </select>
                </td>
            </tr>
            <tr>
                <td></td><td></td><td><input type="submit" name="lonesubmit" value="Save"></td>
            </tr>
        </table>
        
        </form>
    </div>
    <div class="col3">
        <h1>L2</h1>
        <?php
            if(isset($_REQUEST['deleteltwodata'])){
                $db->update('memconfig', array('memdata'=>''), 'memname = "l2"');
            }
            if(isset($_REQUEST['ltwosubmit'])){
                $incw = '';
                if($_REQUEST['memtype'] == 'inc'){
                    foreach ($_REQUEST['memincwith'] as $so){
                        $incd[] = $so;
                    }
                    $incw = json_encode($incd);
                }
                $r = $cache->configcache('l2', $_REQUEST['memtype'], $_REQUEST['memlength'], $incw, $_REQUEST['memaccesstime']);
                if($r){
                    echo '<div style="position:fixed; left: 0; right: 0; bottom: 0; background: yellow; height: 30px; font-weight: bold; text-align:center;">Changes Saved in '.$r.'</div>';
                }
            }
            $q = "SELECT * FROM memconfig WHERE memname='l2'";
            $re = $db->querydb($q, true);
            $memaccesstime = $re->memaccesstime;
            $memincwith = $re->memincwith;
            $memlength = $re->memlength;
            $memtype = $re->memtype;
            if($re->memdata == ''){
                echo 'Cache L2 is Empty!';
            }else{
                ?>
                <form action="configure.php" method="POST">
                    <input type="submit" name="deleteltwodata" value="Empty Level 2 Cache" onclick="return confirm('Are you sure you want to empty Level 2 ?');">
                </form>
                <?php
            }
        ?>
        <form action="configure.php" method="POST">
        <table>
            <tr>
                <td>Access Time</td><td>:</td><td><input type="text" name="memaccesstime" value="<?php if(isset($memaccesstime)){echo $memaccesstime;}?>"></td>
            </tr>
            <tr>
                <td>Size/Capacity</td><td>:</td><td><input type="text" name="memlength" value="<?php if(isset($memlength)){echo $memlength;}?>"></td>
            </tr>
            <tr style="display: none;">
                <td>Level Type</td>
                <td>:</td>
                <td>
                    <select name="memtype" onchange="togglebox(this, 'ltwoincwith')">
                    <?php 
                        $arr = array('exc'=>'Exclusive', 'inc' => 'Inclusive');
                        foreach($arr as $k=>$v){
                            echo '<option value="'.$k.'"';
                            if(isset($memtype) && $memtype == $k){
                                echo ' selected="selected"';
                                if($memtype == 'inc'){
                                    $ifinc =true;
                                }
                            }
                            echo '>'.$v.'</option>';
                        }
                    ?>
                    </select>
                </td>
            </tr>
            <tr style="display: none;" class="ltwoincwith" <?php if($ifinc){ echo 'style="display:block;"'; $ifinc = false;} ?>>
                <td>Inclusive to</td><td>:</td><td>
                <select name="memincwith[]" multiple="multiple">
                    <?php
                        $arr = array('l1'=>'Level 1', 'l2' => 'Level 2', 'l3' => 'Level 3');
                        foreach($arr as $k=>$v){
                            echo '<option value="'.$k.'"';
                            if(isset($memincwith) && strstr($memincwith, $k)){
                                echo ' selected="selected"';
                            }
                            echo '>'.$v.'</option>';
                        }
                    ?>
                </select>
                </td>
            </tr>
            <tr>
                <td></td><td></td><td><input type="submit" name="ltwosubmit" value="Save"></td>
            </tr>
        </table>
        
        </form>
    </div>
    <div class="col3">
        <h1>L3</h1>
        <?php
            if(isset($_REQUEST['deletelthreedata'])){
                $db->update('memconfig', array('memdata'=>''), 'memname = "l3"');
            }
            if(isset($_REQUEST['lthreesubmit'])){
                $incw = '';
                if($_REQUEST['memtype'] == 'inc'){
                    foreach ($_REQUEST['memincwith'] as $so){
                        $incd[] = $so;
                    }
                    $incw = json_encode($incd);
                }
                $r = $cache->configcache('l3', $_REQUEST['memtype'], $_REQUEST['memlength'], $incw, $_REQUEST['memaccesstime']);
                if($r){
                    echo '<div style="position:fixed; left: 0; right: 0; bottom: 0; background: yellow; height: 30px; font-weight: bold; text-align:center;">Changes Saved in '.$r.'</div>';
                }
            }
            $q = "SELECT * FROM memconfig WHERE memname='l3'";
            $re = $db->querydb($q, true);
            $memaccesstime = $re->memaccesstime;
            $memincwith = $re->memincwith;
            $memlength = $re->memlength;
            $memtype = $re->memtype;
            if($re->memdata == ''){
                echo 'Cache L3 is Empty!';
            }else{
                ?>
                <form action="configure.php" method="POST">
                    <input type="submit" name="deletelthreedata" value="Empty Level 3 Cache" onclick="return confirm('Are you sure you want to empty Level 3 ?');">
                </form>
                <?php
            }
            
        ?>
        <form action="configure.php" method="POST">
        <table>
            <tr>
                <td>Access Time</td><td>:</td><td><input type="text" name="memaccesstime" value="<?php if(isset($memaccesstime)){echo $memaccesstime;}?>"></td>
            </tr>
            <tr>
                <td>Size/Capacity</td><td>:</td><td><input type="text" name="memlength" value="<?php if(isset($memlength)){echo $memlength;}?>"></td>
            </tr>
            <tr style="display: none;">
                <td>Level Type</td>
                <td>:</td>
                <td>
                    <select name="memtype" onchange="togglebox(this, 'lthreeincwith')">
                    <?php 
                        $arr = array('exc'=>'Exclusive', 'inc' => 'Inclusive');
                        foreach($arr as $k=>$v){
                            echo '<option value="'.$k.'"';
                            if(isset($memtype) && $memtype == $k){
                                echo ' selected="selected"';
                                if($memtype == 'inc'){
                                    $ifinc =true;
                                }
                            }
                            echo '>'.$v.'</option>';
                        }
                    ?>
                    </select>
                </td>
            </tr>
            <tr style="display: none;" class="lthreeincwith" <?php if($ifinc){ echo 'style="display:block;"'; $ifinc = false;} ?>>
                <td>Inclusive to</td><td>:</td><td>
                <select name="memincwith[]" multiple="multiple">
                    <?php
                        $arr = array('l1'=>'Level 1', 'l2' => 'Level 2', 'l3' => 'Level 3');
                        foreach($arr as $k=>$v){
                            echo '<option value="'.$k.'"';
                            if(isset($memincwith) && strstr($memincwith, $k)){
                                echo ' selected="selected"';
                            }
                            echo '>'.$v.'</option>';
                        }
                    ?>
                </select>
                </td>
            </tr>
            <tr>
                <td></td><td></td><td><input type="submit" name="lthreesubmit" value="Save"></td>
            </tr>
        </table>
        
        </form>
    </div>
</div>

<script>
function togglebox(id, target){
    var val = $(id).val();
    if(val == 'exc'){
        $('.'+target).hide();
    }else{
        $('.'+target).show();
    }
}
</script>
</body>
</html>