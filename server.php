<?php
require_once "lib/nusoap.php";

$server = new soap_server();
$server->configureWSDL("CRUD", "CRUD");
  


$server->register("crud.updateData",
	array("description" => "xsd:string",
		"id" => "xsd:string",
		"name" => "xsd:string",
		),
    array('name' => 'xsd:string'
    ),
    'urn:CRUD',
 	'urn:CRUD#CRUD',
 	'rpc',
 	'encoded',
 	''
    );

$server->register("crud.deleteData",
	array("id" => "xsd:int"),
    array('name' => 'xsd:string'),
    'urn:CRUD',
 	'urn:CRUD#CRUD',
 	'rpc',
 	'encoded',
 	''
    );

$server->register("crud.insertData",
	array("name" => "xsd:string",
		  "description" => "xsd:string"),
    array('name' => 'xsd:string'),
    'urn:CRUD',
 	'urn:CRUD#CRUD',
 	'rpc',
 	'encoded',
 	''
    );

   $server->wsdl->addComplexType(
    'Busca',
    'complexType',
    'struct',
    'all',
    '',
    array(
            'id' => array('name' => 'id', 'type' => 'xsd:int'),
            'name' => array('name' => 'name', 'type' => 'xsd:string'),
            'description' => array('name' => 'description', 'type' => 'xsd:string'),
            'tags' => array('name' => 'tags', 'type' => 'xsd:string'),
    )
    );

    $server->wsdl->addComplexType(
        "TableArray",
        "complexType",
        "array",
        "",
        "SOAP-ENC:Array",
        array(),
        array(
        'Job' => array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Busca[]')
        ),
        'tns:Busca'
        );

    $server->register("crud.GetData",
	array(),
    array("name" => "tns:TableArray"),
    'urn:CRUD',
 	'urn:CRUD#CRUD',
 	'rpc',
 	'encoded',
 	''
    );

 
 class crud {

 	public function GetData() {
 		$con=mysqli_connect("localhost","user","pass","database");
	 		if (mysqli_connect_errno())
	 		{
	 			echo "Failed to connect to MySQL: " . mysqli_connect_error();
	 		}
	 		$result = mysqli_query($con,"SELECT DISTINCT z.name, z.description, t.tags, z.id from yunbit_entriesTags a RIGHT JOIN yunbit_entries z on a.fk_entries_id = z.id LEFT JOIN yunbit_tags t on a.fk_tags_id = t.id");
	 		$items = array();
 			while ($r = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
 				$items[] = $r;
 			}
 			return $items;
 		}

	 	public function insertData($name,$description) {
	 		$con=mysqli_connect("localhost","edsato_yunbit","modelo00","edsato_yunbit");
	 		if (mysqli_connect_errno())
	 		{
	 			echo "Failed to connect to MySQL: " . mysqli_connect_error();
	 		}
	 		mysqli_query($con,"INSERT INTO yunbit_entries (name,description) VALUES ('$name','$description'
	 			)");
	 		$piece = explode(' ',$description);
	 		$dups = array();
	 		foreach(array_count_values($piece) as $val => $c){
	 			if($c > 2) $dups[] = $val;
	 		}
	 		$tags = implode(',', $dups);
	 		if (!empty($tags)){
	 			mysqli_query($con,"INSERT INTO yunbit_tags (tags) VALUES ('{$tags}')");
	 			mysqli_query($con,"INSERT INTO yunbit_entriesTags (fk_entries_id,fk_tags_id) SELECT MAX(yunbit_entries.id), MAX(yunbit_tags.id) FROM yunbit_entries, yunbit_tags");
	 		}
	 	}

	 	public function updateData($id,$description,$name) {
	 		$con=mysqli_connect("localhost","user","pass","database");
	 		if (mysqli_connect_errno())
	 		{
	 			echo "Failed to connect to MySQL: " . mysqli_connect_error();
	 		}
	 		mysqli_query($con,"UPDATE yunbit_entries SET ('$name','$description') name='$name', description='$description' where id = '$id')");
	 		$piece = explode(' ',$description);
	 		$dups = array();
	 		foreach(array_count_values($piece) as $val => $c){
	 			if($c > 2) $dups[] = $val;
	 		}
	 		$tags = implode(',', $dups);
	 		if (!empty($tags)){
	 			mysqli_query($con,"INSERT INTO yunbit_tags (tags) VALUES ('{$tags}')");
	 			mysqli_query($con,"INSERT INTO yunbit_entriesTags (fk_entries_id,fk_tags_id) SELECT MAX(yunbit_entries.id), '$tags' FROM yunbit_entries, yunbit_tags");
	 		}
	 	}

	 public function deleteData($id) {
	 		$con=mysqli_connect("localhost","user","pass","database");
	 		if (mysqli_connect_errno())
	 		{
	 			echo "Failed to connect to MySQL: " . mysqli_connect_error();
	 		}
	 		mysqli_query($con,"DELETE FROM yunbit_entries WHERE id='$id'");
	 	}
}

	


$HTTP_RAW_POST_DATA = (isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : 'teste');
$server->service($HTTP_RAW_POST_DATA);
?>
