<?php
  class enquiry{
      
      function getall(){
          global $db;
          $re = $db->querydb("SELECT * FROM enquiries ORDER BY added");
          if($re->num_rows){
              $arr = array();
              while($ro = $re->fetch_object()){
                  $arr[] = $ro;
              }
              return $arr;
          }else{
              return false;
          }
      }
      
      function getread(){
          global $db;
          $re = $db->querydb("SELECT * FROM enquiries WHERE readstatus = '2' ORDER BY added DESC LIMIT 0,30");
          if($re->num_rows){
              $arr = array();
              while($ro = $re->fetch_object()){
                  $arr[] = $ro;
              }
              return $arr;
          }else{
              return false;
          }
      }
      
      function getunread(){
          global $db;
          $re = $db->querydb("SELECT * FROM enquiries WHERE readstatus = '1' ORDER BY added DESC LIMIT 0,30");
          if($re->num_rows){
              $arr = array();
              while($ro = $re->fetch_object()){
                  $arr[] = $ro;
              }
              return $arr;
          }else{
              return false;
          }
      }
      
      function getenquiry($id){
          global $db;
          $this->markasread($id);
          return $db->querydb("SELECT * FROM enquiries WHERE id = '".$id."'", true);
      }
      
      function markasread($id){
          global $db;
          $values = array('readstatus' => '2');
          $db->update('enquiries', $values, "id = '".$id."'");
          return true;
      }
      
      function getbyrange($start, $end){
          global $db;
          $re = $db->querydb("SELECT * FROM enquiries WHERE added > '".$start."' && added < '".$end."' ORDER BY modified DESC LIMIT 0,30");
          if($re->num_rows){
              $arr = array();
              while($ro = $re->fetch_object()){
                  $arr[] = $ro;
              }
              return $arr;
          }else{
              return false;
          }
      }
      
      public function add($name, $email, $phone, $address, $msg, $services){
          
          global $db, $sendername;
            $emq=$db->querydb('SELECT * FROM email_config');
            $flag=0;
            $to_arr=array();
            if($emq->num_rows>0){
                while($emrow=$emq->fetch_object()){
                    if($flag==0){
                        $to_arr['sendername']=$emrow->sendername;
                        $flag++;
                    }
                    $to_arr[$emrow->type_for]=$emrow->email;
                }
            }
            $from=$to_arr['noreply'];
          
          
          $values = array(
                        'id'        =>       uniqid(),
                        'name'      =>       $name,
                        'email'     =>       $email,
                        'phone'     =>       $phone,
                        'address'   =>       $address,
                        'selcetservices'=>   $services,
                        'message'   =>       $msg
          );
          $body  = "Name: ".$name."\r\n";
          $body .= "Address: ".$address."\r\n";
          $body .= "Phone: ".$phone."\r\n";
          $body .= "Email: ".$email."\r\n";
          $body .= "Message: ".$msg."\r\n";
          $headers = 'From: '.$from."\r\n";
         // $headers .= 'Cc: sales@universalprideinteriors.com'."\r\n";
         if(mail($to_arr['admin'], 'New Query on universalprideinteriors.com', $body, $headers)){
         return $db->insert('enquiries', $values);
          }
      }
      
  }
?>
