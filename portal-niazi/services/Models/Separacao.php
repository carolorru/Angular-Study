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

//		$sel = "SELECT * FROM ".$this->Database->tbl->separacao;

		if($params['tipo'] == 'a-separar')
		{
			$sel = "select	C5_EMISSAO					as EMISSAO,
		C5_NUM						as NUM_PED, 
		C5_CLIENTE+C5_LOJACLI		as COD_CLI, 
		A1_NOME						as NOM_CLI, 
		sum(C9_QTDLIB)				as QUANTIDADE, 
		CB7_HRINIS					as HR_INI_SEP, 
		CB7_DTINIS					as DT_INI_SEP, 
		sum(isnull(CB9_QTESEP,0))	as QTD_SEP, 
		CB7_HRFIMS					as HR_FIM_SEP, 
		CB7_DTFIMS					as DT_FIM_SEP, 
		max(isnull(CB9_CODSEP, ''))	as COD_SEPARADOR, 
		max(isnull(CB1_NOME,''))	as NOM_SEPARADOR, 
		1000                        as META_SEP,
		sum(C5_PBRUTO)				as PESO_BRUTO 
from (
SELECT C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, sum(CB8_QTDORI) as C9_QTDLIB, CB7_PEDIDO, CB7_HRINIS, CB7_DTINIS, CB7_HRFIMS, CB7_DTFIMS, sum(isnull(CB8_QTDORI-CB8_SALDOE,0)) as CB9_QTESEP, max(isnull(CB7_XOPERS, '')) as CB9_CODSEP, max(isnull(CB1_NOME,'')) as CB1_NOME, (select SC5a.C5_PBRUTO from SC5110 as SC5a where SC5a.C5_NUM = SC5.C5_NUM and SC5a.D_E_L_E_T_ <> '*') as C5_PBRUTO
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
 and        SC5.D_E_L_E_T_ <> '*'
 inner join SA1110 SA1
 on         A1_COD = C5_CLIENTE
 and        A1_LOJA = C5_LOJACLI
 and        SA1.D_E_L_E_T_ <> '*'
 left  join CB1110 CB1
 on         CB1_CODOPE = CB7_XOPERS
 and        CB1.D_E_L_E_T_ <> '*'
 where      CB7.D_E_L_E_T_ <> '*'
 and        CB7_DTINIS = '' 
 group by   C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, CB7_PEDIDO, CB7_HRINIS, CB7_DTINIS, CB7_HRFIMS, CB7_DTFIMS
 ) A GROUP BY C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, CB7_PEDIDO, CB7_HRINIS, CB7_DTINIS, CB7_HRFIMS, CB7_DTFIMS";
		}

		if($params['tipo'] == 'separados')
		{
 
 $sel = "select	count(C5_NUM)				as TOTAL_SEPARADOS, 
		sum(isnull(CB9_QTESEP,0))	as QTD_SEP, 
		CB9_CODSEP					as COD_SEPARADOR, 
		CB1_NOME					as NOM_SEPARADOR, 
		max(CB1_XMETAS)				as META_SEP,
		sum(C5_PBRUTO)				as TOTAL_PESO_BRUTO 
from (
SELECT C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, sum(CB8_QTDORI) as C9_QTDLIB, CB7_PEDIDO, CB7_HRINIS, CB7_DTINIS, CB7_HRFIMS, CB7_DTFIMS, sum(isnull(CB8_QTDORI-CB8_SALDOS,0)) as CB9_QTESEP, max(isnull(CB7_XOPERS, '')) as CB9_CODSEP, max(isnull(CB1_NOME,'')) as CB1_NOME, max(isnull(CB1_XMETAS,'')) as CB1_XMETAS, (select SC5a.C5_PBRUTO from SC5110 as SC5a where SC5a.C5_NUM = SC5.C5_NUM and SC5a.D_E_L_E_T_ <> '*') as C5_PBRUTO
 from       CB7110 as CB7
 inner join CB8110 as CB8
 on         CB8_FILIAL = CB7_FILIAL
 and        CB8_PEDIDO = CB7_PEDIDO
 and        CB8.D_E_L_E_T_ <> '*'
 inner join SC5110 as SC5
 on         C5_NUM = CB7_PEDIDO
 and        SC5.D_E_L_E_T_ <> '*'
 inner join SA1110 SA1
 on         A1_COD = C5_CLIENTE
 and        A1_LOJA = C5_LOJACLI
 and        SA1.D_E_L_E_T_ <> '*'
 left  join CB1110 CB1
 on         CB1_CODOPE = CB7_XOPERS
 and        CB1.D_E_L_E_T_ <> '*'
 where      CB7.D_E_L_E_T_ <> '*'
 and        CB7_DTINIS <> '' 
 and		CB7_DTFIMS = '".$params['ref-date']."'
 group by   C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, CB7_PEDIDO, CB7_HRINIS, CB7_DTINIS, CB7_HRFIMS, CB7_DTFIMS
 ) A GROUP BY CB9_CODSEP, CB1_NOME";
 
		}

		if($params['tipo'] == 'em-separacao')
		{
			$sel = "select	C5_EMISSAO					as EMISSAO,
		C5_NUM						as NUM_PED, 
		C5_CLIENTE+C5_LOJACLI		as COD_CLI, 
		A1_NOME						as NOM_CLI, 
		sum(C9_QTDLIB)				as QUANTIDADE, 
		CB7_HRINIS					as HR_INI_SEP, 
		CB7_DTINIS					as DT_INI_SEP, 
		sum(isnull(CB9_QTESEP,0))	as QTD_SEP, 
		CB7_HRFIMS					as HR_FIM_SEP, 
		CB7_DTFIMS					as DT_FIM_SEP, 
		max(isnull(CB9_CODSEP, ''))	as COD_SEPARADOR, 
		max(isnull(CB1_NOME,''))	as NOM_SEPARADOR, 
		1000                        as META,
		sum(C5_PBRUTO)				as PESO_BRUTO 
from (
SELECT C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, sum(CB8_QTDORI) as C9_QTDLIB, CB7_PEDIDO, CB7_HRINIS, CB7_DTINIS, CB7_HRFIMS, CB7_DTFIMS, sum(isnull(CB8_QTDORI-CB8_SALDOS,0)) as CB9_QTESEP, max(isnull(CB7_XOPERS, '')) as CB9_CODSEP, max(isnull(CB1_NOME,'')) as CB1_NOME, (select SC5a.C5_PBRUTO from SC5110 as SC5a where SC5a.C5_NUM = SC5.C5_NUM and SC5a.D_E_L_E_T_ <> '*') as C5_PBRUTO
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
 and        SC5.D_E_L_E_T_ <> '*'
 inner join SA1110 SA1
 on         A1_COD = C5_CLIENTE
 and        A1_LOJA = C5_LOJACLI
 and        SA1.D_E_L_E_T_ <> '*'
 left  join CB1110 CB1
 on         CB1_CODOPE = CB7_XOPERS
 and        CB1.D_E_L_E_T_ <> '*'
 where      CB7.D_E_L_E_T_ <> '*'
 and        CB7_DTINIS <> '' 
 and		CB7_DTFIMS = ''
 group by   C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, CB7_PEDIDO, CB7_HRINIS, CB7_DTINIS, CB7_HRFIMS, CB7_DTFIMS
 ) A GROUP BY C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, CB7_PEDIDO, CB7_HRINIS, CB7_DTINIS, CB7_HRFIMS, CB7_DTFIMS ";
		}

		//$sel.= " ORDER BY EMISSAO";
		$query = $this->Database->doQuery($sel);
		
		//echo $sel;
		if($query > 0)
		{
			
			$num = $this->Database->num_rows($query);

			if($num > 0){

				$_RETURN['code'] = 200;
				
				if($params['tipo'] == 'separados')
				{

					while($row = $this->Database->fetch_array($query))
				    {

				    	$_PESO_TOTAL[] = $row['TOTAL_PESO_BRUTO'];
				    	$_TOTAL_SEPARADOS[] = $row['TOTAL_SEPARADOS'];
						
						if($row['DATA_SEPARACAO'] != '')
							$dt_separado = $this->Common->validaData($row['DATA_SEPARACAO']);

				    	$_RETURN['row'][] = array(
				    							'NOM_SEPARADOR' => $row['NOM_SEPARADOR'],
				    							'TOTAL_SEPARADOS' => $row['TOTAL_SEPARADOS'],
				    							'TOTAL_PESO_BRUTO' => $row['TOTAL_PESO_BRUTO'],
												'QTD_SEP' => $row['QTD_SEP'],
				    							'META' => $row['META']
				    							);
				    }
					
					$_RETURN['num_peso'] = array_sum($_PESO_TOTAL);
					$_RETURN['num']		 = array_sum($_TOTAL_SEPARADOS);

				}else{

					$_RETURN['num'] = $num;
					$pesoBruto = array();
				
				    while($row = $this->Database->fetch_array($query))
				    {

				    	$pesoBruto[] = $row['PESO_BRUTO'];
				    	$qtdSep[]    = $row['NUM_PED'];

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
				    							'NOM_CLI' => trim($row['NOM_CLI']),
				    							'QUANTIDADE' => $row['QUANTIDADE'],
												'HR_INI_SEP' => $hr_ini_sep['formatted'],
												'DT_INI_SEP' => $dt_ini_sep['br_date'],
												'QTD_SEP' => $row['QTD_SEP'],
												'HR_FIM_SEP' => $hr_fim_sep['formatted'],
												'DT_FIM_SEP' => $dt_fim_sep['br_date'],
												'COD_SEPARADOR' => trim($row['COD_SEPARADOR']),
												'NOM_SEPARADOR' => trim($row['NOM_SEPARADOR']),
												'PESO_BRUTO' => $row['PESO_BRUTO'],
				    							 );

				    }

				    $_RETURN['num_peso'] = array_sum($pesoBruto);
				    $_RETURN['num']      = count(array_unique($qtdSep));

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



