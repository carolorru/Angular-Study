<?php

error_reporting(E_ALL);
ini_set("display_errors",1);

class Conferencia
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

		$sel = "SELECT * FROM ".$this->Database->tbl->conferencia;

		if($params['tipo'] == 'a-conferir')
		{
			$sel = "SELECT
						EMISSAO,NUM_PED,COD_CLI,NOM_CLI,QUANTIDADE,
						HR_INI_CONF, DT_INI_CONF,QTD_CONF,
						HR_FIM_CONF,DT_FIM_CONF,
						COD_CONFERENTE,NOM_CONFERENTE,PESO_BRUTO
					FROM ".$this->Database->tbl->conferencia."
					WHERE   1 = 1
							AND DT_INI_CONF = '' AND HR_INI_CONF = ''";
		}

		if($params['tipo'] == 'conferidos')
		{
			$sel = "SELECT
						EMISSAO, NOM_CONFERENTE, COUNT(COD_CONFERENTE) AS TOTAL_CONFERIDOS, SUM(PESO_BRUTO) AS TOTAL_PESO_BRUTO, META_CONF AS META
					FROM ".$this->Database->tbl->conferencia."
					WHERE   1 = 1
							AND DT_INI_CONF != '' AND HR_INI_CONF != '' AND DT_FIM_CONF != '' AND HR_FIM_CONF != ''
					GROUP BY COD_CONFERENTE, NOM_CONFERENTE, META_CONF, EMISSAO";
		}

		if($params['tipo'] == 'em-conferencia')
		{
			$sel = "SELECT
						EMISSAO,NUM_PED,COD_CLI,NOM_CLI,QUANTIDADE,
						HR_INI_CONF, DT_INI_CONF,QTD_CONF,
						HR_FIM_CONF,DT_FIM_CONF,
						COD_CONFERENTE,NOM_CONFERENTE,PESO_BRUTO
					FROM ".$this->Database->tbl->conferencia."
					WHERE   1 = 1
							AND DT_INI_CONF != '' AND HR_INI_CONF != '' AND DT_FIM_CONF = '' AND HR_FIM_CONF = ''";
		}

		$sel.= " ORDER BY EMISSAO";

		$query = $this->Database->doQuery($sel);

		if($query['num'] > 0)
		{
			
			$num = $this->Database->num_rows($query);

			if($num > 0){

				if($params['tipo'] == 'conferidos')
				{

					while($row = $this->Database->fetch_array($query))
				    {

				    	$_RETURN['row'][] = array(
				    							'NOM_CONFERENTE' => $row['NOM_CONFERENTE'],
				    							'TOTAL_CONFERIDOS' => $row['TOTAL_CONFERIDOS'],
				    							'TOTAL_PESO_BRUTO' => $row['TOTAL_PESO_BRUTO'],
				    							'META' => $row['META']
				    							);
				    }

				    $sel = "SELECT
								COUNT(*) AS TOTAL, SUM(PESO_BRUTO) AS PESO_TOTAL
							FROM ".$this->Database->tbl->conferencia."
							WHERE   1 = 1
									AND DT_INI_CONF != '' AND HR_INI_CONF != '' AND DT_FIM_CONF != '' AND HR_FIM_CONF != ''";
					$qry = $this->Database->doQuery($sel);
					$row = $this->Database->fetch_array($qry);

					$_RETURN['num'] = $row['TOTAL'];
					$_RETURN['num_peso'] = $row['PESO_TOTAL'];

				}else{

					$_RETURN['code'] = 200;
					$_RETURN['num'] = $num;

					$pesoBruto = array();

				    while($row = $this->Database->fetch_array($query))
				    {

				    	$pesoBruto[] = $row['PESO_BRUTO'];

				    	$emissao['br_date']      = '';

				    	$hr_ini_conf['formatted'] = '';
				    	$dt_ini_conf['br_date']   = '';

				    	$hr_fim_conf['formatted'] = '';
				    	$dt_fim_conf['br_date']   = '';

				    	if($row['EMISSAO'] != '')
				    	{
				    		$emissao = $this->Common->validaData($row['EMISSAO']);
				    	}

				    	if($row['DT_INI_CONF'] != '')
				    	{
							$dt_ini_conf = $this->Common->validaData($row['DT_INI_CONF']);
							$hr_ini_conf = $this->Common->validaHora($row['HR_INI_CONF']);
				    	}

				    	if($row['DT_FIM_CONF'] != '')
				    	{
							$dt_fim_conf = $this->Common->validaData($row['DT_FIM_CONF']);
							$hr_fim_conf = $this->Common->validaHora($row['HR_FIM_CONF']);
				    	}

				    	$_RETURN['row'][] = array(
				    							'EMISSAO' => $emissao['br_date'],
				    							'NUM_PED' => $row['NUM_PED'],
				    							'COD_CLI' => $row['COD_CLI'],
				    							'NOM_CLI' => trim($row['NOM_CLI']),
				    							'QUANTIDADE' => $row['QUANTIDADE'],
												'HR_INI_CONF' => $hr_ini_conf['formatted'],
												'DT_INI_CONF' => $dt_ini_conf['br_date'],
												'QTD_CONF' => $row['QTD_CONF'],
												'HR_FIM_CONF' => $hr_fim_conf['formatted'],
												'DT_FIM_CONF' => $dt_fim_conf['br_date'],
												'COD_CONFERENTE' => $row['COD_CONFERENTE'],
												'NOM_CONFERENTE' => $row['NOM_CONFERENTE'],
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
			$_RETURN['error'] = $this->Database->dbError();
			$_RETURN['msg'] = 'Erro na query.';

		}

		return $_RETURN;
		
	}
	
}



