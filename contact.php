<?php 

$customerName="";
$customeremail="";
$customerContact="";
$customerMessage="";

$contacerr=[];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   

    $ddsT=json_decode(file_get_contents('php://input'), true);	
    //print_r($ddsT);
    if(!empty($ddsT)){
    foreach ($ddsT[0] as $key => $value) {
        $value=(filter_var($value,FILTER_SANITIZE_STRING));  

        if(!empty($value)){

            if($key=="customername"){ 
                $customerName=$value; 
            };
            if($key=="customeremail"){
                 $customeremail=$value; 
            };
            if($key=="customerphone"){ 
                $customerContact=$value; 
            };
            if($key=="messageBox"){ 
                $customerMessage=$value; 
            };
            
        }
        else
        {
            $customerro="";

            if($key=="customername"){ 
                $customerro="fill a name"; 
            };
            if($key=="customeremail"){
                $customerro="fill your Email address"; 
            };
            if($key=="customerphone"){ 
                $customerro="Give me Mobile Number"; 
            };
            if($key=="messageBox"){ 
                $customerro="leave me a short Message"; 
            };


            array_push($contacerr,["error"=>"Please ".$customerro]);
            echo json_encode($contacerr);
            return;
        }
        
     }//end foreach
     
     
    }//end post

    if(savecontactDetails($customerName,$customeremail,$customerContact,$customerMessage)){

        array_push($contacerr,["succes"=>"Received and will contact your soon!"]);
    }else{
        array_push($contacerr,["error"=>"something gone wrong! Check and Try again"]);
    }

    
    
    echo json_encode($contacerr);
    return;
    

    
}



function savecontactDetails($customerName,$customeremail,$customerContact,$customerMessage){

    $servername = "yourserver";
    $username = "root";
    $password = "";
    $dbname = "yourdbname";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    
    $sql = "INSERT INTO yourtable (c_name, c_email, c_contact,	c_message)
    VALUES ('$customerName','$customeremail','$customerContact','$customerMessage')";
    
   // VALUES (`.$customerName.`,`.$customeremail.`,`.$customerContact.`,`.$customerMessage.`)";
    
    if ($conn->query($sql) === TRUE) {
        $conn->close();
        return true;
      
    } else {
        
        $conn->close();
        return false;
      
    }
    
    

}




?>


