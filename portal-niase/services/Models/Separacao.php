<?php

class Separacao
{

	public $Database;
	public $Common;

	public function __construct()
	{
		// Dados do banco
		$this->Database = new Database;
		$this->Common = new Common;
		
	}
	
	public function search($params)
	{

		$sel = "";

		if($params['tipo'] == 'a-separar')
		{
			$sel = "SELECT
						EMISSAO,NUM_PED,COD_CLI,NOM_CLI,QUANTIDADE,
						HR_INI_SEP, DT_INI_SEP,QTD_SEP,
						HR_FIM_SEP,DT_FIM_SEP,
						COD_SEPARADOR,NOM_SEPARADOR,PESO_BRUTO
					FROM ".$this->Database->tbl->separacao."
					WHERE   1 = 1
							AND DT_INI_SEP = '' AND HR_INI_SEP = ''";
		}

		if($params['tipo'] == 'separados')
		{
			$sel = "SELECT
						NOM_SEPARADOR, COUNT(COD_SEPARADOR) AS TOTAL_SEPARADOS, SUM(PESO_BRUTO) AS TOTAL_PESO_BRUTO, 1 AS META
					FROM ".$this->Database->tbl->separacao."
					WHERE   1 = 1
							AND DT_INI_SEP != '' AND HR_INI_SEP != '' AND DT_FIM_SEP != '' AND HR_FIM_SEP != ''
					GROUP BY COD_SEPARADOR";
		}

		if($params['tipo'] == 'em-separacao')
		{
			$sel = "SELECT
						EMISSAO,NUM_PED,COD_CLI,NOM_CLI,QUANTIDADE,
						HR_INI_SEP, DT_INI_SEP,QTD_SEP,
						HR_FIM_SEP,DT_FIM_SEP,
						COD_SEPARADOR,NOM_SEPARADOR,PESO_BRUTO
					FROM ".$this->Database->tbl->separacao."
					WHERE   1 = 1
							AND DT_INI_SEP != '' AND HR_INI_SEP != '' AND DT_FIM_SEP = '' AND HR_FIM_SEP = ''";
		}

		$sel.= " ORDER BY EMISSAO";
		
		$query = $this->Database->doQuery($sel);
		
		if($query)
		{
			
			$num = mysql_num_rows($query);
			if($num > 0){

				$_RETURN['code'] = 200;
				$_RETURN['num'] = $num;

				

				if($params['tipo'] == 'separados')
				{

					while($row = mysql_fetch_array($query))
				    {

				    	$_RETURN['row'][] = array(
				    							'NOM_SEPARADOR' => $row['NOM_SEPARADOR'],
				    							'TOTAL_SEPARADOS' => $row['TOTAL_SEPARADOS'],
				    							'TOTAL_PESO_BRUTO' => $row['TOTAL_PESO_BRUTO'],
				    							'META' => $row['META']
				    							);
				    }

				}else{

					$pesoBruto = array();
				
				    while($row = mysql_fetch_array($query))
				    {

				    	$pesoBruto[] = $row['PESO_BRUTO'];

				    	$emissao['br_date']      = '';

				    	$hr_ini_sep['formatted'] = '';
				    	$dt_ini_sep['br_date']   = '';
				    	
				    	$hr_fim_sep['formatted'] = '';
				    	$dt_fim_sep['br_date']   = '';

				    	if($row['EMISSAO'] != '')
				    	{
				    		$emissao = $this->Common->validaData($row['EMISSAO']);
				    	}

				    	if($row['DT_INI_SEP'] != '')
				    	{
							$dt_ini_sep = $this->Common->validaData($row['DT_INI_SEP']);
							$hr_ini_sep = $this->Common->validaHora($row['HR_INI_SEP']);
				    	}

				    	if($row['DT_FIM_SEP'] != '')
				    	{
							$dt_fim_sep = $this->Common->validaData($row['DT_FIM_SEP']);
							$hr_fim_sep = $this->Common->validaHora($row['HR_FIM_SEP']);
				    	}

				    	$_RETURN['row'][] = array(
				    							'EMISSAO' => $emissao['br_date'],
				    							'NUM_PED' => $row['NUM_PED'],
				    							'COD_CLI' => $row['COD_CLI'],
				    							'NOM_CLI' => $row['NOM_CLI'],
				    							'QUANTIDADE' => $row['QUANTIDADE'],
												'HR_INI_SEP' => $hr_ini_sep['formatted'],
												'DT_INI_SEP' => $dt_ini_sep['br_date'],
												'QTD_SEP' => $row['QTD_SEP'],
												'HR_FIM_SEP' => $hr_fim_sep['formatted'],
												'DT_FIM_SEP' => $dt_fim_sep['br_date'],
												'COD_SEPARADOR' => $row['COD_SEPARADOR'],
												'NOM_SEPARADOR' => $row['NOM_SEPARADOR'],
												'PESO_BRUTO' => $row['PESO_BRUTO'],
				    							 );

				    }

				    $_RETURN['num_peso'] = array_sum($pesoBruto);

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
			$_RETURN['msg'] = 'Erro na query.';

		}

		return $_RETURN;
		
	}
	
}



