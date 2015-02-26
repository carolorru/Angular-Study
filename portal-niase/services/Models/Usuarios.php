<?php
error_reporting(E_PARSE);
ini_set("display_errors",1);

class Usuarios
{

	public $Database;
	public $Common;

	public function __construct()
	{
		// Dados do banco
		$this->Database = new Database;
		$this->Common = new Common;
		
	}

	public function permissoes()
	{

		$sel = "SELECT
					*
				FROM ".$this->Database->tbl->usuarios_perms."
				ORDER BY name ASC";
		$query = $this->Database->doQuery($sel);
		if($query)
		{
			
			$num = mysql_num_rows($query);
			
			if($num > 0)
			{
				$_RETURN['code'] = 200;
				$_RETURN['num'] = $num;

			    while($row = mysql_fetch_array($query))
			    {

			    	$_RETURN['row'][] = array(
			    							'id' => $row['id'],
			    							'name' => utf8_encode($row['name']),
			    							'slug' => $row['slug']
			    							 );

			    }

			}else{


				$_RETURN['code'] = 200;
				$_RETURN['num'] = 0;
				$_RETURN['msg'] = 'Nenhum resultado encontrado.';

			}
		
		}else{

			$_RETURN['num'] = 0;
			$_RETURN['code'] = 500;
			$_RETURN['error_no'] = mysql_errno();
			$_RETURN['error'] = mysql_error();
			$_RETURN['msg'] = 'Erro.';

		}

		return $_RETURN;

	}
	
	public function search($params)
	{

		$sel = "SELECT
					*
				FROM ".$this->Database->tbl->usuarios."
				WHERE 1 = 1";

		if(isset($params['id']) && !empty($params['id']))
			$sel.= " AND id = ".$params['id'];

		$sel.= " ORDER BY full_name ASC";
		
		$query = $this->Database->doQuery($sel);
		if($query)
		{
			
			$num = mysql_num_rows($query);
			
			if($num > 0)
			{
				$_RETURN['code'] = 200;
				$_RETURN['num'] = $num;

			    while($row = mysql_fetch_array($query))
			    {

			    	$_RETURN['row'][] = array(
			    							'id' => $row['id'],
			    							'full_name' => $row['full_name'],
			    							'email' => $row['email'],
			    							'pass' => $row['pass'],
			    							'permissions' => $row['permissions']
			    							 );

			    }

			}else{


				$_RETURN['code'] = 200;
				$_RETURN['num'] = 0;
				$_RETURN['msg'] = 'Nenhum resultado encontrado.';

			}
		
		}else{

			$_RETURN['num'] = 0;
			$_RETURN['code'] = 500;
			$_RETURN['error_no'] = mysql_errno();
			$_RETURN['error'] = mysql_error();
			$_RETURN['msg'] = 'Erro ao criar usuário.';

		}

		return $_RETURN;
		
	}

	public function login($params)
	{

		$sel = "SELECT
					*
				FROM ".$this->Database->tbl->usuarios."
				WHERE 1 = 1
					  AND email = '".$params['email']."'
					  AND pass  = '".$params['pass']."'";
		$query = $this->Database->doQuery($sel);
		if($query)
		{

			if(mysql_num_rows($query) > 0)
			{

				$_RETURN['code'] = 200;
				$_RETURN['msg']  = 'Usuário autenticado.';
				$_RETURN['num']  = mysql_num_rows($query);

			    $row = mysql_fetch_array($query);

		    	$_RETURN['row'][] = array(
		    							'id' => $row['id'],
		    							'full_name' => $row['full_name'],
		    							'email' => $row['email'],
		    							'pass' => $row['pass']
		    							 );

		    	$sel = "SELECT * FROM ".$this->Database->tbl->usuarios_perms." WHERE id IN(".$row['permissions'].")";
		    	$qry = $this->Database->doQuery($sel);
				if($qry && mysql_num_rows($query) >0)
				{
					
					while($r = mysql_fetch_array($qry))
					{
						$menu[] = array(
									'id' => $r['id'],
									'name' => utf8_encode($r['name']),
									'slug' => $r['slug']
									);
					}

					$_RETURN['menu'] = $menu;
					$_SESSION['menu'] = $menu;

				}

		    	$_SESSION['auth'] = 1;
		    	$_SESSION['full_name'] = $row['full_name'];

			}else{

				$_RETURN['code'] = 200;
				$_RETURN['num'] = 0;
				$_RETURN['msg'] = 'Nenhum resultado encontrado.';

			}

		}else{

			$_RETURN['num'] = 0;
			$_RETURN['code'] = 500;
			$_RETURN['error_no'] = mysql_errno();
			$_RETURN['error'] = mysql_error();
			$_RETURN['msg'] = 'Erro ao criar usuário.';

		}

		return $_RETURN;
		
	}

	public function logout()
	{

		session_destroy();

		$_RETURN['code'] = 200;
		$_RETURN['msg']  = 'Usuário deslogado.';
		
		return $_RETURN;
		
	}

	public function createUser($params)
	{

		$permissions = "";
		if(isset($params['permissions']))
		{
			$permissions = implode(",", $params['permissions']);
		}

		$ins = "INSERT INTO ".$this->Database->tbl->usuarios."
				(full_name,email,pass,permissions)
				VALUES
				('".$params['full_name']."','".$params['email']."','".$params['pass']."','".$permissions."')";
		$query = $this->Database->doQuery($ins);
		if($query)
		{

			$_RETURN['code'] = 200;
			$_RETURN['msg']  = 'Usuário criado.';

		}else{

			$_RETURN['num'] = 0;
			$_RETURN['code'] = 500;
			$_RETURN['error_no'] = mysql_errno();
			$_RETURN['error'] = mysql_error();
			$_RETURN['msg'] = 'Erro ao criar usuário.';

		}

		return $_RETURN;
		
	}

	public function updateUser($params)
	{

		$permission = "";
		if(isset($params['permissions']))
		{
			$permission = implode(",", $params['permissions']);
		}

		$upd = "UPDATE ".$this->Database->tbl->usuarios." SET
					full_name = '".$params['full_name']."',
					email = '".$params['email']."',
					pass = '".$params['pass']."',
					permissions = '".$permissions."'
				WHERE id = ".$params['id'];
		$query = $this->Database->doQuery($upd);
		if($query)
		{

			$_RETURN['code'] = 200;
			$_RETURN['msg']  = 'Usuário atualizado.';

		}else{

			$_RETURN['num'] = 0;
			$_RETURN['code'] = 500;
			$_RETURN['error_no'] = mysql_errno();
			$_RETURN['error'] = mysql_error();
			$_RETURN['msg'] = 'Erro ao atualizar usuário.';

		}

		return $_RETURN;
		
	}

	public function removeUser($params)
	{

		$del = "DELETE FROM ".$this->Database->tbl->usuarios." WHERE id = ".$params['id'];
		$query = $this->Database->doQuery($del);
		if($query)
		{

			$_RETURN['code'] = 200;
			$_RETURN['msg']  = 'Usuário removido.';

		}else{

			$_RETURN['num'] = 500;
			$_RETURN['msg'] = 'Erro ao remover usuário.';

		}

		return $_RETURN;
		
	}
	
}



