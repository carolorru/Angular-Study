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
			
			$num = $this->Database->num_rows($query);

			if($num > 0)
			{
				$_RETURN['code'] = 200;
				$_RETURN['num'] = $num;

			    while($row = $this->Database->fetch_array($query))
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
				$_RETURN['msg'] = 'Nenhum1 resultado encontrado.';

			}
		
		}else{

			$_RETURN['num'] = 0;
			$_RETURN['code'] = 500;
			$_RETURN['error'] = $this->Database->dbError();
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
			
			$num = $this->Database->num_rows($query);
			
			if($num > 0)
			{
				$_RETURN['code'] = 200;
				$_RETURN['num'] = $num;

			    while($row = $this->Database->fetch_array($query))
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
				$_RETURN['msg'] = 'Nenhum2 resultado encontrado.';

			}
		
		}else{

			$_RETURN['num'] = 0;
			$_RETURN['code'] = 500;
			$_RETURN['error'] = $this->Database->dbError();
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
		//echo $sel;
		$query = $this->Database->doQuery($sel);
		if($query)
		{

			$num = $this->Database->num_rows($query);

			if($num > 0)
			{

				$_RETURN['code'] = 200;
				$_RETURN['msg']  = 'Usuário autenticado.';
				$_RETURN['num']  = $num;

			    $row = $this->Database->fetch_array($query);

		    	$_RETURN['row'][] = array(
		    							'id' => $row['id'],
		    							'full_name' => $row['full_name'],
		    							'email' => $row['email'],
		    							'pass' => $row['pass']
		    							 );

		    	$sel = "SELECT * FROM ".$this->Database->tbl->usuarios_perms." WHERE id IN(".$row['permissions'].")";
		    	$qry = $this->Database->doQuery($sel);
		    	$num = $this->Database->num_rows($qry);

				if($qry && $num >0)
				{

					while($r = $this->Database->fetch_array($qry))
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
				$_RETURN['msg'] = 'Nenhum3 resultado encontrado.';

			}

		}else{

			$_RETURN['num'] = 0;
			$_RETURN['code'] = 500;
			$_RETURN['error'] = $this->Database->dbError();
			$_RETURN['msg'] = 'Erro na query.';

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

	public function trocar_senha($params)
	{

		$upd = "UPDATE ".$this->Database->tbl->usuarios." SET
					pass = '".$params['pass']."'
				WHERE id = ".$params['id'];
		$query = $this->Database->doQuery($upd);
		if($query)
		{

			$_RETURN['code'] = 200;
			$_RETURN['num'] = 0;
			$_RETURN['msg'] = 'Senha alterada.';

		}else{

			$_RETURN['code'] = 500;
			$_RETURN['num'] = 0;
			$_RETURN['msg'] = 'Erro ao alterar senha.';

		}

	}
	
}