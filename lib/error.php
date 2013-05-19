<?php
/**
*   Class & File Name: error.php
*   Date: 28-08-2012
*   Company: Coravity Infotech
*   Developer: Sudhanshu Mishra
*   Description: error class handles the error generated in classes and are used by trigger in the error generating classes
*                to pass it to the error class and store ther error id, error no., error file, error msg, time and date of
*                the occurence of the error. 
*/

class error{                                              
    
    /**
    * function report() takes five parameters as an argument and is called automatically whenever error is triggered
    *
    * @param mixed $e_number : Error Number
    * @param mixed $e_message : Error Message
    * @param mixed $e_file : Name of File Causing Error 
    * @param mixed $e_line : Line Number of error in the file causing error
    * @param mixed $e_vars : Holds value of vars as an array
    * @var $e_id : Holds unique id (32 characters long) of the error
    */
    function report($e_number='', $e_message='', $e_file='', $e_line='', $e_vars=''){
        $e_id= uniqid();
        $values= array(
            'error_id'   => $e_id,
            'error_no'   => $e_number,
            'error_file' => $e_file,
            'error_line' => $e_line,
            'error_msg'  => $e_message,
            'error_vars' => print_r($e_vars, TRUE)
        );
        /**
        * this call is made to save errors into database table or if it is not present the values will be written into a file
        */
        $this->dbstore($values);
    }
    /**
    * info: function dbstore() is used to store the errors in the database table errorlog and if teh table is not present or incase of failed insertion the values will be       *       inserted into a file.
    * @param mixed $vals : to store array values passed as an argument from function report()
    */
    private function dbstore($vals=values){
        global $profiler, $db;
        $q=false;
         /**
         * @var $tblname : It stores the name of table in which the error entries will be made
         */
         $tblname='errorlog';
         if($db->checktable($tblname)){
         $q=$db->insert($tblname, $vals);
         }
         if($q){
            $profiler->add('Error Stored in database table['.$tblname.']');
            return TRUE;
         }
         else{
               $profiler->add('Failed to store error in database table['.$tblname.']');
               return $this->filestore($vals);
         }
    }
    /**
    * info: function filestore() is called inside or from function dbstore() in case table errorlog is not found or in case the values are not inserted into database table.
    *       This function checks for the @var $fileaddress if present it inserts the error @var $vals or values and if file not found it creates and then inserts.
    * 
    * @param mixed $val: array of error values
    */
    private function filestore($val=vals){
              global $profiler;
              $filedir='.'.'\\'.'logs';
              $filename='errorlogs.txt';
              $fileaddress=$filedir.'\\'.$filename;
            
              if(!file_exists($filedir)) //part A
              {
                    mkdir($filedir);//directory created
              }
              if(!file_exists($fileaddress)){
                  $fhandle= fopen($fileaddress,'a');
                  fclose($fhandle);
              }
                  // Let's make sure the file exists and is writable first.
              if (is_writable($fileaddress)) {
               $profiler->add('Now Storing the error in File['.$fileaddress.']');
                      // The file pointer is at the bottom of the file hence
                      // that's where $filecontent will go when we fwrite() it.
                        if (!$handle = fopen($fileaddress, 'a')) {
                        echo "Cannot open file ($fileaddress)";
                        return FALSE;
                        }
                  
                        $val['added']=time();
                         // Write $filecontent which is of array type and stored in $vals to our opened file.
                         if (fwrite($handle, json_encode($val)."\r\n") === FALSE) {
                        // echo "Cannot write to file ($fileaddress)";
                         }
                         fclose($handle);
                         $profiler->add('Error Stored in File['.$fileaddress.']');
                         return TRUE;
                  } 
                   $profiler->add('File ['.$fileaddress.'] not found.');
        return FALSE;
    }
    /**
    * info: function mail() used to send errors via mail
    * 
    */
    function mail(){  
        //todo -o abhishek -c base error class: mails are not being sent from error class needs to be completed.
    }   
}

?>