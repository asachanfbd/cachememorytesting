<?php
  /**
  * this file provides html only.
  * 
  * it gets the data in predefined format and gives output in html.
  */
  
  class view{
      /**
      * this function creates a page structure
      * position for navigation, body, footer, help tips, CSS style, JS/JQ scripts and error are predefined in structure.
      * 
      */
      private static $ajax = FALSE;
      
      
      private static $page = '';
      
      /**
      * CodeLib Back-end Row creator in the box.
      * this function creates a row which on click can expand. It is assumed that required JQ is present in main file.
      * 
      * @param mixed $page : page in which this row would be used. this helps in loading content.
      * @param mixed $id   : id of the page to be loaded on click.
      * @param mixed $heading : Heading of the row.
      * @param mixed $content : A small description to be shown with each row
      * @param mixed $action : Action text to be written in row.
      * @param mixed $status : status of the row.. read/unread.
      */
      function getcmsrow($page, $id, $heading, $content, $action = 'Edit', $status = 'read'){
          return '
                    <li class="'.$status.'" id="'.$id.'">
                        <a href="listner.php?page='.$page.'&pagename='.$id.'" class="ajaxify">
                            <div class="tabrow">
                                <div style="overflow:auto">
                                    <div class="tabrowheader headingplain">'.$heading.'</div>
                                    
                                    <div class="tabrowactionbox">
                                        <div class="successmsg"><span>Changes Saved</span></div>
                                        <div class="tabrowactionbutton">
                                            '.$action.'
                                        </div>
                                        <div class="loadingico">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="tabrowcontent">'.$content.'</div>
                            </div>
                        </a>
                        <div class="ajaxresult">
                            <div class="tabrow">
                                <div style="overflow:auto">
                                    <div class="tabrowheader headingplain">'.$heading.'</div>
                                </div>
                                <div class="tabrowcontentajaxresult">Loading data...</div>
                            </div>
                        </div>
                    </li>';
      }
      
      function cmsrow_innerrow($page, $id, $heading, $content, $rollno){
          return '<li id="'.$id.'" style="background: #fff; border:1px solid #eee; list-style-type:none; margin:5px; overflow:auto; color: #666; float:left; width: 200px;">
                        <a href="listner.php?page='.$page.'&pagename='.$id.'" class="left studentinfo" style="display:block; width: 100%;overflow:auto;">
                            <div class="pad5 left" style="background: #eee;">'.$rollno.'.</div>
                            <div class="pad5 left">'.$heading.'</div>
                            <div style="overflow: auto; border-top:1px solid #eee; clear:both; background: url(\'images/father_ico1.jpg\') no-repeat 102px 6px;">
                                <div class="pad5 left">'.$content.'</div>
                            </div>
                        </a>
                    </li>
                ';
      }
      
      /**
      * CodeLib Back-end box creator.
      * this would create a box with heading, body and Footer
      * 
      * @param mixed $title : heading of the box to be created (Plain Text)
      * @param mixed $body : content of the box to be created  (HTML)
      * @param mixed $footer : Footer of the box (Plain Text)
      * @param mixed $add : Array : text and link to be created at top right of title.
      */
      function getcmsbox($class1='',$title, $body, $footer="", $add = "", $breadcrumbs = ""){
          $addnew = '';
          $class = '';
          
          if(is_array($add)){
              foreach($add as $k=>$v){
                  $addnew .= '<a class="cmsbox_addbut" href="'.$v.'" style="float:right; font-size:17px; margin-left:8px;">'.$k.'</a>';
              }
          }
          else{
              $class = $add;
          }
          if($breadcrumbs != ""){
              $breadcrumbs = '<div class="breadcrumbs">'.$breadcrumbs.'</div>';
          }
          $frame = '
           
                <div class="innerbox'.$class1.$class.'">
                <div class="heading">
                        '.$title.'
                        '.$breadcrumbs.'
                        '.$addnew.'
                    </div>
                <div>
                    
                    <ul class="tabdata">
                        ';
          if(is_array($body)){
              foreach($body as $v){
                  $frame .= $v;
              }
          }else{
              $frame .= $body;
          }
          $frame .= '
                    </ul>';
          if($footer != ""){
              $frame .= '<div style="padding: 1px 6px; border-top:1px solid #ddd; font-size: 12px; display:none;">
                        '.$footer.'
                    </div>';
          }
          $frame .=  '
                        <div class="innerboxloading"></div>
                    </div>
                    
                </div>';
          return $frame;
      }
      
      function getformfields($label, $type, $name, $help = "abc", $value = "", $placeholder = "", $onclick=""){
          $c1 = '<label for="'.$name.'">'.$label.'</label>';
          $c2 = ':';
          if($type == 'text' || $type == 'password'){
              if($name=='mobile'){
              $c3 = '<input type="'.$type.'" class="clview_input showhelp" name="'.$name.'" id="'.$name.'" value="'.$value.'" maxlength="10" placeholder="'.$placeholder.'" >';   
              }else{
              $c3 = '<input type="'.$type.'" class="clview_input showhelp" name="'.$name.'" id="'.$name.'" value="'.$value.'" placeholder="'.$placeholder.'" >';
              }
          }
          elseif($type == 'select'){
              $c3 = '<select name="'.$name.'"  class="clview_input">';
              foreach($value as $k => $v){
                  $c3 .= '<option ';
                  if($k==1){
                      $c3 .= 'selected="selected"';
                  }
                  $c3 .= ' value="'.$k.'">'.$v.'</option>';
              }
              $c3 .= '</select>';
          }
          elseif($type == 'submit'){
              $c1 = '';
              $c2 = '';
              $c3 = '<input type="submit" class="button" value="'.$value.'" name="'.$name.'" id="'.$name.'">';
              $help = 'Click to Submit';
          }elseif($type == 'textarea'){
              $c3 = '<textarea class="clview_input showhelp" name="'.$name.'" id="'.$name.'">'.$value.'</textarea>';
          }
          elseif($type == 'button' && $name=='cancel'){
              $c3 ='<input type="button" class="button" name="'.$name.'" value="'.$value.'" onclick="'.$onclick.'">';
          }
          else{
              $c3 = 'Input Type not defined.';
          }
          
          if($label == ''){
              $c2 = '';
          }
          
          return $this->getformrows($c1, $c2, $c3, $help);
      }
      
      function getformrows($c1 = '&nbsp;', $c2 = '&nbsp;', $c3 = '&nbsp;', $chelp = '&nbsp;'){
          $re = '
            <div class="clview_row">
                <div  class="clview_row_in">
                    <div class="clview_row_lbl">'.$c1.'</div>
                    <div class="left pad5">'.$c2.'</div>
                    <div class="left pad5" style="width:70%;">'.$c3.'</div>
                </div>
                <div class="clview_help">'.$chelp.'</div>
            </div>';
            return $re;
      }
      
      function getform($page, $type, $data){
          $re = '
          <form action="controller.php" method="post" class="ajaxsubmitform">
                <input type="hidden" value="'.$page.'" name="page" id="page">
                <input type="hidden" value="'.$type.'" name="type" id="type">
                <input type="hidden" value="" name="ajaxrequest" id="ajaxrequest">
                '.$data.'
          </form>';
          return $re;
      }
      
     /* function getnavtree($name, $curpage = ''){
            global $db;
            global  $contentpages;
            $re1=$db->querydb("SELECT * FROM page_tree WHERE parent='".$name."' ORDER BY priority, name");
            $list = '';
            $class='';
            if($re1->num_rows){
                while($ro1=$re1->fetch_object()){
                    $q = "SELECT title FROM pages WHERE name='".$ro1->name."' ORDER BY added DESC";
                    $r = $db->querydb($q, true);
                    $sublist = $this->getnavtree($ro1->name, $curpage);
                    if($sublist != ""){
                        $span = "<span></span>";
                        $link.= "#";
                    }else{
                        $span = "";
                        $link = "?page=".$ro1->name;
                    }
                    $list .= '<li><a ';
                    if($curpage == $ro1->name){
                        $list .= 'class="selected" ';
                    }
                     if($contentpages->getparent($curpage) == $ro1->name){
                         $class="class='selected'";
                    } 
                    $list .= 'href="'.$link.'" '.$class.'>'.$r->title.$span.'</a>
                      '.$sublist.'
                      </li>';
                }
            }
            if($list == ""){
                return "";
            }
            return "<ul>".$list."</ul>";
      }
      */
    /* function getnav($pages, $type, $pageid){
          $r = '<ul class="'.$type.'">';
          foreach($pages as $k => $v){
              $r .= '<li><a href="?page='.$k.'&type='.$type.'" ';
              if($k == $pageid){
                  $r .= 'class="selected" ';
              }
              $r .= '>'.$v.'</a></li>';
          }
          $r .= '</ul>';
          return $r;
      }  */
      
       function getnav($name, $curpage = ''){
            global $db;
            global  $contentpages;
            $re1=$db->querydb("SELECT * FROM page_tree WHERE parent='".$name."' ORDER BY priority, name");
            $list = '';
            $link='';
            $class='';
            $flag=0;
            if($re1->num_rows){
                while($ro1=$re1->fetch_object()){
                    $q = "SELECT title FROM pages WHERE name='".$ro1->name."' ORDER BY added DESC";
                    $r = $db->querydb($q, true);
                    $sublist = $this->getnav($ro1->name, $curpage);
                    if($sublist != ""){
                        if($flag==0){
                        $span = "<span></span>";
                        $link= "#";
                        $flag++;
                        }
                    }else{
                        $span = "";
                        $link= "?page=".$ro1->name;
                        
                    }
                    $list .= '<li><a ';
                    if(isset($_REQUEST['page'])){
                        $rootp='';
                        $rootpage=$db->querydb("SELECT parent AS p FROM page_tree WHERE name='".$_REQUEST['page']."'", true);
                        if($rootpage){
                            $rootp=$rootpage->p;
                        }
                        
                        if($rootp == $ro1->name){
                            $list .= 'class="selected" ';
                        }
                    }
                    if($curpage == $ro1->name){
                        $list .= 'class="selected" ';
                    }
                    $list .= 'href="'.$link.'" >'.$r->title.$span.'</a>
                      '.$sublist.'
                      </li>';
 }
            }
            if($list == ""){
                return "";
            }
            return "<ul>".$list."</ul>";
            
      }
  /*  function getsubnav($pages, $type, $pageid, $subpage){
          global $user;
          $r = '<ul class="'.$type.'"  id="leftsubnav">';
          $p = '';
          foreach($pages as $k => $v){
              $r .= '<li><a href="?page='.$pageid.'&type='.$type.'&subpage='.$k.'" ';
              if($k == $subpage){
                  $r .= 'class="selected" ';
              }
              $r .= '>'.$v.'</a></li>';
          }
          $r .= '</ul>';
          return $r;
      }  */
      
      function htmlframe($data, $page = ''){
          global $user;
          if(!$user->iflogin()){
              if($page == 'home'){
                  $theme = 'home.php';
              }else{
                  $theme = 'internal.php';
              }
          }else{
              $theme = 'loginpage.php';
          }
          ob_start();
          require_once('view/'.$theme);
          $d = ob_get_contents();
          ob_end_clean();
          foreach($data as $k => $v){
              while(substr_count($d, '[#:@`'.$k.'`]') > 0){
                  $d = str_replace('[#:@`'.$k.'`]', $v, $d);
              }
          }
          return $d;
      }
      
      function getbody($name){
          global $contentpages;
          //TODO: On login error needs to be handled.
            $a = $contentpages->getcontent($name);
            
          return $a;
      }
      
      function getimage($name){
          global $contentpages;
            $a = $contentpages->getimage($name);
          return $a;
          
      }
      
      function bodyinnerframe($nav, $body, $page, $subpage, $st_id = ''){
          return '<a name="loggedin"></a>
            <div style="overflow:auto; border-top:1px solid #666; margin:10px 0;">
                <div style="overflow:auto; min-height: 200px; border-left: 1px solid #aaa;">'.$body.'</div>
            </div>
          ';
      }
      
      function afterlogininfoheader($stid = ''){
          global $user, $parents_obj, $student, $db;
          $d = '';
          if($user->getusertype() == 'parent'){
              
              $st_id = $parents_obj->getstudentid($user->getid());
              if(is_array($st_id)){
                  $d .= '<ul class="student_nav">';
                  foreach($st_id as $v){
                      $d .= '<li><a href="?studentid='.$v.'" ';
                      if($v == $stid){
                          $d .= 'class="selected" ';
                      }
                      $d .= '>'.$student->getname($v).'';
                      if($v == $stid){
                          //$d .= 'born on <strong>'.date('M d, Y', $student->getdob($stid)).'</strong>';   
                      }
                      $d .= '</a></li>';
                  }
                  $d .= '</ul>';
              }
              
          }
          return $d;
      }
      
      function getloginbar(){
          global $user, $notify;
          if($user->iflogin()){
              $acc_type = 'Admin Account';
              $name = '<span style="font-size:15px; font-weight:bold;">'.$user->getfullname().'</span>';
              $settings = '<a href="?page=homepage&type=subnav&subpage=profiles">Settings</a>';
              $logout = '<a href=?logout>Logout</a>';
              $d = '
              <section id="logininfobar">
                        <div style="font-size:18px; font-weight:lighter; float:left;padding:3px;">'.$acc_type.'&nbsp;|&nbsp;'.$name.'</div>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li>'.$settings.'</li>
                            <li>'.$logout.'</li>
                        </ul>
                    </section>
                    ';
              return $d;
          }
      }
      
      function createresulttbl($examid){
          global $db, $exam_obj;
          $tbl = '';
          $rows = array();
          $re = $db->querydb("SELECT * FROM  bt_inst_exam WHERE exam_id = '".$examid."' AND date_of_exam < ".time());
          if($re->num_rows){
              $rows['subject_name'] = '<tr style="background:#eee;"><td>Subject Name</td>';
              $rows['marks_max'] = '<tr style="background:#eee;"><td>Max. Marks</td>';
              $rows['grades'] = '<tr style="background:#eee;"><td>Grades</td>';
              $rows['marks_passing'] = '<tr><td>Passing Marks</td>';
              $subject_name = array();
              $subject_id = array();
              $marks_max = array();
              $marks_pass = array();
              $j = 0;
              // getting all the subjects
              while($ro = $re->fetch_object()){
                  $subject_id[] = $ro->exam_sub_id;
                  $marks_max[] = $ro->marks_max;
                  $marks_pass[] = $ro->marks_passing;
                  $rows['subject_name'] .= '<td class="rotate">'.$ro->subject_name.'<input type="hidden" id="sub'.$j.'" name="sub'.$j.'" value="'.$subject_id[$j].'"></td>';
                  $rows['marks_max'] .= '<td class="center">
                                            '.$ro->marks_max.'
                                            <input type="hidden" id="max'.$ro->exam_sub_id.'" name="max'.$ro->exam_sub_id.'" value="'.$ro->marks_max.'">
                                         </td>';
                  $rows['marks_passing'] .= '<td class="center">'.$ro->marks_passing.'</td>';
                  $tgrade = '';
                  if($ro->grades == 'grade'){
                      $tgrade = ' checked="checked"';
                  }
                  $rows['grades'] .= '<td class="center"><input type="checkbox" name="grade'.$ro->exam_sub_id.'" id="grade'.$ro->exam_sub_id.'" '.$tgrade.'></td>';
                  $inst_id = $ro->inst_id;
                  $class = $ro->class;
                  $j++;
              }
              $rows['subject_name'] .= '</tr>';
              $rows['marks_max'] .= '</tr>';
              $rows['marks_passing'] .= '</tr>';
              $rows['grades'] .= '</tr>';
              //getting all student's marks
              $r = $db->querydb("SELECT * FROM bt_st_info WHERE inst_id = '".$inst_id."' && class = '".$class."'");
              $j = 0;
              
              if($r->num_rows){
                  $tbl .= '<table class="roundedcornertable">';
                  $tbl .= $rows['subject_name'].$rows['marks_max'].$rows['grades'].$rows['marks_passing'];
                  while($ro = $r->fetch_object()){
                      $tr = '<tr>';
                      $tr .= '<td>'.$ro->name.'<input type="hidden" id="st'.$j.'" name="st'.$j.'" value="'.$ro->id.'"></td>';
                      for($i = 0; $i<count($subject_id); $i++){
                          
                          $tr .= '<td><input size="3" value="'.$exam_obj->ifresultexists($examid, $ro->id, $subject_id[$i]).'" class="inputresultbox" type="number" data-pass="'.$marks_pass[$i].'" data-min="0" data-max="'.$marks_max[$i].'" id="'.$ro->id.'_'.$subject_id[$i].'" name="'.$ro->id.'_'.$subject_id[$i].'"></td>';
                          //DONE: develop a JS to detect the failed student status and change the field background to red and also the range if exceeded then stop.
                      }
                      $tr .= '</tr>';
                      $tbl .= $tr;
                      $j++;
                  }
                  
                  $tbl .= '<tr class="actionbar"><td colspan="'.($i+1).'">';
                  $tbl .= '<input type="hidden" name="exam_id" id="exam_id" value="'.$examid.'">';
                  $tbl .= '<div class="right"><input type="submit" value="Save"><input type="reset"></div></td></tr>';
                  $tbl .= '</table>';
                  //showing declare button only if result completed but not declared.
                  if($exam_obj->checkresults($examid) && !$exam_obj->ifresultdeclared($examid)){
                      $tbl .= '<input type="button" class="button declareresult" id="'.$examid.'" value="Declare Result">';
                  }
                  $result = $this->getform('results', 'addresults', $tbl);
              }
          }
          if(!isset($result)){
              return FALSE;
          }
          return "<div style='float:left;'>".$result."</div><div style='float:left;'><pre>
          Use Grades As follows:
            97 => 'A1',
            86 => 'A2',
            81 => 'B1',
            71 => 'B2',
            66 => 'C1',
            56 => 'C2',
            52 => 'D',
            51 => 'D1',
            41 => 'D2',
            37 => 'E',
            29 => 'E1',
            17 => 'E2'
          </pre></div>";
      }
      
      function piechart($marks){
          $grade = array(
            'ap'    =>0,
            'a'     =>0,
            'b'     =>0,
            'c'     =>0,
            'd'     =>0
          );
          foreach($marks as $v){
              $v = round($v);
              if($v >= 90){
                  $grade['ap']++;
              }elseif($v >= 75 && $v < 90){
                  $grade['a']++;
              }elseif($v >= 55 && $v < 75){
                  $grade['b']++;
              }elseif($v >= 35 && $v < 55){
                  $grade['c']++;
              }else{
                  $grade['d']++;
              }
          }
          $id = uniqid();
          $js = '
              function fn'.$id.'(){
                var data = new google.visualization.DataTable();
                data.addColumn("string", \'Topping\');
                data.addColumn(\'number\', \'Slices\');
                data.addRows([
                  [\'Grade A+ (Above 90%)\', '.$grade['ap'].'],
                  [\'Grade A (75% - 90%)\', '.$grade['a'].'],
                  [\'Grade B (55% - 75%)\', '.$grade['b'].'],
                  [\'Grade C (35% - 55%)\', '.$grade['c'].'],
                  [\'Grade D (35% and Below)\', '.$grade['d'].']
                ]);

                var options = {
                  title: "Grades achieved in This session"
                };

                var chart = new google.visualization.PieChart(document.getElementById("'.$id.'"));
                chart.draw(data, options);
              }
              ';
          $this->makegraphs('fn'.$id, $js);
          return '
          <div class="grid1">
              <div id="'.$id.'" class="grid2"></div>
              <div class="grid2 graphcontent">
                <div>
                    <h3>Grades Achieved in this session</h3>
                    <p>This pie chart represents in term of percentage that what grades are more consistent in performance of the student.</p>
                    <p>It is divided into sections of different Grades. Each grade is represented with a specific colour.</p>
                    <p>If the area of a colour is more that means the student is consistently getting that grade in exams.</p>
                </div>
              </div>
          </div>';
      }
      
      function makexygraph($max, $current, $min, $examname, $grapname = ''){
          $id = uniqid();
          static $position = 'right';
          $js = '
              function fn'.$id.'(){
                // Create and populate the data table.
                  var data = google.visualization.arrayToDataTable([
                    [\'x\', \'Maximinum Marks Scored\', \'Marks Scored by Your Child\', \'Minimum Marks Scored\'],';
          if(count($max) == count($min) && count($min)  == count($current)){
              foreach($current as $k => $v){
                  $js .= '
                  ["'.$examname[$k].'", '.$max[$k].', '.$current[$k].', '.$min[$k].'],';
              }
          }
          
          echo '<script type="text/javascript">alert("'.$js.'");</script>';
          
          if($grapname == ''){
              $grapname = 'Student Performance Graph';
              $details = '<p>This graph represents the performance of the students according to the marks achieved by him in respective exams.</p>
                        <p>It represents the three types of marks. These marks are represented by the specific colours. </p>
                        <p><strong>Blue Line:</strong> Maximum marks scored in any exam.<br>
                        <strong>Orange:</strong> Minimum marks scored in any exam.<br>
                        <strong>Red:</strong> Marks obtained by your child in any exam.</p>';
          }else{
              $details = '<p>This graph gives performance of student in given exam.</p>
                        <p><strong>Blue Line:</strong> Maximum marks scored in any exam.<br>
                        <strong>Orange:</strong> Minimum marks scored in any exam.<br>
                        <strong>Red:</strong> Marks obtained by your child in any exam.</p>';
          }
          $js .= '
                  ]);

                  // Create and draw the visualization.
                  new google.visualization.LineChart(document.getElementById("'.$id.'")).
                      draw(data, {
                                      curveType: "function",
                                      title: "'.$grapname.'"
                                 }
                          );
              }';
          $this->makegraphs('fn'.$id, $js);
          //return '<div id="'.$id.'" class="grid2"></div>';
          if($position == 'left'){
              $position = 'right';
              return '
              <div class="grid1">
                  <div id="'.$id.'" class="grid2"></div>
                  <div class="grid2 graphcontent">
                    <div>
                        <h3>'.$grapname.'</h3>
                        '.$details.'
                    </div>
                  </div>
              </div>';
          }elseif($position == 'right'){
              $position = 'left';
              return '
              <div class="grid1">
                  <div class="grid2 graphcontent">
                    <div>
                        <h3>'.$grapname.'</h3>
                        '.$details.'
                    </div>
                  </div>
                  <div id="'.$id.'" class="grid2"></div>
              </div>';
          }
          
      }
      
      function makegraphs($add = '', $fn = ''){
          static $str2 = '';
          static $str4 = '';
          static $str1 = '
          <script type="text/javascript">
          
                      $(document).load(function(){
                            //drawallgraphs();
                      });
                      
                      function drawallgraphs(){
          ';
          static $str3 = '
                      }';
          static $str5 = '</script>';
          
          if($add != ''){
              $str2 .= $add.'();';
          }
          
          if($fn != ''){
              $str4 .= $fn.'
              ';
          }
          //
          $str6 = '
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
            <script type="text/javascript">
                    google.load("visualization", "1", {packages:["corechart"]});
                    google.setOnLoadCallback(drawallgraphs);
            </script>';
          if($add == '' && $str2 != ''){
              return $str1.$str2.$str3.$str4.$str5.$str6;
          }
      }
      
      function makesubjectgraph($subjects){
          $data = '[\'Exams\', \''.implode('\', \'', $subjects['subject_names']).'\']';
          foreach($subjects as $k => $v){
              if($k != 'subject_names'){
                  $data .= ',
                  [\''.$k.'\'';
                  foreach($subjects['subject_names'] as $v1){
                      if(!isset($subjects[$k][$v1])){
                          $subjects[$k][$v1] = 0;
                      }
                      $data .= ', '.$subjects[$k][$v1];
                  }
                  $data .= ']';
              }
          }
          $id = uniqid();
          $js = '
              function fn'.$id.'(){
                // Some raw data (not necessarily accurate)
                var data = google.visualization.arrayToDataTable([
                  '.$data.'
                ]);

                var ac = new google.visualization.ComboChart(document.getElementById("'.$id.'"));
                ac.draw(data, {
                    title : \'Subject Wise Performance\',
                    vAxis: {title: "Marks"},
                    hAxis: {title: "Exams and Subjects"},
                    seriesType: "bars",
                    series: {5: {type: "line"}}
                  });
                
              }
          ';
          $this->makegraphs('fn'.$id, $js);
          return '<div id="'.$id.'" class="grid1"></div>';
      }
      
      function getremarksbox($to, $for){
         global $user;
         $remarksform = $this->getformfields("Title", 'text', 'remarkstitle', 'Enter the title of you remarks - one liner brief');
         $remarksform .= '<input type="hidden" name="remarksfrom" id="remarksfrom" value="'.$user->getid().'">'."\n";
         $remarksform .= '<input type="hidden" name="remarksto" id="remarksto" value="'.$to.'">'."\n";
         $remarksform .= '<input type="hidden" name="remarksfor" id="remarksfor" value="'.$for.'">'."\n";
         $remarksform .= '<input type="hidden" name="remarkstype" id="remarkstype" value="remarks">'."\n";
         $remarksform .= $this->getformfields("Remark", 'textarea', 'remarksbody', 'Enter your message in detail.');
         $remarksform .= $this->getformfields('', 'submit', 'remarksubmit', '', 'Send');
         $remarksform = $this->getform('remarks', 'newremark', $remarksform);
         return $this->getcmsbox('Send Remarks to institution', $remarksform, '');
      }
      
      function getremarksreplybox($id, $to){
         global $user;
         $remarksform = '<input type="hidden" name="remarksid" id="remarksid" value="'.$id.'">'."\n";
         $remarksform .= '<input type="hidden" name="remarksto" id="remarksto" value="'.$to.'">'."\n";
         $remarksform .= '<input type="hidden" name="remarkstype" id="remarkstype" value="remarks">'."\n";
         $remarksform .= '<div class="remarksreplybox">
         <div class="left"><textarea id="remarksreplybody" name="remarksreplybody"></textarea></div>'."\n";
         $remarksform .= '
         <div class="left"><input type="submit" id="remarksubmit" name="remarksubmit" value="Reply"></div></div>'."\n";
         return $this->getform('remarks', 'replyremark', $remarksform);
         //return $this->getcmsbox('Send Remarks to institution', $remarksform, '');
      }
      
      function usernamechangebox(){
          global $user;
          $d = $this->getformfields('New Username', 'text', 'username', 'Put your email id here.');
          $d .= '
          <div class="clview_row">
              <div  class="clview_row_in">
                  <div class="clview_row_lbl">&nbsp;</div>
                  <div class="left pad5">&nbsp;</div>
                  <input type="hidden" value="'.$user->getid().'" name="userid" id="userid">
                  <div class="left pad5"><input type="submit" name="submit" id="submit" value="Save"></div>
              </div>
          </div>';
          $d = $this->getform('profiles', 'changeusername', $d);
          return $this->getcmsbox('Change Username', $d, 'You can set your email id as username. So that you can remember it easily.');
      }
      
        /**
      * This function will create a datatable with sorting searching and pagination method.
      * 
      * $data is an array as follows:
      *     $data = array(
      *                 array('Col Name 1', 'Col Name 2', 'Col Name 3'), 
      *                 array('data 1 1', 'data 1 2', 'data 1 3'),
      *                 array('data 2 1', 'data 2 2', 'data 2 3'),
      *                 .
      *                 .
      *                 .
      *                 array('data n 1', 'data n 2', 'data n 3')
      *             );
      *     Note: First element of array comtains coloumn names.
      * @param mixed $data
      */
      
      function createdatatable($data){
          static $styles = 1;
          $thead = '';
          $tbody = '';
          $flag = 1;
          foreach($data as $th){
              if($flag == 1){
                  $thead .= '<tr>';
              }else{
                  $tbody .= '<tr>';
              }
              foreach($th as $td){
                  if($flag == 1){
                      $thead .= '<th>'.$td.'</th>';
                  }else{
                      $tbody .= '<td>'.$td.'</td>';
                  }
                  
              }
              if($flag == 1){
                  $thead .= '</tr>';
                  $flag = 0;
              }else{
                  $tbody .= '</tr>';
              }
          }
          $d = '
                <div style="overflow:auto; padding: 10px;">
                    <table class="datatables display" width="100%">
                        <thead>'.$thead.'</thead>
                        <tbody>'.$tbody.'</tbody>
                        <tfoot>'.$thead.'</tfoot>
                    </table>
                </div>
          ';
          if($styles== 1){
              $d = '
            <style type="text/css" title="currentStyle">
                @import "js/media/css/demo_page.css";
                @import "js/media/css/demo_table_jui.css";
            </style>'.$d;
            $styles=0;
          }
          return $d;
      }
      
      function highlightsuccess($msg=''){
        return '<div style="font-size:14px; font-weight: bold" id="fadeout"><div class="ui-widget">
        <div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
        <strong>Success!</strong> '.$msg.'</p>
        </div>
        </div></div>';
      }
      function highlighterror($msg=''){
        return '<div style="font-size:14px; font-weight: bold" id="fadeout"><div class="ui-widget">
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
        <strong>Alert:</strong> '.$msg.'</p>
        </div>
        </div></div>';
      }
  }
?>