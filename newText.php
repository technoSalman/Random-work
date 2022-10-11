<?php
$handle = fopen("demo.txt", "r");
$tempArr = [];
$d = [];
if ($handle) {
  $count = 0;
    while (($line = fgets($handle)) !== false) {
    
      if(trim($line) != "Infofox 929781"){
        
        if ((strpos(trim($line), "Millennium Coin") !== false) || (strpos(trim($line), "*User") !== false) || (strpos(trim($line), "disconnected") !== false)) {
           
        }else{
          if(trim($line) != ""){
            array_push($d, trim($line));
          }
          
        }

      }else{
        array_push($tempArr, $d);
        $d = [];
      }

      $count++;
    }
    fclose($handle);
  }
  $arr = [];
  $DataArr=[];
  // Create connection
  
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "12StepIllinois";
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // die;
  $phoneArr = [];

  $c = 1;
  foreach($tempArr as $item){
    $tempItem = $item;
    $TphoneArr = [];
    $email = "";
    $count = 0;
    unset($item[0]);
    $item = array_values($item);
    $city = "";
    $state = "";
    $zip = "";
   
    foreach($item as $row){

         
     
      $reg = "/^\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/";

      if(preg_match($reg,trim($row))) {
        array_push($TphoneArr, trim($row));
        unset($item[$count]);
      
      }

      if (filter_var(trim($row), FILTER_VALIDATE_EMAIL)) {
        $email = trim($row);
        unset($item[$count]);
      }
      if(strpos(strtolower($row), "investment amt") !== false){
        
        unset($item[$count]);
      }
      $count++;
    }
    
    // echo '<pre>';
    // echo print_r($item);
    // echo '</pre>';

    $fulladress = end($item);
    $fulladress = explode(" ",$fulladress);
    
    array_pop($item);
    $name = end($item);
    array_pop($item);
   

    if(sizeof($item) <= 2 && isset($name[0])){
      $ct = 0;
      foreach($fulladress as $row){
        $ct =  $ct + 1;
        if($ct != sizeof($fulladress)){
          if(strlen($row) == 2){
            $state = $row;
          }else{
            $city .= $row.' ';
          }
        }else{

          $zip = $row.' ';
        }
      }




      $name = explode(" ",trim($name));

   
      $address1 = (isset($item[0]) ? $item[0] : '');
      $address2 = (isset($item[1]) ? $item[1] : '');

      $fnam = (isset($name[0]) ? $name[0] : '');
      $lnam = (isset($name[1]) ? $name[1] : '');

      $fieldVal1  = mysqli_real_escape_string($conn, "fronts");
      $fieldVal2  = mysqli_real_escape_string($conn, json_encode($tempItem));
      $fnam       = mysqli_real_escape_string($conn, $fnam);
      $lnam       = mysqli_real_escape_string($conn, $lnam);
      $fieldVal4  = mysqli_real_escape_string($conn, trim($address1));
      $fieldVal41 = mysqli_real_escape_string($conn, trim($address2));
      $fieldVal5  = mysqli_real_escape_string($conn,  trim(implode(",",$TphoneArr)));
      $fieldVal6  = mysqli_real_escape_string($conn,  trim($city));
      $fieldVal7  = mysqli_real_escape_string($conn,  trim($state));
      $fieldVal8  = mysqli_real_escape_string($conn,  trim($zip));

      $emaild  = mysqli_real_escape_string($conn,  trim($email));


      $DataArr[] = "('$fieldVal1', '$fieldVal2', '$fnam','$lnam','$fieldVal4','$fieldVal41','$fieldVal5','$fieldVal6','$fieldVal7','$fieldVal8','$emaild')";
    }else{
     
      $c++;
    }

  try {

    $sql = "INSERT INTO pdfdata (file_name, data, first_name, last_name, address, address2, phone_number, city, state, zip, email_address) values ";
    $sql .= implode(',', $DataArr);


   $mysqli =  mysqli_query($conn, $sql) or trigger_error("Query Failed! SQL: $sql - Error: ".mysqli_error($conn), E_USER_ERROR);;

   print_r($mysqli);

    //echo 'Record Added';
  }
  catch(Exception $e) {
    echo 'Message: ' .$e->getMessage();
  }




?>