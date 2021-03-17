<?php
require('config.php');
class Employee extends Dbconfig {	
    protected $hostName;
    protected $userName;
    protected $password;
    protected $dbName;
    protected $hostName_bkp;
    protected $userName_bkp;
    protected $password_bkp;
    protected $dbNamei_bkp;
    private $empTable = 'employee';
    private $dbConnect = false;
    private $dbConnect_bkp = false;
    public function __construct(){
        if(!$this->dbConnect){ 		
	    $database = new dbConfig();            
            $this -> hostName = $database -> serverName;
            $this -> userName = $database -> userName;
            $this -> password = $database ->password;
	    $this -> dbName = $database -> dbName;			
            $this -> hostName_bkp = $database -> serverName_bkp;
            $this -> userName_bkp = $database -> userName_bkp;
            $this -> password_bkp = $database ->password_bkp;
	    $this -> dbName_bkp = $database -> dbName_bkp;			
            $conn = new mysqli($this->hostName, $this->userName, $this->password, $this->dbName);
            $conn_bkp = new mysqli($this->hostName_bkp, $this->userName_bkp, $this->password_bkp, $this->dbName_bkp);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            } else{
                $this->dbConnect = $conn;
	    }
            if($conn_bkp->connect_error){
                die("Error failed to connect to MySQL: " . $conn_bkp->connect_error);
            } else{
                $this->dbConnect_bkp = $conn_bkp;
	    }
        }
    }
	private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$data= array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$data[]=$row;            
		}
		return $data;
	}
	private function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}   	
	public function employeeList(){		
		#$sqlQuery = "SELECT * FROM ".$this->empTable." where deleted=0 ";
		$sqlQuery = "SELECT * FROM ".$this->empTable." ";
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= 'where(id LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR name LIKE "%'.$_POST["search"]["value"].'%" ';			
			$sqlQuery .= ' OR designation LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR address LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR skills LIKE "%'.$_POST["search"]["value"].'%") ';			
		}
		else{
			$sqlQuery .= "where deleted=0 ";
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		
		$sqlQuery1 = "SELECT * FROM ".$this->empTable." ";
		$result1 = mysqli_query($this->dbConnect, $sqlQuery1);
		$numRows = mysqli_num_rows($result1);
		
		$employeeData = array();	
		while( $employee = mysqli_fetch_assoc($result) ) {		
			$empRows = array();			
			$empRows[] = $employee['id'];
			$empRows[] = ucfirst($employee['name']);
			$empRows[] = $employee['age'];		
			$empRows[] = $employee['skills'];	
			$empRows[] = $employee['address'];
			$empRows[] = $employee['designation'];					
			$empRows[] = '<button type="button" name="update" id="'.$employee["id"].'" class="btn btn-warning btn-xs update">Update</button>';
			$empRows[] = '<button type="button" name="delete" id="'.$employee["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$employeeData[] = $empRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$employeeData
		);
		echo json_encode($output);
	}
	public function getEmployee(){
		if($_POST["empId"]) {
			$sqlQuery = "
				SELECT * FROM ".$this->empTable." 
				WHERE id = '".$_POST["empId"]."'";
			$result = mysqli_query($this->dbConnect, $sqlQuery);	
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			echo json_encode($row);
		}
	}
	public function updateEmployee(){
		if($_POST['empId']) {	
			$updateQuery = "UPDATE ".$this->empTable." 
			SET name = '".$_POST["empName"]."', age = '".$_POST["empAge"]."', skills = '".$_POST["empSkills"]."', address = '".$_POST["address"]."' , designation = '".$_POST["designation"]."'
			WHERE id ='".$_POST["empId"]."'";
			$isUpdated = mysqli_query($this->dbConnect, $updateQuery);		
			$isUpdated = mysqli_query($this->dbConnect_bkp, $updateQuery);		
		}	
	}
	public function addEmployee(){
		$insertQuery = "INSERT INTO ".$this->empTable." (name, age, skills, address, designation) 
			VALUES ('".$_POST["empName"]."', '".$_POST["empAge"]."', '".$_POST["empSkills"]."', '".$_POST["address"]."', '".$_POST["designation"]."')";
		$isUpdated = mysqli_query($this->dbConnect, $insertQuery);	
		$isUpdated = mysqli_query($this->dbConnect_bkp, $insertQuery);		
	}
	public function deleteEmployee(){
		if($_POST["empId"]) {
			$sqlDelete = "
				UPDATE ".$this->empTable." SET name=false,skills=false,address=false,designation=false,age=false,deleted=true 
				WHERE id = '".$_POST["empId"]."'";		
			mysqli_query($this->dbConnect, $sqlDelete);		
			mysqli_query($this->dbConnect_bkp, $sqlDelete);		
		}
	}
}
?>
