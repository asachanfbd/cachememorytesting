<?php
/**
*   Class & File Name: db.php
*   Date: 25-08-2012
*   Company: Coravity Infotech
*   Developer: Sudhanshu Mishra
*   Description: db class handles all requests for database connection, creation and checking of tables existence in database, insertion and search 
*               of diff. queries. 
*/
  class db {
      private $host;
      private $uname;
      private $pass;
      private $dbname;
      
      /**
      * The constructor accepts the following parameters as arguments passed (from) at the time of object instantiation or creation
      * of class db. Through the constrcutor received set of parameters the field values or class properties are initialzed. 
      * After initialization, the call to dbconnect() method or function is to make an active connecction with the database.
      * 
      * @param string $dbname: It stores the value of database name
      * @param string $host: It stores the value of hostname, the default value is localhost.
      * @param string $uname: It stores the value of user name, the default value is root.
      * @param string $pass: It stores the value of password, the default value is null or empty string.
      * 
      */
      function __construct($dbname,$host='localhost', $uname='root', $pass=''){
               $this->host=$host;
               $this->uname=$uname;
               $this->pass=$pass;
               $this->dbname=$dbname;
               //$this->dbconnect();
               //no need to enable the above code as dbconnect() is called internally by every function
        }
      
      /**
        * info: This function makes connection with the database and return the lock
        * 
        */
      private function dbconnect(){
          static $sqlconn = FALSE; 
          global $profiler;
            //$profiler->add('Called dbconnect()');
            if(!$sqlconn){
                $profiler->add('dbconnect() :: connection to mysqli()');
                $sqlconn = new mysqli($this->host, $this->uname, $this->pass, $this->dbname);
                $profiler->add('dbconnect() :: connection to mysqli(): Successful');
            }
            //$profiler->add('Returned from dbconnect()');
            return $sqlconn;
        }
      
      /**
        * info: This function inserts the values received into the provided table name. 
        *       First it checks for the table name by calling function checktable() and if table is found it inserts the values by making an insert query to querydb().
        * 
        * @param mixed $table_name: name of table
        * @param mixed $values: array of values
        */
      function insert($table_name, $values){
            global $profiler;
            $a = $profiler->add('Called insert() : table['.$table_name.']');
            if($this->checktable($table_name)){
                $col = '(';
                $val = '(';
                foreach($values as $k=>$v){
                  $col .= '`'.$k.'`, ';
                  $val .= "'".addslashes($v)."', ";
                }
                $col .= '`added`, `modified`)';
                $val .= "'".time()."', '".time()."')";
                $query = 'INSERT INTO '.$table_name.' '.$col.' VALUES '.$val;
                $r = $this->querydb($query);
                if($r){ 
                    $profiler->add('Returned from insert(): Insert Operation Successful', $a); 
                }
                else{ 
                    $profiler->add('Returned from insert(): Insert Operation Failed!', $a); 
                }
                return $r;
            }else{
                trigger_error('Cannot Insert into Table : "'.$table_name.'" does not exists.');
                return FALSE;
            }
        }      
      
      /**
        * info: this function creates a table if not present and if it is present then removes the previous values and insert the new values.
        * 
        * @param mixed $table_name: table name to be replaced/created or into which the values are to be inserted
        * @param mixed $values: array of values
        */
      function replace($table_name, $values){
            global $profiler;
            $a = $profiler->add('Called replace() values on : table['.$table_name.']');
            if($this->checktable($table_name)){
                $col = '(';
                $val = '(';
                foreach($values as $k=>$v){
                  $col .= '`'.$k.'`, ';
                  $val .= "'".addslashes($v)."', ";
                }
                $col .= '`added`, `modified`)';
                $val .= "'".time()."', '".time()."')";
                $query = 'REPLACE INTO '.$table_name.' '.$col.' VALUES '.$val;
                $r = $this->querydb($query);
                if($r){ 
                    $profiler->add('Returned from replace(): Operation Successful', $a); 
                }
                else{ 
                    $profiler->add('Returned from insert(): Insert Operation Failed!', $a); 
                }
                return $r;
            }else{
                trigger_error('Cannot Insert into Table : "'.$table_name.'" does not exists.');
                return FALSE;
            }
        }      
      
      /**
        * info: this function is used to make update to a particular column or rows based upon certain conditions
        * 
        * @param mixed $table: name of table
        * @param mixed $values: array of values
        * @param mixed $condition: condition of update
        */
      function update($table, $values, $condition){
            global $profiler;
            $a = $profiler->add('Called update() : table['.$table.']');
          if($this->checktable($table)){
            $val = '';
            foreach($values as $k=>$v){
                $val .= "`".$k."`='".addslashes($v)."', ";
            }
            $val .= "modified='".time()."'";
            $query = 'UPDATE '.$table.' SET '.$val.' WHERE '.$condition;
            return $this->querydb($query);
            $profiler->add('Returned from Update() : Update Completed', $a);
          }else{
              trigger_error('Returned from Update : Table "'.$table.'" does not exists.');
              return FALSE;
          }
        }
        
      /**
        * info: to check whether a particular table is present in the database or not if not it tries to create the table.
        * 
        * @param mixed $table: name of table.
        */
      function checktable($table = ''){
            global $profiler;
            $a = $profiler->add('Called checktable() : table['.$table.']');
            $result = $this->querydb("SHOW TABLEs LIKE '".$table."'");
            if($result->num_rows){
              $profiler->add('Returned from checktable() : Table Found', $a);
              return TRUE;
            }else{
              $profiler->add('Returned from checktable() : Table Not Found', $a);
              //return FALSE;
              return $this->createtable($table);
              
            }
        }
        
      /**
        * info: it deletes a record from table based upon certain condition
        * 
        * @param mixed $table: name of table
        * @param mixed $condition: delete condition
        */
      function delete($table, $condition){
            global $profiler;
            $a=$profiler->add('Called delete() : table['.$table.']');
            $result = $this->querydb("DELETE FROM ".$table." WHERE ".$condition);
            $profiler->add('Returned from delete() : Table deleted', $a);
            return true;
        }
        
      /**
        * info: it makes query to the database for tables
        * 
        * @param mixed $query: mysql query
        * @param mixed $returnObject: by default it is false. if it is true it returns array of arrays i.e more the one row or in case of false it return only one row.
        */
      function querydb($query, $returnObject = FALSE){
            global $error,$profiler;
            $a = $profiler->add('Called querydb() : query['.$query.']');
            $sql = $this->dbconnect();
            $result = $sql->query($query);
            if(!$result){
                $profiler->add('Returned from querydb() : Query Failed reporting error', $a);
                trigger_error('Problem in query: "'.$query.'"');
                return FALSE;
            }
            $profiler->add('Returned from querydb() : Query Completed', $a);
            if($returnObject){
                if($result->num_rows){
                    return $result->fetch_object();                    
                }else{
                    return FALSE;
                }

            }else{
              return $result;
            }
        }
        
      /**
        * info: this function is used to create tables by searching for its structure in mysql.php where an array of tables with structure is stored.
        *       if the @var $tblname i.e. name of table is found the query is fired.
        * @var $tblname: name of table to be created
        */
      function createtable($tblname){
            global $error,$profiler, $array_of_tables;
            //static $i = 0;
            $a = $profiler->add('Called createtable() : table['.$tblname.']'); 
            foreach($array_of_tables as $key=>$createtablequery){
                    if($key==$tblname)
                    {
                        $var=$this->querydb($createtablequery);
                          if($var){
                            //print_r($var);
                            $profiler->add('Returned from createtable() : Table created['.$tblname.']');
                            return TRUE;
                            }
                          else{
                            //print_r($var);
                            $profiler->add('Returned from createtable() : Error in table creation['.$tblname.']');
                            return FALSE;
                            }
                    }
            }
            $profiler->add('Returned from creatable() table : '.$tblname.' Not found in mysql.php');
            return FALSE;
        }
        
  }
?>
