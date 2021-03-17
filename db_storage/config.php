<?php
class dbConfig {
    protected $serverName;
    protected $userName;
    protected $password;
    protected $dbName;
    protected $serverName_bkp;
    protected $userName_bkp;
    protected $password_bkp;
    protected $dbName_bkp;
    function dbConfig() {
        $this -> serverName = 'localhost';
        $this -> userName = 'appl_user';
        $this -> password = "redhat";
        $this -> dbName = "emp_db";
        $this -> serverName_bkp = 'localhost';
        $this -> userName_bkp = 'appl_user';
        $this -> password_bkp = "redhat";
        $this -> dbName_bkp = "emp_db_bkp";
    
    }
}
?>
