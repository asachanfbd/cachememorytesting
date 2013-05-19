<?php
  class dashboard{
      public function addchangelog($msg){
          global $user;
          $values = array(
                    'msg'       =>  $_GET['changelogdata'],
                    'addedby'   =>  $user->getid()
          );
          db::insert('changelog', $values);
          $result = '<div style="display:none;"><i>'.date('M d, Y').' by '.$user->getfullname().' &lt;<a href="mailto:'.$user->getemail().'">'.$user->getemail().'</a>&gt;</i></div>';
          $result .= '<span> - '.$_GET['changelogdata'].' - ['.getRelativeTime(time()).']</span>';
          return $result;
      }
      
      public function getchangelog(){
          global $db, $user;
          $re = $db->querydb("SELECT * FROM changelog ORDER BY added DESC LIMIT 0, 30");
          $chlog = '<ul>';
          if($re->num_rows){
              while($ro = $re->fetch_object()){
                  $name = $user->getuserinfo($ro->addedby);
                  $chlog .= '<li title="Added by '.$name->fname." ".$name->lname.'">'.$ro->msg.' - ['.getRelativeTime($ro->added).']</li>';
                }
                return $chlog;
            }
      }
  }
  
  $dashboard = new dashboard();
?>
