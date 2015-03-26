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

			$this->tbl->usuarios   	   = 'PAINEL_ACD_004';

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
			$this->dbhost   = "192.168.0.121"; #Nome do host
			$this->db       = "DATANIAZITEX"; 			#Nome do banco de dados
			$this->user     = "SIGA"; 			#Nome do usuário
			$this->password = "SIGA"; 	#Senha do usuário

			$this->tbl->usuarios   	   = 'PAINEL_ACD_004';
			$this->tbl->usuarios_perms = 'uperp_USERS_PERMS';

			$this->tbl->separacao   = 'PAINEL_ACD_001';
			$this->tbl->conferencia = 'PAINEL_ACD_002';
			$this->tbl->expedicao   = 'PAINEL_ACD_003';

			//$this->mssql = @mssql_connect($this->dbhost,$this->user,$this->password) or die("Não foi possível a conexão com o banco de dados!");
			//@mssql_select_db($this->db,$this->mssql) or die("Não foi possível selecionar o banco de dados!");

			$this->dbtype   = "sqlsrv";
			$connectionInfo = array("UID" => $this->user, "PWD" => $this->password, "Database" => $this->db);
			$this->sqlsrv   = sqlsrv_connect($this->dbhost, $connectionInfo); 
			
			if($conn === false)
			{

				echo "Unable to connect.";
				die( print_r( sqlsrv_errors(), true)); 
			 
			}

		}

		//echo "::".$this->dbtype;

	}

	public function dbError($query)
	{

		if($this->dbtype == 'mysql')
		{

			$error = mysql_error();
			return $error;

		}else if($this->dbtype == 'sqlsrv'){

			$error = sqlsrv_errors();
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

		}else if($this->dbtype == 'sqlsrv'){

			$error = sqlsrv_fetch_array($query);
			return $error;

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

		}else if($this->dbtype == 'sqlsrv'){

			$num = sqlsrv_num_rows($query);
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

		}else if($this->dbtype == 'sqlsrv'){

			$query = sqlsrv_query($this->sqlsrv,$params,array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
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



