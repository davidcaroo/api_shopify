<?php
class db
{

	private $db_host;
	private $db_user;
	private $db_pass;
	private $db_name;

	public function __construct()
	{
		$this->db_host = env('DB_HOST', '127.0.0.1');
		$this->db_user = env('DB_USER', 'root');
		$this->db_pass = env('DB_PASS', '');
		$this->db_name = env('DB_NAME', 'yourDataBaseBrow');
	}

	public function conectar_db()
	{
		$conexion = "mysql:host=$this->db_host;dbname=$this->db_name";
		$db_coneccion = new PDO($conexion, $this->db_user, $this->db_pass);

		$db_coneccion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		return $db_coneccion;
	}
}
