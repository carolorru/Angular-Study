<?php

class Expedicao
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

		if($params['tipo'] == 'a-embarcar')
		{
			$sel = "SELECT
						EMISSAO,NUM_NF,SERIE_NF,COD_CLI,NOM_CLI,TRANSP,NOM_TRANSP,CUBAGEM,QUANTIDADE,
						HR_INI_EMB,DT_INI_EMB,
						QTD_EMB,
						HR_FIM_EMB,DT_FIM_EMB,
						COD_EMBARCADOR,NOM_EMBARCADOR,PESO_BRUTO
					FROM ".$this->Database->tbl->expedicao."
					WHERE   1 = 1
							AND DT_INI_EMB = '' AND HR_INI_EMB = ''";
		}

		if($params['tipo'] == 'embarcados')
		{
			$sel = "SELECT
						TRANSP,
						NOM_TRANSP,
						COUNT(NUM_NF) AS TOTAL_EMBARCADOS,
						SUM(CUBAGEM) AS TOTAL_CUBAGEM,
						SUM(PESO_BRUTO) AS TOTAL_PESO_BRUTO,
						SUM(100) AS VALOR
					FROM ".$this->Database->tbl->expedicao."
					WHERE   1 = 1
							AND DT_INI_EMB != '' AND HR_INI_EMB != '' AND DT_FIM_EMB != '' AND HR_FIM_EMB != ''
					GROUP BY TRANSP";
		}

		if($params['tipo'] == 'embarcando')
		{
			$sel = "SELECT
						EMISSAO,NUM_NF,SERIE_NF,COD_CLI,NOM_CLI,TRANSP,NOM_TRANSP,CUBAGEM,QUANTIDADE,
						HR_INI_EMB,DT_INI_EMB,
						QTD_EMB,
						HR_FIM_EMB,DT_FIM_EMB,
						COD_EMBARCADOR,NOM_EMBARCADOR,PESO_BRUTO
					FROM ".$this->Database->tbl->expedicao."
					WHERE   1 = 1
							AND DT_INI_EMB != '' AND HR_INI_EMB != '' AND DT_FIM_EMB = '' AND HR_FIM_EMB = ''";
		}

		$sel.= " ORDER BY EMISSAO";
		
		$query = $this->Database->doQuery($sel);
		
		if($query)
		{
			
			$num = mysql_num_rows($query);
			if($num > 0){

				$_RETURN['code'] = 200;

				if($params['tipo'] == 'embarcados')
				{

					
					while($row = mysql_fetch_array($query))
				    {

				    	$sel = "SELECT
									NUM_NF,
									DT_INI_EMB,
									100 AS VALOR,
									PESO_BRUTO,
									CUBAGEM
								FROM ".$this->Database->tbl->expedicao."
								WHERE   1 = 1
										AND DT_INI_EMB != '' AND HR_INI_EMB != '' AND DT_FIM_EMB != '' AND HR_FIM_EMB != ''
										AND TRANSP = '".$row['TRANSP']."'";
						$qry = $this->Database->doQuery($sel);
						$notas = array();
						$total_notas = mysql_num_rows($qry);
						while($r = mysql_fetch_array($qry))
						{

							$dt_ini_emb['br_date']   = '';
							$dt_ini_emb = $this->Common->validaData($r['DT_INI_EMB']);

							$notas[] = array(
										'NUM_NF' => $r['NUM_NF'],
										'DT_INI_EMB' => $dt_ini_emb['br_date'],
										'VALOR' => $r['VALOR'],
										'PESO_BRUTO' => $r['PESO_BRUTO'],
										'CUBAGEM' => $r['CUBAGEM'],
										);

						}

				    	$_RETURN['row'][] = array(
				    							'NOM_TRANSP' => $row['NOM_TRANSP'],
				    							'TOTAL_EMBARCADOS' => $row['TOTAL_EMBARCADOS'],
				    							'TOTAL_CUBAGEM' => $row['TOTAL_CUBAGEM'],
				    							'TOTAL_PESO_BRUTO' => $row['TOTAL_PESO_BRUTO'],
				    							'VALOR' => $row['VALOR'],
				    							'NOTAS' => $notas,
				    							'TOTAL_NOTAS' => $total_notas
				    							);

				    }

				    $sel = "SELECT
								COUNT(*) AS TOTAL, SUM(PESO_BRUTO) AS PESO_TOTAL
							FROM ".$this->Database->tbl->expedicao."
							WHERE   1 = 1
									AND DT_INI_EMB != '' AND HR_INI_EMB != '' AND DT_FIM_EMB != '' AND HR_FIM_EMB != ''";
					$qry = $this->Database->doQuery($sel);
					$row = mysql_fetch_array($qry);

					$_RETURN['num'] = $row['TOTAL'];
					$_RETURN['num_peso'] = $row['PESO_TOTAL'];

				}else{

					$_RETURN['num'] = $num;

					$pesoBruto = array();

				    while($row = mysql_fetch_array($query))
				    {

				    	$pesoBruto[] = $row['PESO_BRUTO'];

				    	$emissao['br_date']      = '';

				    	$hr_ini_emb['formatted'] = '';
				    	$dt_ini_emb['br_date']   = '';

				    	$hr_fim_emb['formatted'] = '';
				    	$dt_fim_emb['br_date']   = '';

				    	if($row['EMISSAO'] != '')
				    	{
				    		$emissao = $this->Common->validaData($row['EMISSAO']);
				    	}

				    	if($row['DT_INI_EMB'] != '')
				    	{
							$dt_ini_emb = $this->Common->validaData($row['DT_INI_EMB']);
							$hr_ini_emb = $this->Common->validaHora($row['HR_INI_EMB']);
				    	}

				    	if($row['DT_FIM_EMB'] != '')
				    	{
							$dt_fim_emb = $this->Common->validaData($row['DT_FIM_EMB']);
							$hr_fim_emb = $this->Common->validaHora($row['HR_FIM_EMB']);
				    	}
				    	
				    	

				    	$_RETURN['row'][] = array(
				    							'EMISSAO' => $emissao['br_date'],
				    							'NUM_NF' => $row['NUM_NF'],
				    							'SERIE_NF' => $row['SERIE_NF'],
				    							'COD_CLI' => $row['COD_CLI'],
				    							'NOM_CLI' => $row['NOM_CLI'],
				    							'TRANSP' => $row['TRANSP'],
												'NOM_TRANSP' => $row['NOM_TRANSP'],
												'CUBAGEM' => $row['CUBAGEM'],
				    							'QUANTIDADE' => $row['QUANTIDADE'],
												'HR_INI_EMB' => $hr_ini_emb['formatted'],
												'DT_INI_EMB' => $dt_ini_emb['br_date'],
												'QTD_EMB' => $row['QTD_EMB'],
												'HR_FIM_EMB' => $hr_fim_emb['formatted'],
												'DT_FIM_EMB' => $dt_fim_emb['br_date'],
												'COD_EMBARCADOR' => $row['COD_EMBARCADOR'],
												'NOM_EMBARCADOR' => $row['NOM_EMBARCADOR'],
												'PESO_BRUTO' => $row['PESO_BRUTO']
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



