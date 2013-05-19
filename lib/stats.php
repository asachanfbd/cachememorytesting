<?php
/**
  * Class Name  :   stats.php
  * Date        :   15-09-2012
  * Company     :   coravity infotech
  * Developer   :   Sudhanshu Mishra
  * Description :   stats.php class provides information about the visiting user through the http/https (web browser). It has a constructor and some
  *                 functions that aim to have specfic functionalities/operations to perform. This class can be used to track the visitor details of a website or web pages.
  */
class stats{
/**
    * Info: The constructor is called when the object of class is instantiated. Here when the object of this class is constructed in any php class or page i.e. @ new stats()
    *       the constructor is called. It is called automatically at the time of object instantiation. The following actions are performed in this constructor:
    *       
    *       The first if condition checks whether the request is coming from HTTP_HOST e.g. a browser or not. If isset returns false then function refineBrowser() and 
    *       ip2location() is called. If isset($_SERVER['HTTP_HOST']) returns true then the control is passed inside to second if. Here if checks whether the requested 
    *       resource is running on web server or localhost. Further control is passed only if it is not the localhost else in case of localhost no action is performed 
    *       in this constructor.
    * @var global $db: $db contains the object reference of the db.php class.
    * @var global $profiler : this is basically used for testing or debugging purpose.
    * Cookie 'bid'   : bid is the name of the cookie with which the unique browser id is stored in the visitors browser.
    * Database Tables: 1. Table name 'raw_stats' columns - BROWSER_ID as primary key, HTTP_USER_AGENT, REMOTE_ADDRESS, HTTP_REFERER 
    *                  2. Table name 'refined_stats' columns - ID, HTTP_USER_AGENT, agent_type, agent_name, agent_version, os_name, browser_refined
    *      
    */
    function __construct(){
        global $profiler;
        $profiler->add('Constructor of stats.php called');
        if(isset($_SERVER["HTTP_HOST"])){
                if(stristr($_SERVER["HTTP_HOST"],'localhost')){
                    global $db;
                    //  $this->visitordb();
                    $flag=false;
                    if(isset($_COOKIE['bid'])){
                        if($db->checktable('rawstats')){    
                            $q = $db->querydb("SELECT * FROM rawstats WHERE browser_id='".$_COOKIE['bid']."'", true);
                        }
                        if($q){
                            $flag = false;
                        }else{
                            $flag=true;
                        }
                    }else{
                        $flag = true;
                    }
                    if($flag){
                        $values['browser_id'] = uniqid();
                        setcookie('bid',$values['browser_id'],time()+3600*24*365);
                        $values['http_user_agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
                        $values['remote_address'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                        $values['http_referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct';
                        $db->insert('rawstats', $values);
                        $value=array(
                            'id'               =>   md5($values['http_user_agent']),
                            'http_user_agent'  =>   $values['http_user_agent']
                        );
                        $db->replace('refinedstats', $value);
                    }
                    
                }
        }else{
            $this->refineBrowser();
            $this->ip2location();
        }
   }
/**
    * Info: function refineBrowser() is called from within the constructor of this(stats.php) class. when the first if condition inside the constructor results in false i.e
    *       if(isset($_SERVER["HTTP_HOST"]) returns false this function is called. This fuction is set to private so it can't be called from outside the class.
    * 
    *       The purpose of this function is to refine the data prsent in refined_stats table if browser_refined status is 0. The refined-stats table contains ID and 
    *       HTTP_USER_AGENT values which is inserted from within the constructor and default vaule if assigned to browser_refined which is 0. When this function is called 
    *       based upon the HTTP_USER_AGENT values the rest of the fileds are set. The values of columns such agent_type, agent_name, agent_version, os_name is manipulated  
    *       by making a request to the http://www.useragentstring.com/ website see line no. 82
    * 
    */
    private function refineBrowser(){
        global $profiler;
        global $db;
        $t = $profiler->add("Refining browser");
          $qr=$db->querydb('SELECT * FROM refinedstats WHERE browser_refined=0');
           if($qr->num_rows){
                while($ro=$qr->fetch_object()){ 
                    $profiler->add("Starting useragentstring connection");
                    $url = "http://www.useragentstring.com/?uas=".urlencode($ro->http_user_agent)."&getJSON=agent_type-agent_name-agent_version-os_name";  
                    if(($fp=fopen($url, 'rb'))!=null){
                       $json = stream_get_contents($fp);
                       fclose($fp);
                    } 
                       $profiler->add("UAS connection completed");
                    if($result = json_decode($json, true)){
                       $values['agent_type'] = isset($result['agent_type']) ? $result['agent_type'] : 'unavailable';
                       $values['agent_name'] = isset($result['agent_name']) ? $result['agent_name'] : 'unavailable';
                       $values['agent_version'] = isset($result['agent_version']) ? $result['agent_version'] : 'unavailable';
                       $values['os_name'] = isset($result['os_name']) ? $result['os_name'] : 'unavailable';
                       $values['browser_refined']=1;
                       $db->update('refined_stats', $values, 'id = "'.$ro->id.'"'); 
                       $profiler->add("Refined browser data", $t);
                    }
                }
          }else{
                     $profiler->add('Browser not refined : no row found for browser_refined=0');
                 }    
   }
/**
      * info: function ip2location() is called from the constructor if the first if results in false.
      *       This function converts and stores the various values fetched or derived based upon the visitors ip address into database table 'ip2location'.
      *       This function calls ipdb() with param $ip i.e. ip address is paased to ipdb() to get an array of values such as  name, region name, country name, country code,
      *       zip code, latitude, longitude, time zone, etc. of the visiting user.
      *       All the information got about the visitor is stored in the table 'ip2location'.  
      *  
      */
    private function ip2location(){
          global $profiler;
          global $db;
          $profiler->add("ip2location() called");
          if($db->checktable('ip2location')){
            $q=$db->querydb("SELECT * FROM rawstats");
            if($q->num_rows){
                while($ro=$q->fetch_object()){
                    $ip=$ro->REMOTE_ADDRESS;
                    $values=$this->ipdb($ip);
                    $values['browser_id']=$ro->browser_id;
                    $db->replace('ip2location', $values);
                }
            }
          $profiler->add("Returned from ip2location()");
          }
    }
/**
   * info:  function ipdb() takes ip address as a parameter and based upon this ip addres information is fetched about the visitor by making a request to the website http://
   *        api.ipinfodb.com which in returns provide the information like city name, region name, country name, country code, zip code, latitude, longitude, time zone, etc.   *        of the visiting user. All the above values are returned by this function to the calling function in the form of array.
   *        This function is called from ip2location()
   * @param mixed $ip : Hold value of IP address
   */
    private function ipdb($ip){
        global $profiler;
          $t = $profiler->add("Calling IP Info at ipinfodb");
          $url = 'http://api.ipinfodb.com/v3/ip-city/?key=0b6b8fd8799b4f47ecb7988c3d48bac070e65407ec0e80f8471c0e22b5887e59&ip='.$ip.'&format=json';
          if(($fp=fopen($url, 'rb'))!=null){
             $json = stream_get_contents($fp);
             fclose($fp);
          }
          $t = $profiler->add("ipinfodb returned now refining ip", $t);
          if($result = json_decode($json, true)){
              if(isset($result['cityName'])){ $values['city_name'] = $result['cityName'];}
              if(isset($result['regionName'])){ $values['region_name'] = $result['regionName'];}
              if(isset($result['countryName'])){ $values['country_name'] = $result['countryName'];}
              if(isset($result['countryCode'])){ $values['country_code'] = $result['countryCode'];}
              if(isset($result['zipCode'])){ $values['zip_code'] = $result['zipCode'];}
              if(isset($result['latitude'])){ $values['latitude'] = $result['latitude'];}
              if(isset($result['longitude'])){ $values['longitude'] = $result['longitude'];}
              if(isset($result['timeZone'])){ $values['time_zone'] = $result['timeZone'];}
          }
          
          return $values;
      }    
/**
    * info: function getcurrenturl() gets the complete url address of the browser making page request.
    *       This function is called from the function visitordb().
    */
    private function getcurrenturl(){
        $curpageURL = 'http';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
            $curpageURL.= "s";
        }
        $curpageURL.= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
        $curpageURL.= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } 
        else {
        $curpageURL.= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $curpageURL;
    }
/**
    * info: function visitordb() this function is called everytime the user requests a new page or do page refresh. It stores information about     * the visitors requests with each request havig a unique id. It is or can be called from every php page.
    * Table: Table name used 'visitordb' columns - BROWSER_ID, Visitor_ID as primary key, Visited_URL
    */
    function visitordb($bid, $reqid){
        global $profiler;
        global $db;
        $qr=$db->querydb("SELECT visitor_id as reqid FROM visitordb Where browser_id='".$bid."' && visitor_id = '".$reqid."'", true);
        if($qr){
            $values=array(
                'browser_id'    =>      $bid,
             );
             $db->update( 'visitordb', $values, "visitor_id='".$reqid."'");
        }else{
            $values=array(
            'browser_id'    =>      $bid,
            'visitor_id'     =>     $reqid,
            'visited_url'   =>      $this->getcurrenturl()
            );
            $db->insert('visitordb', $values);
        }
    }
}

?>