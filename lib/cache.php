<?php
  class cache {
      
      public $level;
      public $inc;
      public $incto;
      public $incdata;
      public $excdata;
      public $exclist;
      
      function __construct(){
          global $db;
          $re = $db->querydb("SELECT * FROM memconfig");
            if($re->num_rows){
                while($ro = $re->fetch_object()){
                    $this->level[$ro->memname]['type'] = $ro->memtype;
                    $this->level[$ro->memname]['data'] = json_decode($ro->memdata);
                    $this->level[$ro->memname]['length'] = $ro->memlength;
                    $this->level[$ro->memname]['incwith'] = json_decode($ro->memincwith);
                    $this->level[$ro->memname]['accesstime'] = $ro->memaccesstime;
                    if($ro->memtype == 'inc'){
                        $this->inc[] = $ro->memname;
                        foreach($this->level[$ro->memname]['incwith'] as $v){
                            $this->incto[] = $v;
                        }
                        foreach($this->level[$ro->memname]['data'] as $v){
                            $this->incdata[] = $v;
                        }
                    }
                    if($ro->memtype == 'exc'){
                        $this->exclist[] = $ro->memname;
                        foreach($this->level[$ro->memname]['data'] as $v){
                            $this->excdata[] = $v;
                        }
                    }
                }
            }
            /*
          echo "<pre style='font-size: 11px;'>";
          echo "<br>level: ";
          print_r($this->level);
          echo "<br>inc: ";
          print_r($this->inc);
          echo "<br>incto: ";
          print_r($this->incto);
          echo "<br>incdata: ";
          print_r($this->incdata);
          echo "<br>excdata: ";
          print_r($this->excdata);
          echo "<br>exclist: ";
          print_r($this->exclist);
          echo "</pre>";
          */
      }
      
      function configcache($level, $type, $length, $inc_with){
          global $db;
          $values = array(
                'memname' => $level,
                'memtype' => $type,
                'memlength'=>$length,
                'memincwith'=>json_encode($inc_with)
          );
          
          if($db->insert('memconfig', $values)){
              return true;
          }
          return false;
      }
      
      function getcache($name, $property){
          return $this->level[$name][$property];
      }
      
      function hit($data = false){
          static $count = 0;
          if($data){
              $count++;
          }else{
              return $count;
          }
      }
      
      function miss($data = false){
          static $count = 0;
          if($data){
              $count++;
          }else{
              return $count;
          }
      }
      
      function conflictmiss($data = false){
          static $count = 0;
          if($data){
              $count++;
          }else{
              return $count;
          }
      }
      
      function accesstime($data = 0){
          static $count = 0;
          if($data != 0){
              $count += $data;
          }else{
              return $count;
          }
      }
      
      function get($block){
          $d = '<br>Test Case debug data:'.$block;
          $this->accesstime($this->level['l1']['accesstime']);
          if(in_array($block, $this->excdata)){
              $this->hit(true);
              $d .= '<br>Hit counted';
          }else{
              $this->miss(true);
              $d .= '<br>Miss counted';
              $this->getfrommainmem($block);
          }
          //echo $d;
      }
      
      function fillexclusive($data){
          $this->excdata[] = $data;
          $this->fillinclusive($data);
          if($this->level[$this->exclist[0]]['length'] < count($this->excdata)){
              $this->conflictmiss(true);
          }
      }
      
      function fillinclusive($data){
          $this->incdata[] = $data;
      }
      
      function getfrommainmem($data){
          $this->fillexclusive($data);
      }
      
      function finalize(){
          global $db;
          $i = 0;
          $this->level[$this->exclist[$i]]['data'] = array();
          foreach($this->excdata as $k=>$v){
              $this->level[$this->exclist[$i]]['data'][] = $v;
              if(count($this->level[$this->exclist[$i]]['data']) >= $this->level[$this->exclist[$i]]['length']){
                  $i++;
                  if(isset($this->level[$this->exclist[$i]]['data'])){
                      $this->level[$this->exclist[$i]]['data'] = array();
                  }else{
                      break;
                  }
              }
          }
          $i = 0;
          $this->level[$this->inc[$i]]['data'] = array();
          foreach($this->incdata as $v){
              $this->level[$this->inc[$i]]['data'][] = $v;
              if(count($this->level[$this->inc[$i]]['data']) >= $this->level[$this->inc[$i]]['length']){
                  $i++;
                  if(isset($this->level[$this->inc[$i]]['data'])){
                      $this->level[$this->inc[$i]]['data'] = array();
                  }else{
                      break;
                  }
              }
          }
          /*
          echo "<pre style='font-size: 11px;'>";
          echo "<br>level: ";
          print_r($this->level);
          echo "<br>inc: ";
          print_r($this->inc);
          echo "<br>incto: ";
          print_r($this->incto);
          echo "<br>incdata: ";
          print_r($this->incdata);
          echo "<br>excdata: ";
          print_r($this->excdata);
          echo "<br>exclist: ";
          print_r($this->exclist);
          echo "</pre>";
          */
          foreach($this->level as $k=>$v){
              $values = array(
                    'memdata' => $this->level[$k]['data']
              );
              $db->update('memconfig', $values, 'memname = "'.$k.'"');
          }
      }
      
      function __destruct(){
          //$this->finalize();
      }
  }
?>
