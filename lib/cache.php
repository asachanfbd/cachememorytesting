<?php
  class cache {
      
      public $level;
      public $inc;          /* list of names of inclusive memories */
      public $inclist;      /* list of names of inclusive memories */
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
                        if(is_array($this->level[$ro->memname]['incwith'])){
                            foreach($this->level[$ro->memname]['incwith'] as $v){
                                $this->incto[] = $v;
                            }
                        }
                        if(is_array($this->level[$ro->memname]['data'])){
                            foreach($this->level[$ro->memname]['data'] as $v){
                                $this->incdata[] = $v;
                            }
                        }
                    }
                    if($ro->memtype == 'exc'){
                        $this->exclist[] = $ro->memname;
                        if(is_array($this->level[$ro->memname]['data'])){
                            foreach($this->level[$ro->memname]['data'] as $v){
                                $this->excdata[] = $v;
                            }
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
      
      function configcache($level, $type, $length, $inc_with, $accesstime){
          global $db;
          $values = array(
                'memname' => $level,
                'memtype' => $type,
                'memlength'=>$length,
                'memincwith'=>json_encode($inc_with),
                'memaccesstime'=>$accesstime
          );
          
          if($db->replace('memconfig', $values)){
              return $level;
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
          return $this->levelone($block);
          //echo $d;
      }
      
      function levelone($blockname = ''){
          $ltype = 'l1';
          static $access = 0;
          static $hits = 0;
          static $miss = 0;
          if($blockname != 'status'){
              $access++;
              if(is_array($this->level[$ltype]['data']) && in_array($blockname, $this->level[$ltype]['data'])){
                  $hits++;
                  $this->moveup($blockname);
                  return true;
              }else{
                  $miss++;
                  return $this->leveltwo($blockname);
              }
          }else{
              //get the status of this cache
              $arr = array(
                    'access' => $access,
                    'hits' => $hits,
                    'miss' => $miss
              );
              return $arr;
          }
      }
      
      function leveltwo($blockname = ''){
          $ltype = 'l2';
          static $access = 0;
          static $hits = 0;
          static $miss = 0;
          if($blockname != 'status'){
              $access++;
              if(is_array($this->level[$ltype]['data']) && in_array($blockname, $this->level[$ltype]['data'])){
                  $hits++;
                  $this->moveup($blockname);
                  return true;
              }else{
                  $miss++;
                  return $this->levelthree($blockname);
              }
          }else{
              //get the status of this cache
              $arr = array(
                    'access' => $access,
                    'hits' => $hits,
                    'miss' => $miss
              );
              return $arr;
          }
      }
      
      function levelthree($blockname = ''){
          $ltype = 'l3';
          static $access = 0;
          static $hits = 0;
          static $miss = 0;
          if($blockname != 'status'){
              $access++;
              if(is_array($this->level[$ltype]['data']) && in_array($blockname, $this->level[$ltype]['data'])){
                  $hits++;
                  $this->moveup($blockname);
                  return true;
              }else{
                  $miss++;
                  return $this->load_from_main_memory($blockname);
              }
          }else{
              //get the status of this cache
              $arr = array(
                    'access' => $access,
                    'hits' => $hits,
                    'miss' => $miss
              );
              return $arr;
          }
      }
      
      function load_from_main_memory($blockname = ''){
          $ltype = 'l3';
          static $access = 0;
          $access++;
          $this->excdata[] = $blockname;
          $this->incdata[] = $blockname;
          $this->fell_levels();
          return true;
      }
      
      function fell_levels(){
          global $db;
          $i = 0;
          $this->level[$this->exclist[$i]]['data'] = array();
          
          foreach(array_reverse($this->excdata) as $k=>$v){
              $this->level[$this->exclist[$i]]['data'][] = $v;
              
              if(count($this->level[$this->exclist[$i]]['data']) >= $this->level[$this->exclist[$i]]['length']){
                  $i++;
                  //echo '<pre style="font-size: 10px;">'.print_r($this->level, true).'</pre>'.print_r($this->exclist, true);
                  if(isset($this->exclist[$i])){
                      $this->level[$this->exclist[$i]]['data'] = array();
                  }else{
                      break;
                  }
              }
          }
          $i = 0;
          $this->level[$this->inc[$i]]['data'] = array();
          foreach(array_reverse($this->incdata) as $v){
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
      }
      
      function moveup($blockname){
          //move up in exclusive levels
          $k = array_search($blockname, $this->excdata);
          unset($this->excdata[$k]);
          $this->excdata[] = $blockname;
          //move up in inclusive levels
          $k = array_search($blockname, $this->incdata);
          unset($this->incdata[$k]);
          $this->incdata[] = $blockname;
      }
      
      function output(){
          $output['l1'] = $this->levelone('status');
          $output['l2'] = $this->leveltwo('status');
          $output['l3'] = $this->levelthree('status');
          $roundoff = 2;
          return '
          <table>
                    <tr>
                        <th></th>
                        <th>Level 1</th>
                        <th>Level 2</th>
                        <th>Level 3</th>
                        <th>Total</th>
                    </tr>
                    <tr>
                        <th>Access time</th>
                        <td>'.$output['l1']['access'].' * '.$this->level['l1']['accesstime'].' = <b>'.($output['l1']['access']*$this->level['l1']['accesstime']).'</b></td>
                        <td>'.$output['l2']['access'].' * '.$this->level['l2']['accesstime'].' = <b>'.($output['l2']['access']*$this->level['l2']['accesstime']).'</b></td>
                        <td>'.$output['l3']['access'].' * '.$this->level['l3']['accesstime'].' = <b>'.($output['l3']['access']*$this->level['l3']['accesstime']).'</b></td>
                        <td><b>'.(($output['l1']['access']*$this->level['l1']['accesstime']) + ($output['l2']['access']*$this->level['l2']['accesstime']) + ($output['l3']['access']*$this->level['l3']['accesstime'])).'</b></td>
                    </tr>
                    <tr>
                        <th>Hits Count</th>
                        <td>'.$output['l1']['hits'].'</td>
                        <td>'.$output['l2']['hits'].'</td>
                        <td>'.$output['l3']['hits'].'</td>
                        <td><b>'.($output['l1']['hits'] + $output['l2']['hits'] + $output['l3']['hits']).'</b></td>
                    </tr>
                    <tr>
                        <th>Miss Count</th>
                        <td>'.$output['l1']['miss'].'</td>
                        <td>'.$output['l2']['miss'].'</td>
                        <td>'.$output['l3']['miss'].'</td>
                        <td><b>'.($output['l1']['miss'] + $output['l2']['miss'] + $output['l3']['miss']).'</b></td>
                    </tr>
                    <tr>
                        <th>Local Miss Rate</th>
                        <td>'.$output['l1']['miss'].' / '.$output['l1']['access'].' = '.round(($output['l1']['miss']/$output['l1']['access']), $roundoff).'</td>
                        <td>'.$output['l2']['miss'].' / '.$output['l2']['access'].' = '.round(($output['l2']['miss']/$output['l2']['access']), $roundoff).'</td>
                        <td>'.$output['l3']['miss'].' / '.$output['l3']['access'].' = '.round(($output['l3']['miss']/$output['l3']['access']), $roundoff).'</td>
                        <td>'.round(round($output['l1']['miss']/$output['l1']['access'], $roundoff) + round($output['l2']['miss']/$output['l2']['access'], $roundoff) + round($output['l3']['miss']/$output['l3']['access'], $roundoff), $roundoff).'</td>
                    </tr>
                    <tr>
                        <th>Global Miss Rate</th>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <th>Hit Rate</th>
                        <td>'.$output['l1']['hits'].' / '.$output['l1']['access'].' * 100 = '.round(($output['l1']['hits']/$output['l1']['access'])*100, $roundoff).'%</td>
                        <td>'.$output['l2']['hits'].' / '.$output['l2']['access'].' * 100 = '.round(($output['l2']['hits']/$output['l2']['access'])*100, $roundoff).'%</td>
                        <td>'.$output['l3']['hits'].' / '.$output['l3']['access'].' * 100 = '.round(($output['l3']['hits']/$output['l3']['access'])*100, $roundoff).'%</td>
                        <td>'.round(round((($output['l1']['hits']/$output['l1']['access'])*100 + ($output['l2']['hits']/$output['l2']['access'])*100 + ($output['l3']['hits']/$output['l3']['access'])*100), $roundoff)/3, $roundoff).'% avg.</td>
                    </tr>
                    <tr>
                        <th>Miss Rate</th>
                        <td>'.(100 - round($output['l1']['hits']/$output['l1']['access'], $roundoff)*100).'%</td>
                        <td>'.(100 - round($output['l2']['hits']/$output['l2']['access'], $roundoff)*100).'%</td>
                        <td>'.(100 - round($output['l3']['hits']/$output['l3']['access'], $roundoff)*100).'%</td>
                        <td>'.(round(((100 - round($output['l1']['hits']/$output['l1']['access'], $roundoff)*100) + (100 - round($output['l2']['hits']/$output['l2']['access'], $roundoff)*100) + (100 - round($output['l3']['hits']/$output['l3']['access'], $roundoff)*100)), $roundoff)/3).'% avg.</td>
                    </tr>
                </table>';
      }
      /*
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
      */
      
      function finalize(){
          global $db;
          $i = 0;
          $this->level[$this->exclist[$i]]['data'] = array();
          
          foreach(array_reverse($this->excdata) as $k=>$v){
              $this->level[$this->exclist[$i]]['data'][] = $v;
              
              if(count($this->level[$this->exclist[$i]]['data']) >= $this->level[$this->exclist[$i]]['length']){
                  $i++;
                  //echo '<pre style="font-size: 10px;">'.print_r($this->level, true).'</pre>'.print_r($this->exclist, true);
                  if(isset($this->exclist[$i])){
                      $this->level[$this->exclist[$i]]['data'] = array();
                  }else{
                      break;
                  }
              }
          }
          $i = 0;
          $this->level[$this->inc[$i]]['data'] = array();
          foreach(array_reverse($this->incdata) as $v){
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
                    'memdata' => json_encode($this->level[$k]['data'])
              );
              $db->update('memconfig', $values, 'memname = "'.$k.'"');
          }
      }
      
      function __destruct(){
          //$this->finalize();
      }
  }
?>
