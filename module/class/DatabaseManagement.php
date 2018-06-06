<?php 
	include_once $_SERVER['DOCUMENT_ROOT'] . '/path.php';
	include_once ROOT . 'parameter.php';
	class DATABASE_MANAGEMENT
	{
		private $Connection = '';
		public function __construct()
		{
			if ($this->Connection == '') 
			{
				try
				{
					@session_start();
					$Host = DB_HOST;
					$Port = DB_PORT;
					$Username = DB_USERNAME;
					$Password = DB_PASSWORD;
					$Name = DB_NAME;
					$DNS = "mysql:host={$Host . ':' . $Port};dbname={$Name}";
					$this->Connection = new PDO($DNS , $Username , $Password);
					$this->Connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->Connection->exec("SET NAMES utf8");	
				} 
				catch (PDOException $Exception) 
				{
					 $Exception->getMessage();	
				}
			}
		}
		public function GetConnection()
		{
			return $this->Connection;
		}
	}
 ?>