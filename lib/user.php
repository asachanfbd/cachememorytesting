<?php
  class user{
      
      private static $loginstatus = FALSE;
      
      private static $user = '';
      
      private static $temp_id = '';
      
      private static $temp_info = '';
      
      
      
      public function iflogin(){
          global $db;
          if(isset($_COOKIE['ls']) && !self::$loginstatus){
              $ro = $this->getsessioninfo($_COOKIE['ls']);
              if($ro->expire <= time()){
                  $this->logout($_COOKIE['ls']);
              }else{
                  $this->refreshlogin($_COOKIE['ls']);
                  self::$loginstatus = TRUE;
                  self::$user = $db->querydb("SELECT 
                                                users_info.id AS id, 
                                                users_info.fname AS fname, 
                                                users_info.lname AS lname, 
                                                users_email.email AS email,
                                                users_logininfo.added AS added
                                              FROM users_info, users_email, users_logininfo 
                                              WHERE users_email.id = '".$ro->id."' && users_info.id = '".$ro->id."' && users_logininfo.id = '".$ro->id."'", true);
              }
          }
          return self::$loginstatus;
      }
      
      public function logout($id){
          global $db;
          setcookie('ls',"",123);
          $r = $db->update('users_sessions', array('expire' => time()-1), 'sessionid = "'.$id.'"');
          if($r){
              return true;
          }
          return false;  
      }
      
      private function getsessioninfo($sessionid){
          global $db;
          $re = $db->querydb("SELECT * FROM users_sessions WHERE sessionid = '".$sessionid."'");
          if($re->num_rows){
              while($ro = $re->fetch_object()){
                  return $ro;
              }
          }
      }
      
      /**
      * Returns all the active sessions of a user
      * 
      * @param mixed $userid 
      */
      private function getallsessions($userid){
          global $db;
          $re = $db->querydb("SELECT * FROM users_sessions WHERE id = '".$userid."' && expire > ".time());
          if($re->num_rows){
              $arr = array();
              $i=0;
              while($ro = $re->fetch_assoc()){
                  $arr[$i++] = $ro;
              }
              return $arr;
          }
      }
      
      public function getfullname($id = ""){
          if($this->iflogin()){
              if($id == ""){
                  return $this->getfirstname().' '.$this->getlastname();
              }else{
                  $u = $this->getuserinfo($id);
                  return $u->fname." ".$u->lname;
              }
              
          }
      }
      
      public function getfirstname($id = ""){
          if($this->iflogin()){
              if($id != ""){
                  $r = $this->getuserinfo($id);
                  return $r->fname;
              }else{
                return stripcslashes(self::$user->fname);
              }
          }
      }
      
      public function getlastname(){
          if($this->iflogin()){
              return stripcslashes(self::$user->lname);
          }
      }
      
      public function getusercreatedtime(){
          if($this->iflogin()){
              return self::$user->added;
          }
      }
      
      public function getuserinfo($id){
          global $db;
          if(self::$temp_id != $id){
              self::$temp_info = $db->querydb("SELECT 
                                users_info.id AS id, 
                                users_info.fname AS fname, 
                                users_info.lname AS lname, 
                                users_email.email AS email,
                                users_logininfo.added AS added
                              FROM users_info, users_email, users_logininfo 
                              WHERE users_email.id = '".$id."' && users_info.id = '".$id."' && users_logininfo.id = '".$id."'", true);
          }
          return self::$temp_info;
          
      }
      
      public function getpasswordtime(){
          global $db;
          $re = $db->querydb("SELECT * FROM users_logininfo WHERE id = '".$this->getid()."'");
          if($re->num_rows){
              $ro = $re->fetch_object();
              return $ro->modified;
          }else{
              return 0;
          }
      }
      
      public function getpassword(){
          global $db;
          $re = $db->querydb("SELECT * FROM users_logininfo WHERE id = '".$this->getid()."'");
          if($re->num_rows){
              $ro = $re->fetch_object();
              return $ro->password;
          }else{
              return false;
          }
      }
      
      public function getemail(){
          if($this->iflogin()){
              return stripcslashes(self::$user->email);
          }
      }
      
      public function getid(){
          if($this->iflogin()){
              return stripcslashes(self::$user->id);
          }
      }
      
      public function setuser($fname, $lname, $email,$val){
          $val=$val;
          global $db;
          $id = md5($email);
          $password = substr(md5(rand(0,100).$id), 0, 8);
          $values = array(
                        'id'        => $id,
                        'password'  => md5($password)
          );
          $db->insert('users_logininfo', $values);
          unset($values);
          $values = array(
                        'id'        => $id,
                        'fname'     => $fname,
                        'lname'     => $lname
          );
          $db->insert('users_info', $values);
          unset($values);
          $values = array(
                        'id'        => $id,
                        'email'     => $email
          );
          $db->insert('users_email', $values);
          unset($values);
          $values = array(
                        'id'        => $id,
                        'email'     => $email,
                        'fullname'  => $fname." ".$lname
          );
          unset($values);
          $values = array(
                        'name'      =>  $fname." ".$lname,
                        'email'     =>  $email,
                        'password'  =>  $password
          );
          $this->setprivilege($val,$id);
          $this->usermail($values);
          /**
          * mail login credentials to user.
          */
          
          return $values;
      }
      
      public function deluser($id){
          global $db;
          if($id == $this->getid()){
              return false;
          }
          $db->querydb("DELETE FROM users_logininfo WHERE id = '".$id."'");
          return true;
      }
      
      public function getprivileges(){
          global $db;
          $re = $db->querydb("SELECT * FROM users_privilege WHERE id = ".getid());
          $r = array();
          if($re->num_rows){
              while($ro = $re->fetch_object){
                  $r[] = $ro->priviledge;
              }
          }
          return $r;
      }
      
      public function getusers(){
          global $db;
          $q = $db->querydb("SELECT id FROM users_logininfo");
          if($q->num_rows){
              $a = array();
              while($r = $q->fetch_assoc()){
                  if(count($r)){
                      $a[] = $r;
                  }
              }
              return $a;
          }
      }
      
      private function update($tbl, $vars, $cond){
          global $db;
          $db->update($tbl, $vars, $cond);
          return true;
      }
      
      public function setname($fname, $lname){
          $arr = array(
                'fname' =>  addslashes($fname),
                'lname' =>  addslashes($lname)
          );
          $this->update('users_info', $arr, "id = '".$this->getid()."'");
          return true;
      }
      
      public function setemail($email){
          $arr = array(
                'email' =>  addslashes($email)
          );
          $this->update('users_email', $arr, "id = '".$this->getid()."'");
          return true;
      }
      
      public function setpassword($pass){
          $arr = array(
                'password' =>  md5($pass)
          );
          $this->update('users_logininfo', $arr, "id = '".$this->getid()."'");
          return true;
      }
      
      public function forgotpassword($email){
          global $db;
          $re=$db->querydb("select * from users_email where email ='".$email."'",true );
             if($re){
                 $id=$re->id;
                 // print_r($re->id);
                  $arr=substr(uniqid(), 0, 8);
                  $value=md5($arr);
                  $val=array(
                    'password'    =>   $value
                  );
                  $subject="Password Recovery";
                  $msg="Your Password is:".$arr;
                  //echo $value;
                  $q=$db->update('users_logininfo',$val,"id='".$id."'");
                  if($q){
                      mail($email,$subject,$msg);
                      return true;
                  }
                  else{
                      return false;
                  }
              }else{
                  return false;
              }
      }
      
      public function contactformmail($value){
          $to="admin@coravity.com";
          $msg=$value['msg'];
          $header="From".$value['email'];
          $subject="Query regarding trouble in login on demo interface";
          if(mail($to,$subject,$msg,$header)){
              return true;
          }
          else {
              return true;
          }
      }
      
      public function resetpassword($id){
          $values = array(
                        'name' => $this->getfullname(),
                        'email'=> $this->getemail(),
                        'password' => uniqid()
          );
          $arr = array(
                'password' =>  md5($values['password'])
          );
          $this->update('users_logininfo', $arr, "id = '".$id."'");
          
          if($this->usermail($values)){
              return TRUE;
          }else{
              return false;
          }
      }
      
      private function usermail($values){
        if(stristr($_SERVER['HTTP_HOST'], 'localhost')){
            return false;
        }
        $to = $values['email'];
        $temp = explode('.', $_SERVER['SERVER_NAME']);
        $hostname = $temp[count($temp) - 2] . '.' . $temp[count($temp) - 1];
        $subject = 'Demo Interface Login Account Information';
        $headers = "From: no-reply@coravity.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $message = '<html><body>';
        $message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
        $message .= "<tr style='background: #eee;'><th colspan='2'>Account Information</th></tr>";
        $message .= "<tr><td><strong>Name:</strong> </td><td>" . $values['name'] . "</td></tr>";
        $message .= "<tr><td><strong>Email:</strong> </td><td>" . $values['email'] . "</td></tr>";
        $message .= "<tr><td><strong>Password:</strong> </td><td>" . $values['password'] . "</td></tr>";
        $message .= "<tr><td><strong>Login Link:</strong> </td><td><a href='http://demo.coravity.com//'>http://demo.coravity.com/</a></td></tr>";
        $message .= "</table>";
        $message .= "Please do not reply to this e-mail. For any queries contact <a href='mailto:info@coravity.com'>info@coravity.com</a>";
        $message .= "</body></html>";
        if(mail($to, $subject, $message, $headers)){
            return true;
        }else{
            return false;
        }
      }
      
      public function dologin($email, $pass, $remember = FALSE){
          global $db;
          $pass = md5($pass);
          $re = $db->querydb("SELECT users_email.id AS id, users_email.email AS email, users_logininfo.password AS password
                                FROM users_email, users_logininfo
                                WHERE users_email.email = '".$email."'
                                AND users_email.id = users_logininfo.id");
          if($re->num_rows){
              $ro = $re->fetch_object();
              if($pass == $ro->password){
                  $id = uniqid();
                  if($remember){
                      $time = time()+(60*60*24*365*10);
                      setcookie('ls', $id, $time);
                  }else{
                      $time = time()+(60*60*2);
                      setcookie('ls', $id);
                  }
                  $values = array(
                            'id'            =>  $ro->id,
                            'sessionid'     =>  $id,
                            'ip'            =>  $_SERVER['REMOTE_ADDR'],
                            'ua'            =>  $_SERVER['HTTP_USER_AGENT'],
                            'expire'        =>  $time
                  );
                  $db->insert('users_sessions', $values);
                  return true;
              }else{
                  return false;
              }
          }else{
              return false;
          }
      }
      
      /**
      * this will refresh the login time and expire time of given session.
      * 
      */
      function refreshlogin($id){
            global $db;
            setcookie("ls",$id,time()+3600);
            $r=$db->update('users_sessions',array('expire' =>time()+3600),'sessionid="'.$id.'"');
            if($r){
                return true;
            }
            else{
                return false;
            }
      }
      
      public function setprivilege($valar,$id){
          global $db;
          $vls=implode(",",$valar);
          $array = array(
                'id'    =>  $id,
                'permissions'=>$vls
          );
          $q = $db->insert('users_privilege', $array);
        
         return true;
      }
      
      public function haspermission($permission,$id){
          global $db;
          if($id=='superadmin'){
              return true;
          }
          $q="SELECT * from users_privilege where id='".$id."'";
          $ro=$db->querydb($q);
          if($ro->num_rows){
              while($r=$ro->fetch_object()){
              $val=explode(",",$r->permissions);
          }
          //print_r($val);
           if(in_array($permission,$val)){
              return true;
          }
          }
         else{
              return false;
          } 
          
      }
      /**
      * TODO: Test above all the functions and create something for handling notification & priviledge(add more & delete old)
      */
  }
  
  
?>
