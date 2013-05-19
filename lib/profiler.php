<?php
/**
* Class & File Name: db.php
* Date: 28-08-2012
* Company: Coravity Infotech
* Developer: Sudhanshu Mishra
* Description: 
* USAGE: To use the profiler follow the following steps
* 
* BASIC:---------------------------------------------
* START:                        profiler::start(TRUE);
* Add Profiler Step Msg:        profiler::add('your message here');
* Display report                profiler::display();
* 
* ADVANCED:-------------------------------------------
* To calculate the time between any two steps pass return value of first call to profiler:add to the second call to profiler:add as second argument
* ex.   $abc = profiler::add('starting step from where time to calculate');
*       profiler::add('ending step where time diff. to report', $a);
* 
*/
  class profiler{
      /**
      * $record keeps the status of profiler to profile the page or not.
      * 
      * @var mixed holds boolen TRUE/FLASE
      */
      private static $record = FALSE;
      /**
      * $msg is used for holding the html message to be displayed upon complition.
      * 
      * @var mixed
      */
      private static $msg = "";
      /**
      * 
      */
      
      /**
      * if called with TRUE it would initialize the profiler. A FLASE passed value would stop it.
      * 
      * @param mixed $stat > boolen value TRUE/FALSE
      */
      public function __construct($stat = FALSE){
          if($stat){
              self::$record = TRUE;
              $this->add("Profiler Started");
          }else{
              self::$record = FALSE;
              $this->add("Profiler Stopped");
          }
      }
      /**
      * Function to be called on every step to be profiled.
      * 
      * @param mixed $msg The message to be displayed on profiler report.
      * @param mixed $t This must be the return value of profiler::add(...) at any time, the time difference would be shown in Report in small braces with step time.
      * @param mixed $final If set to TRUE it would add total runtime to $msg to complete the report. We can call this in between to classify the report.
      */
      public function add($msg, $t = null, $final = FALSE){
          static $profiler_msg = "", $i = 1;
          static $total_time = 0;
          if(self::$record){
              $class = 'green';
              $time = $this->exec_time();
              $total_time += $time;
              if($time >= 20){
                  $class = 'red';
              }
              $a = "";      //$a temp. variable
              if($t !== null){
                  $a = "(".($total_time - $t).")";
              }
              self::$msg .= "\n<div class='profiler_row ".$class."'>\n\t<div class='profiler_sno'>".$i++."</div>\n\t<div class='profiler_msg'>".$msg."</div>\n\t<div class='profiler_time'>".$time.$a."</div>\n\t<div class='profiler_time'>".$total_time."</div>\n</div>";
          }
          if($final){
              self::$msg .= "\n<div class='profiler_row'>\n\t<strong>\n\t\t<div class='profiler_sno'>&nbsp;</div>\n\t\t<div class='profiler_msg'>Total Run Time:</div>\n\t<div class='profiler_time'>".$total_time." X 10<sup>-5</sup>Sec.</div>\n\t<div class='profiler_time'>".$total_time." X 10<sup>-5</sup>Sec.</div>\n\t</strong>\n</div>";
          }
          return $total_time;
      }
      /**
      * Returns the time difference from its previous call in microseconds.
      * 
      */
      function exec_time(){
          static $m = null;
          if($m === null){
              $m = microtime(TRUE);
          }
          $t = round(microtime(TRUE) - $m, 5)*100000;
          $m = microtime(true);
          return $t;
      }
      /**
      * used to display the output of profiler. It prints an HTML report of profiler.
      * 
      */
      public function display(){
        // global $profiler;
          if(self::$record){
              $this->add('Displaying Profiler Output',null , TRUE);
              $d = self::$msg;
              if($d != ""){
                  $a = '<style> .profiler_row{ float:left; padding:3px 0; width:100%; border-bottom:1px dotted #ccc; font-size:11px;} .profiler_msg{ float:left; width: 250px; } .profiler_sno{width:40px; float:left;} .profiler_time{text-align:center; float:left; width:175px; } .red{color: red;} .green{color: green;}</style>'."\n";
                  $a .= "<div style='width: 650px; float:left; padding:5px; border: 1px dashed #999;'>\n<div class='profiler_row'>\n\t<strong>\n\t\t<div class='profiler_sno'>S.No</div>\n\t\t<div class='profiler_msg'>Message</div>\n\t<div class='profiler_time'>Step Time(10<sup>-5</sup>Sec.)</div>\n\t<div class='profiler_time'>Execution Time(10<sup>-5</sup>Sec.)</div>\n\t</strong>\n</div>";
                  $a .= $d;
                  $a .= '</div>';
                  self::$msg='';//to clear the previous output
                  return $a;
              }
          }
      }
      /**
      * The destructor is called after the whole php page is executed from top to bottom.
      * This will display the message stored if the constructor of this php class is passed the value true at the time of object instantiation
      * 
      */
      public function __destruct(){
          echo $this->display();
      }
  }
  
  /**
  * ####################################################
  * Foot Notes: Testing of profiler class - 21 Jan, 2012
  * ####################################################
  * 
  * On a test run there was a simple page which loads all the libraries then query to database to show tables and then insert a row in sample table.
  * this whole process took a total time of 0.00651 in which details are as follows:
  * - Library loading:          0.00178 s
  * - initiating profiler       0.00005 s
  * - query-SHOW TABLES         0.00234 s
  * - query-INSERT              0.00231 s
  * - diplaying output          0.00003 s
  * 
  * THE ABOVE TIME WAS CALCULATED USING > echo "1.".microtime()."<br>"; < AFTER EVERY LINE OF CODE.
  * 
  * While the profiler shows 5sec less time than above. This may be excluding the time to initialize the profiler. Also library loading time is excluded.
  */
?>
