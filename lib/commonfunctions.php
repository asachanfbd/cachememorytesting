<?php
/*Function to find out time in appropriate way*/
function plural($num) {
    if ($num != 1)
        return "s";
}
function calcreltime($date) {
    $diff = time() - ($date);
    if ($diff<60)
        return $diff . " second" . plural($diff) . " ago";
    $diff = round($diff/60);
    if ($diff<60)
        return $diff . " minute" . plural($diff) . " ago";
    $diff = round($diff/60);
    if ($diff<24)
        return $diff . " hour" . plural($diff) . " ago";
    $diff = round($diff/24);
    if ($diff<7)
        return $diff . " day" . plural($diff) . " ago";
    $diff = round($diff/7);
    if ($diff<4)
        return $diff . " week" . plural($diff) . " ago";
    return  "on " . date("F j, Y", ($date));
}
function getRelativeTime($ts){
    return "<span class='timeautoupdate' timestamp='".$ts."' title='".date("l, M d, Y", $ts)." at ".date("h:i A", $ts)."'>".calcreltime($ts)."</span>";
}
function validatemail($email){
          $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex)
        {
        $isValid = false;
        }
        else
        {
        $domain = substr($email, $atIndex+1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);
        if ($localLen < 1 || $localLen > 64)
        {
        $isValid = false;
        }
        else if ($domainLen < 1 || $domainLen > 255)
        {
        $isValid = false;
        }
        else if ($local[0] == '.' || $local[$localLen-1] == '.')
        {
        $isValid = false;
        }
        else if (preg_match('/\\.\\./', $local))
        {
        $isValid = false;
        }
        else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
        {
        $isValid = false;
        }
        else if (preg_match('/\\.\\./', $domain))
        {
        $isValid = false;
        }
        else if
        (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
        str_replace("\\\\","",$local)))
        {
        if (!preg_match('/^"(\\\\"|[^"])+"$/',
        str_replace("\\\\","",$local)))
        {
        $isValid = false;
        }
        }
        if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
        {
        $isValid = false;
        }
        }
        return $isValid;
        }
function encrypt($string){
    return $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5("HFH65665CTRY4564D5Y6D4DTR45T3S4T3S0"), $string, MCRYPT_MODE_CBC, md5(md5("HFH65665CTRY4564D5Y6D4DTR45T3S4T3S0"))));
}
function decrypt($encrypted){
    return $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5("HFH65665CTRY4564D5Y6D4DTR45T3S4T3S0"), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5("HFH65665CTRY4564D5Y6D4DTR45T3S4T3S0"))), "\0");
}
/**
* Function gets the db configurations from file.
* 
*/
function getdbconfig(){
    global $REF;
          $filedir=$REF.'/config';
          $filename="dbconfig.php";
          $fileaddress=$filedir.'/'.$filename;
                        
          if(!file_exists($filedir)) //part A
          {
                mkdir($filedir);//directory created
          }
          $d = '';
          if(file_exists($fileaddress)){
              $fhandle= fopen($fileaddress,'r+');
              $d = json_decode(decrypt(fgets($fhandle)), true);
              fclose($fhandle);
          }
          if($d != ''){
              return $d;
          }
          return false;
}
function createfile($file, $content){
              $filedir='.'.'\\'.'config';
              $filename=$file;
              $fileaddress=$filedir.'\\'.$filename;
                            
              if(!file_exists($filedir)) //part A
              {
                    mkdir($filedir);//directory created
              }
              if(!file_exists($fileaddress)){
                  $fhandle= fopen($fileaddress,'a');
                  fclose($fhandle);
              }
                  // Let's make sure the file exists and is writable first.
              if (is_writable($fileaddress)) {
                      // The file pointer is at the bottom of the file hence
                      // that's where $filecontent will go when we fwrite() it.
                        if (!$handle = fopen($fileaddress, 'w+')) {
                        echo "Cannot open file (".$fileaddress.")";
                        return FALSE;
                        }
                         // Write $filecontent which is of array type and stored in $vals to our opened file.
                         if (fwrite($handle, $content) === FALSE) {
                        // echo "Cannot write to file ($fileaddress)";
                         }
                         fclose($handle);
                         return "<p class=green>Configurations Saved</p>";
                  } 
                 return '<p class="red">Fatal Error: Configurations not saved. Please try again!</p>';
}
?>
