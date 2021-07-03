<?php 


class customercontact implements IContactform {
    
    protected $dbconnection;
    protected $customerDbTable;
    protected $formvalues;
    protected $statusTracker=[];
    protected $collectedFormData=[];    
    protected $customerformrequestingKeys=["customername","customeremail","customerphone","messageBox"];//requestind form attribute names
    protected $processedContactDetails=[];
    protected $databasecolomns=["c_name","c_email","c_contact","c_message"];

    public function __construct($dbcon, $formvalues, $tbtable){
        //var_dump($tbtable);
        $this->customerDbTable=$tbtable;
        $this->formvalues=$formvalues;
        $this->dbconnection=$dbcon;
    }
    
    public function addcontact() {
        if($this->validate()){            
            if($this->preparesqlstatemen()){
                array_push($this->statusTracker,["succes"=>"Received and we will contact you soon"]);
                return $this->statusTracker;                
            }            
            array_push($this->statusTracker,["error"=>"Something gone wrong"]);
                return $this->statusTracker;                
        }else{
            //var_dump($this->statusTracker);
            return $this->statusTracker;
        }        
    }

    public function validate() {
        foreach ($this->formvalues as $key => $value){
            $santizedinput=$this->sanitizeAndEmptyValueValidate($key,$value);                   
            if($santizedinput ){
                for ($i=0; $i < count($this->customerformrequestingKeys);$i++){
                    if($key==$this->customerformrequestingKeys[$i]){
                        array_push($this->collectedFormData,[$this->customerformrequestingKeys[$i]=>$santizedinput]);
                    }                    
                }                
            }
            else
            {              
               return false;                
            }  
        }
        return true;//return true if all values are validated and filled
        
    }
    
    //sanitize and empty value check userinput
    public function sanitizeAndEmptyValueValidate($key,$value){
         $valuea=(filter_var($value,FILTER_SANITIZE_STRING)); 
        if(!empty($valuea)){
            return $valuea;
        }else{
            array_push($this->statusTracker,["error"=>"".$key." is Empty"]);
            return false;
        }        
    }
    
    //prepare sqlinsert statement dynamically
    private function preparesqlstatemen(){        
        $preparedStatementPlaceHolders=[];
        $sqlexpectedTypes=[];
        $sqlexpectedValues1=[];
        $sqlexpectedValues2=[];
        
        for($k=0; $k<COUNT($this->collectedFormData); $k++){
            array_push($preparedStatementPlaceHolders,"?");
            array_push($sqlexpectedTypes,"s");
            array_push($sqlexpectedValues1,implode(",",$this->collectedFormData[$k]));
        }       
       
        for($k=0; $k<COUNT($sqlexpectedValues1); $k++){ 
            $rt=($sqlexpectedValues1[$k]);
            array_push($sqlexpectedValues2,''.$rt.'');            
        }
        
        $sql= $this->dbconnection->conn->prepare("INSERT INTO ".$this->customerDbTable."(".implode(",",$this->databasecolomns).") VALUES(".implode(",",$preparedStatementPlaceHolders).")");       
        $da=implode('',$sqlexpectedTypes);
        $sql->bind_param($da,...$sqlexpectedValues2);

        //var_dump(...$sqlexpectedValues2);
        
       if($sql->execute()=== TRUE){           
           return true;
       }else{
           return $sql->error;
       }
  
    }
    
    public function getcontact() {
        
    }

}

interface IContactform {
    
    public function validate();
    public  function addcontact();
    public function  getcontact();
}

class dbclass {
    //put your code here
    
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "contact_form";
    
    public function __construct(){
        $this->conn=new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . ($this->conn->connect_error));
        }
        //return $this->conn;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formvalues=json_decode(file_get_contents('php://input'), true);	

    $dbcon=new dbclass(); //passing db connection from db class
    $tbtable="contact_master"; //table name     
    $contcatClass=new customercontact($dbcon, $formvalues[0], $tbtable);    
    
    echo json_encode($contcatClass->addcontact());

}

?>
