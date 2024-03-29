<?php 
/*
	Nombre: heasy_mysql
	Versión: 1.3
	Autor del Script: Luis Torres Carpio
	Correo: luis.torres.carpio1@gmail.com
	Descripción: 
		Script para facilitar las consultas en Mysql desde PHP
		Pensado para el desarrollo agil.
*/
class conexion{	
	private $server;
	private $user;
	private $clave;
	private $db;
	
	public $conex;
	
	function __construct()
	{	
		$this->server="localhost";
		$this->user="root";
		$this->clave="";
		$this->db="ejemplo_3";
	}
	public function conectar()
	{
		$this->conex=new mysqli($this->server,$this->user,$this->clave,$this->db);
		$this->conex->set_charset("utf8");//save accent db
	}
	public function cerrar()
	{
		$this->conex->close();
	}
}
function query_exec($consulta){
	$mc=new conexion();
	$mc->conectar();
	$mc->conex->multi_query($consulta);
	while ($mc->conex->next_result()) {;} // flush multi_queries
	$mc->cerrar();
	return 1;
}
function query($consulta){
	$type = array(
		"INSERT INTO", 
		"INSERT", 
		"UPDATE",
		"SELECT * FROM",
		"SELECT",
		"DELETE FROM",
		"CREATE TABLE",
		);
	for($i=0;$i<count($type);$i++){

		if (strpos($consulta, $type[$i]) !== false){
			$mc=new conexion();
			$mc->conectar();
			switch ($type[$i]) {
				case 'INSERT INTO':
					if( !$mc->conex->query($consulta))
						return ["error"=>$mc->conex->error];
					$id=$mc->conex->insert_id;
					return $id;
					break;
				
				case 'UPDATE':
					if( !$mc->conex->query($consulta) )
						return ["error"=>$mc->conex->error]; 
					else
						return 1;
					break;	

				case 'SELECT * FROM':
					if( !$results = $mc->conex->query($consulta) )
						return ["error"=>$mc->conex->error]; 
					
					if($results->num_rows>0){
						while( $rr = mysqli_fetch_assoc($results) ) $rows[] = (object)$rr;
							return $rows;
					}else
						return [];
					break;

				case 'SELECT':
					if( !$results = $mc->conex->query($consulta) ){
						return ["error"=>$mc->conex->error]; 
					}
					if($results->num_rows>0){
						while( $rr = mysqli_fetch_assoc($results) ) $rows[] = (object)$rr;
						return $rows;
					}else
						return [];
					break;
					
				case 'DELETE FROM':
					if( !$mc->conex->query($consulta) )
						return ["error"=>$mc->conex->error]; 
					else
						return 1;
					break;

				case 'CREATE TABLE':

					break;
			}
			$mc->cerrar();
		}
	}
}
function res($data){
	if( gettype($data) == "string" )
			echo $data;
		else
			echo json_encode($data);
		exit();
}
?>