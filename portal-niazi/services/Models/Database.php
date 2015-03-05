<?php

error_reporting(E_ALL);
ini_set("display_errors",1);

class Database
{

	public function __construct()
	{

		$this->tbl = new stdClass();

		##
		# SERVIDORES DE TESTE
		##
		//if($_GET['TYPE'] == "MySQL" || $_SERVER['SERVER_NAME'] == 'webtalk.com.br' || $_SERVER['SERVER_NAME'] == 'www.webtalk.com.br' || $_SERVER['SERVER_NAME'] == 'carolineorru.com.br' || $_SERVER['SERVER_NAME'] == 'www.carolineorru.com.br')
		if(isset($_GET['TYPE']) && $_GET['TYPE'] == "MySQL")
		{
			
			$this->dbtype   = "mysql";
			$this->dbhost   = "186.202.152.133"; #Nome do host
			$this->db       = "incommunicatio1"; #Nome do banco de dados
			$this->user     = "incommunicatio1"; #Nome do usuário
			$this->password = "geral152133";     #Senha do usuário

			$this->tbl->usuarios   	   = 'uperp_USERS';
			$this->tbl->usuarios_perms = 'uperp_USERS_PERMS';
			
			$this->tbl->separacao      = 'uperp_PAINEL_ACD_001';
			$this->tbl->conferencia    = 'uperp_PAINEL_ACD_002';
			$this->tbl->expedicao 	   = 'uperp_PAINEL_ACD_003';

			$this->mssql = @mysql_connect($this->dbhost,$this->user,$this->password) or die("Não foi possível a conexão com o banco de dados!");
			@mysql_select_db($this->db,$this->mssql) or die("Não foi possível selecionar o banco de dados!");

		}else{

			##
			# SERVIDOR DE PRODUÇÃO
			##
			// Dados do banco
			$this->dbtype   = "mssql";
			$this->dbhost   = "177.102.18.147"; #Nome do host
			$this->db       = "UPERP"; 			#Nome do banco de dados
			$this->user     = "sa"; 			#Nome do usuário
			$this->password = "uperp@3468"; 	#Senha do usuário

			$this->tbl->usuarios   	   = 'uperp_USERS';
			$this->tbl->usuarios_perms = 'uperp_USERS_PERMS';

			$this->tbl->separacao   = 'PAINEL_ACD_001';
			$this->tbl->conferencia = 'PAINEL_ACD_002';
			$this->tbl->expedicao   = 'PAINEL_ACD_003';

			$this->mssql = @mssql_connect($this->dbhost,$this->user,$this->password) or die("Não foi possível a conexão com o banco de dados!");
			@mssql_select_db($this->db,$this->mssql) or die("Não foi possível selecionar o banco de dados!");

		}

		//echo "::".$this->dbtype;

	}

	public function dbError($query)
	{

		if($this->dbtype == 'mysql')
		{
			$error = mysql_error();
			return $error;
		}

		$error = mssql_get_last_message();
		return $error;

	}
	
	public function fetch_array($query)
	{

		if($this->dbtype == 'mysql')
		{
			$row = mysql_fetch_array($query);
			return $row;
		}

		$row = mssql_fetch_array($query);
		return $row;

	}

	public function num_rows($query)
	{

		if($this->dbtype == 'mysql')
		{
			$num = mysql_num_rows($query);
			return $num;
		}

		$num = mssql_num_rows($query);
		return $num;

	}

	public function doQuery($params)
	{
		
		if($this->dbtype == 'mysql')
		{
			$query = mysql_query($params,$this->mssql);
			return $query;
		}

		$query = mssql_query($params);

		return $query;

	}

	public function pre($params)
	{

		echo "<pre>";
		print_r($params);
		echo "</pre>";

	}
	
}



