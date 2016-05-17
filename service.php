<?php
/*
Nome: Eduardo sato
Version: 1.0
Description : CRUD SOAP using NUSOAP
*/
//Call the reference lib
require_once "lib/nusoap.php";
 
//GET the server to consume
$client = new nusoap_client("server.php?wsdl", true);

//Get the API values
$action = $_GET['action'];
$id = $_GET['id'];
$name = $_GET['name'];
$description = $_GET['description'];

//Create CRUD based in action
if($action === "insert"){
 $client->call("crud.insertData", array("name" => "".$name."", "description" => "".$description.""));
}

if($action === "delete"){
 $client->call("crud.deleteData", array("id" => "".$id.""));
}

if($action === "update"){
 $client->call("crud.updateData", array("id" => "".$id."", "description" => "".$description."", "name" => "".$description.""));
}

if($action === "select"){
$result = $client->call("crud.GetData");
echo build_table($result);
}

//Create visualization
    function build_table($array){
    // start table
    $html = '<table>';
    // header row
    $html .= '<tr>';
    foreach($array[0] as $key=>$value){
            $html .= '<th>' . $key . '</th>';
        }
    $html .= '</tr>';

    // data rows
    foreach( $array as $key=>$value){
        $html .= '<tr>';
        foreach($value as $key2=>$value2){
            $html .= '<td>' . $value2 . '</td>';
        }
        $html .= '</tr>';
    }
    // finish table and return it
    $html .= '</table>';
    return $html;
}
