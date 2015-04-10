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
			$sel = "select	C5_EMISSAO					as EMISSAO,
		C5_NUM						as NUM_PED, 
		C5_CLIENTE+C5_LOJACLI		as COD_CLI, 
		A1_NOME						as NOM_CLI, 
		sum(C9_QTDLIB)				as QUANTIDADE, 
		CB7_XHRIC					as HR_INI_CONF, 
		CB7_XDTIC					as DT_INI_CONF, 
		sum(isnull(CB9_QTESEP,0))	as QTD_CONF, 
		CB7_XHRFC					as HR_FIM_CONF, 
		CB7_XDTFC					as DT_FIM_CONF, 
		max(isnull(CB9_CODSEP, ''))	as COD_CONFERENTE, 
		max(isnull(CB1_NOME,''))	as NOM_CONFERENTE, 
		1000                        as META_CONF,
		sum(C5_PBRUTO)				as PESO_BRUTO,
		CB7_DTFIMS 					as DT_SEPARACAO 
from (

SELECT C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, sum(CB8_QTDORI) as C9_QTDLIB, CB7_PEDIDO, CB7_DTFIMS, CB7_XHRIC, CB7_XDTIC, CB7_XHRFC, CB7_XDTFC, sum(isnull(CB8_QTDORI-CB8_SALDOE,0)) as CB9_QTESEP, max(isnull(CB7_XOPERC, '')) as CB9_CODSEP, max(isnull(CB1_NOME,'')) as CB1_NOME, 
(select SC5a.C5_PBRUTO from SC5110 as SC5a where SC5a.C5_NUM = SC5.C5_NUM and SC5a.D_E_L_E_T_ <> '*') as C5_PBRUTO
 from       CB7110 as CB7
 inner join CB8110 as CB8
 on         CB8_FILIAL = CB7_FILIAL
 and        CB8_PEDIDO = CB7_PEDIDO
 and        CB8.D_E_L_E_T_ <> '*'
 left  join SB1110 as SB1
 on         B1_COD = CB8_PROD 
 and        SB1.D_E_L_E_T_ <> '*' 
 inner join SC5110 as SC5
 on         C5_NUM = CB7_PEDIDO
-- and        C5_EMISSAO LIKE '201511%'
 and        SC5.D_E_L_E_T_ <> '*'
 inner join SA1110 SA1
 on         A1_COD = C5_CLIENTE
 and        A1_LOJA = C5_LOJACLI
 and        SA1.D_E_L_E_T_ <> '*'
 left  join CB1110 CB1
 on         CB1_CODOPE = CB7_XOPERC
 and        CB1.D_E_L_E_T_ <> '*'
 where      CB7.D_E_L_E_T_ <> '*'
 and        CB7_XDTIC = '' and CB7_DTFIMS <> ''
 group by   C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, CB7_PEDIDO, CB7_DTFIMS, CB7_XHRIC, CB7_XDTIC, CB7_XHRFC, CB7_XDTFC
 ) A GROUP BY C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, CB7_PEDIDO, CB7_DTFIMS, CB7_XHRIC, CB7_XDTIC, CB7_XHRFC, CB7_XDTFC";
		}

		if($params['tipo'] == 'conferidos')
		{
			$sel = "select	count(C5_NUM)		as TOTAL_CONFERIDOS, 
							CB1_NOME			as NOM_CONFERENTE, 
							max(CB1_XMETAC)     as META,
							sum(C5_PBRUTO)		as TOTAL_PESO_BRUTO 
					 from       CB7110 as CB7
					 inner join SC5110 as SC5
					 on         C5_NUM = CB7_PEDIDO
					 and        SC5.D_E_L_E_T_ <> '*'
					 left  join CB1110 CB1
					 on         CB1_CODOPE = CB7_XOPERC
					 and        CB1.D_E_L_E_T_ <> '*'
					 where      CB7.D_E_L_E_T_ <> '*'
					 and        CB7_XDTFC = '".$params['ref-date']."'
					 group by   CB1_NOME
					";
		}

		if($params['tipo'] == 'em-conferencia')
		{
			$sel = "select	C5_EMISSAO					as EMISSAO,
		C5_NUM						as NUM_PED, 
		C5_CLIENTE+C5_LOJACLI		as COD_CLI, 
		A1_NOME						as NOM_CLI, 
		sum(C9_QTDLIB)				as QUANTIDADE, 
		CB7_XHRIC					as HR_INI_CONF, 
		CB7_XDTIC					as DT_INI_CONF, 
		sum(isnull(CB9_QTESEP,0))	as QTD_CONF, 
		CB7_XHRFC					as HR_FIM_CONF, 
		CB7_XDTFC					as DT_FIM_CONF, 
		max(isnull(CB9_CODSEP, ''))	as COD_CONFERENTE, 
		max(isnull(CB1_NOME,''))	as NOM_CONFERENTE, 
		1000                        as META_CONF,
		sum(C5_PBRUTO)				as PESO_BRUTO 
from (

SELECT C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, sum(CB8_QTDORI) as C9_QTDLIB, CB7_PEDIDO, CB7_XHRIC, CB7_XDTIC, CB7_XHRFC, CB7_XDTFC, sum(isnull(CB8_QTDORI-CB8_SALDOE,0)) as CB9_QTESEP, max(isnull(CB7_XOPERC, '')) as CB9_CODSEP, max(isnull(CB1_NOME,'')) as CB1_NOME,
(select SC5a.C5_PBRUTO from SC5110 as SC5a where SC5a.C5_NUM = SC5.C5_NUM and SC5a.D_E_L_E_T_ <> '*') as C5_PBRUTO
 from       CB7110 as CB7
 inner join CB8110 as CB8
 on         CB8_FILIAL = CB7_FILIAL
 and        CB8_PEDIDO = CB7_PEDIDO
 and        CB8.D_E_L_E_T_ <> '*'
 left  join SB1110 as SB1
 on         B1_COD = CB8_PROD 
 and        SB1.D_E_L_E_T_ <> '*' 
 inner join SC5110 as SC5
 on         C5_NUM = CB7_PEDIDO
-- and        C5_EMISSAO LIKE '201511%'
 and        SC5.D_E_L_E_T_ <> '*'
 inner join SA1110 SA1
 on         A1_COD = C5_CLIENTE
 and        A1_LOJA = C5_LOJACLI
 and        SA1.D_E_L_E_T_ <> '*'
 left  join CB1110 CB1
 on         CB1_CODOPE = CB7_XOPERC
 and        CB1.D_E_L_E_T_ <> '*'
 where      CB7.D_E_L_E_T_ <> '*'
 and        CB7_XDTIC <> ''
 and        CB7_XDTFC = ''
 group by   C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, CB7_PEDIDO, CB7_XHRIC, CB7_XDTIC, CB7_XHRFC, CB7_XDTFC
 ) A GROUP BY C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, CB7_PEDIDO, CB7_XHRIC, CB7_XDTIC, CB7_XHRFC, CB7_XDTFC";
		}

		//$sel.= " ORDER BY EMISSAO";

		$query = $this->Database->doQuery($sel);

		if($query > 0)
		{
			
			$num = $this->Database->num_rows($query);

			if($num > 0){

				if($params['tipo'] == 'conferidos')
				{

					while($row = $this->Database->fetch_array($query))
				    {

				    	$_PESO_TOTAL[] 		 = $row['TOTAL_PESO_BRUTO'];
				    	$_TOTAL_CONFERIDOS[] = $row['TOTAL_CONFERIDOS'];

				    	$_RETURN['row'][] = array(
				    							'NOM_CONFERENTE'   => $row['NOM_CONFERENTE'],
				    							'TOTAL_CONFERIDOS' => $row['TOTAL_CONFERIDOS'],
				    							'TOTAL_PESO_BRUTO' => $row['TOTAL_PESO_BRUTO'],
				    							'META' 			   => $row['META']
				    							);
				    }
				    /*
				    $sel = "SELECT
								COUNT(*) AS TOTAL, SUM(PESO_BRUTO) AS PESO_TOTAL
							FROM ".$this->Database->tbl->conferencia."
							WHERE   1 = 1
									AND DT_INI_CONF != '' AND HR_INI_CONF != '' AND DT_FIM_CONF != '' AND HR_FIM_CONF != ''";
					$qry = $this->Database->doQuery($sel);
					$row = $this->Database->fetch_array($qry);
					*/

					$_RETURN['num_peso'] = array_sum($_PESO_TOTAL);
					$_RETURN['num']		 = array_sum($_TOTAL_CONFERIDOS);

				}else{

					$_RETURN['code'] = 200;
					

					$pesoBruto = array();
					$qtdConf = array();

				    while($row = $this->Database->fetch_array($query))
				    {

				    	$pesoBruto[] = $row['PESO_BRUTO'];
				    	$qtdConf[]   = $row['NUM_PED'];

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
						
						if($row['DT_SEPARACAO'] != '')
				    	{
							$dt_separacao = $this->Common->validaData($row['DT_SEPARACAO']);
				    	}

				    	$_RETURN['row'][] = array(
				    							'EMISSAO' 		 => $emissao['br_date'],
				    							'NUM_PED' 		 => $row['NUM_PED'],
				    							'COD_CLI' 		 => $row['COD_CLI'],
				    							'NOM_CLI' 		 => trim($row['NOM_CLI']),
				    							'QUANTIDADE' 	 => $row['QUANTIDADE'],
												'HR_INI_CONF' 	 => $hr_ini_conf['formatted'],
												'DT_INI_CONF' 	 => $dt_ini_conf['br_date'],
												'QTD_CONF' 		 => $row['QTD_CONF'],
												'HR_FIM_CONF' 	 => $hr_fim_conf['formatted'],
												'DT_FIM_CONF'    => $dt_fim_conf['br_date'],
												'COD_CONFERENTE' => trim($row['COD_CONFERENTE']),
												'NOM_CONFERENTE' => trim($row['NOM_CONFERENTE']),
												'PESO_BRUTO' 	 => $row['PESO_BRUTO'],
												'DT_SEPARACAO'   => $dt_separacao['br_date']
				    							 );

				    }

				    $_RETURN['num_peso'] = array_sum($pesoBruto);
				    $_RETURN['num']      = count(array_unique($qtdConf));

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
