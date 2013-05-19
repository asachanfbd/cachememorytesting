<?php
  class pages{
      
      function __construct(){
          global $db;
          $db->checktable('page_tree');
          $db->checktable('pages');
      }
      
      static $page = '';
      
      static $name = '';
      
      private function getpage($name){
          global $db;
          if(self::$name != $name){
               self::$name = $name;
               self::$page = $db->querydb("SELECT p.id, t.name, t.parent, p.title, p.content, p.editedby, p.seotags, t.priority, p.added, p.modified FROM pages as p, page_tree as t WHERE p.name = '".$name."' && p.name = t.name ORDER BY p.added DESC", true);
          }
          return self::$page;
      }
      
      function getcontent($name){
          $p = $this->getpage($name);
          return stripslashes($p->content);
      }
      
      public function gettitle($name){
          $p = $this->getpage($name);
          if(is_object($p)){
             return stripslashes($p->title);   
          }
      }
      
      public function getname($name){
          return $name;
      }
      
      public function getcreated($name){
          global $db;
          $q = "SELECT MIN(added) as created FROM pages WHERE name = '".$name."'";
          $r = $db->querydb($q, true);
          return $r->created;
      }
      
      public function getauthor($name){
          $p = $this->getpage($name);
          return $p->editedby;
      }
      
      public function getlastedited($name){
          $p = $this->getpage($name);
          return $p->modified;
      } 
      
      public function gettype($name){
          $p = $this->getpage($name);
          return $p->type;
      }
      
      public function getseotags($name){
          $p = $this->getpage($name);
          return $p->seotags;
      }
      
      public function getpriority($name){
          $p = $this->getpage($name);
          return $p->priority;
      }
      
      public function getparent($name){
          $p = $this->getpage($name);
          return stripslashes($p->parent);
      }
      
      private function insert($tbl, $val){
          global $db;
          return $db->insert($tbl, $val);
      }
      
      private function update($tbl, $val, $con){
          global $db;
          return $db->update($tbl, $val, $con);
      }
      
      function createpage($title = '', $content = '',$parent='', $priority = 0){
         global $user, $db, $errorvar;
         if(ctype_digit($priority)){
             if($title != $this->gettitle($parent)){
         $name = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $title);
         $name = trim($name);
         $name = strtolower( str_replace(" ", "_", $name));
         if(strstr($name, "__")){
             $name = str_replace("__", "", $name);
         }
        
          $q = "SELECT * FROM page_tree WHERE name = '".$name."'and parent='".$parent."'";
          $r = $db->querydb($q);
          if($r->num_rows < 1){
            $qn = "SELECT * FROM page_tree WHERE name = '".$name."'";
            $rn = $db->querydb($qn);
            if($rn->num_rows>0){
                $name.=substr(uniqid(), 0, 3);
            }
                $val  = array(
                    'name'      =>  $name,
                    'parent'    =>  $parent,
                    'priority'  =>  $priority
                );
                $this->insert('page_tree', $val);
                $values = array(
                      'id'        =>  uniqid(),
                      'name'      =>  $name,
                      'title'     =>  $title,
                      'content'   =>  $content,
                      'editedby'  =>  $user->getid()
                );
                $this->insert('pages', $values);
                return true;
          }
          }
          else{
              $errorvar="Parent page and Child page can not have same name";
              return false;
          }
          $errorvar = 'Error in creating page';
          return false;
      }else{
          $errorvar='Priority should be numeric';
          return false;
      }
      }
      
      function setcontent($name, $title = '', $content = '',$parent='', $priority = 0){
          global $user, $db;
          if(is_numeric($priority)){
          $q = "SELECT * FROM page_tree WHERE name = '".$name."'";
          $r = $db->querydb($q, true);
          if($r->parent != $parent || $r->priority != $priority){
                $val  = array(
                    'name'      =>  $name,
                    'parent'    =>  $parent,
                    'priority'  =>  $priority
                );
                $this->update('page_tree', $val, 'name = "'.$name.'"');
          }
          $values = array(
                'id'        =>  uniqid(),
                'name'      =>  $name,
                'title'     =>  $title,
            //    'parent'    =>  $parent,
                'content'   =>  $content,
                'editedby'  =>  $user->getid(),
          );
          $this->insert('pages', $values);
          return true;
      }else{
          $errorvar='Priority should be numeric';
      }
       }
  }
?>
