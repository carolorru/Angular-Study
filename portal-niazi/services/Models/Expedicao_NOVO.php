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

//		$sel = "SELECT * FROM ".$this->Database->tbl->expedicao;

		if($params['tipo'] == 'a-embarcar')
		{

			$sel = "select			count(A.C5_NOTA)       			as TOTAL_EMBARCADOS, 
		A.C5_TRANSP				    as TRANSP,
		A4_NOME						as NOM_TRANSP,
		sum(A.C5_XCUB)				as TOTAL_CUBAGEM,
		sum(B.C5_PBRUTO)			as TOTAL_PESO_BRUTO,
		sum(F2_VALBRUT)             as VALOR
from (
SELECT C5_XCUB, C5_NOTA, C5_SERIE, C5_TRANSP, C5_EMISSAO, C5_NUM, sum(C5_PBRUTO) as C5_PBRUTO,
		A4_NOME
 from       CB7110 as CB7
 inner join SC5110 as SC5
 on         C5_FILIAL = CB7_FILIAL
 and        C5_NUM = CB7_PEDIDO
  and        SC5.D_E_L_E_T_ <> '*'
 inner join SA4110 SA4
 on         A4_COD = C5_TRANSP
 and        SA4.D_E_L_E_T_ <> '*'
 where      CB7_XDTIE = '' and CB7_XDTFP <> ''
 and        CB7.D_E_L_E_T_ <> '*'
 group by   C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A4_NOME, C5_TRANSP, C5_NOTA, C5_SERIE, C5_XCUB
 ) A
 inner join SC5110 as B 
 on         B.C5_NUM = A.C5_NUM 
 and        B.D_E_L_E_T_ <> '*'
 inner join SF2110
 on         F2_DOC = A.C5_NOTA
 and        F2_SERIE = A.C5_SERIE
 GROUP BY A4_NOME, A.C5_TRANSP					";

		}

		if($params['tipo'] == 'embarcados')
		{
			$sel = "select			count(A.C5_NOTA)			as TOTAL_EMBARCADOS, 
		A.C5_TRANSP				    as TRANSP,
		A4_NOME						as NOM_TRANSP,
		sum(A.C5_XCUB)				as TOTAL_CUBAGEM,
		sum(B.C5_PBRUTO)			as TOTAL_PESO_BRUTO,
		sum(F2_VALBRUT)             as VALOR
from (
SELECT C5_XCUB, C5_NOTA, C5_SERIE, C5_TRANSP, C5_EMISSAO, C5_NUM, sum(C5_PBRUTO) as C5_PBRUTO,
		A4_NOME
 from       CB7110 as CB7
 inner join SC5110 as SC5
 on         C5_FILIAL = CB7_FILIAL
 and        C5_NUM = CB7_PEDIDO
  and        SC5.D_E_L_E_T_ <> '*'
 inner join SA4110 SA4
 on         A4_COD = C5_TRANSP
 and        SA4.D_E_L_E_T_ <> '*'
 where      CB7_XDTFE = convert(varchar(8), SYSDATETIME(), 112)
 and        CB7.D_E_L_E_T_ <> '*'
 group by   C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A4_NOME, C5_TRANSP, C5_NOTA, C5_SERIE, C5_XCUB
 ) A
 inner join SC5110 as B 
 on         B.C5_NUM = A.C5_NUM 
 and        B.D_E_L_E_T_ <> '*'
 inner join SF2110
 on         F2_DOC = A.C5_NOTA
 and        F2_SERIE = A.C5_SERIE
 GROUP BY A4_NOME, A.C5_TRANSP			";
 
		}

		if($params['tipo'] == 'embarcando')
		{
			/*$sel = "SELECT
						EMISSAO,NUM_NF,SERIE_NF,COD_CLI,NOM_CLI,TRANSP,NOM_TRANSP,CUBAGEM,QUANTIDADE,
						HR_INI_EMB,DT_INI_EMB,
						QTD_EMB,
						HR_FIM_EMB,DT_FIM_EMB,
						COD_EMBARCADOR,NOM_EMBARCADOR,PESO_BRUTO
					FROM ".$this->Database->tbl->expedicao."
					WHERE   DT_INI_EMB != '' AND DT_FIM_EMB = '' ";*/
			$sel = "select	A.C5_EMISSAO				as EMISSAO,
		A.C5_NOTA					as NUM_NF, 
		A.C5_SERIE					as SERIE_NF, 
		A.C5_CLIENTE+A.C5_LOJACLI	as COD_CLI, 
		A1_NOME						as NOM_CLI,
		A.C5_TRANSP				    as TRANSP,
		A4_NOME						as NOM_TRANSP,
		A.C5_XCUB					as CUBAGEM,
		sum(C9_QTDLIB)				as QUANTIDADE, 
		CB7_XHRIE					as HR_INI_EMB, 
		CB7_XDTIE					as DT_INI_EMB, 
		sum(isnull(CB9_QTESEP,0))	as QTD_EMB, 
		CB7_XHRFE					as HR_FIM_EMB, 
		CB7_XDTFE					as DT_FIM_EMB, 
		max(isnull(CB9_CODSEP, ''))	as COD_EMBARCADOR, 
		max(isnull(CB1_NOME,''))	as NOM_EMBARCADOR, 
		sum(B.C5_PBRUTO)			as PESO_BRUTO
from (
SELECT C5_XCUB, C5_NOTA, C5_SERIE, C5_TRANSP, C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, sum(C5_PBRUTO) as C5_PBRUTO,
		A4_NOME, 
		A1_NOME, 
		sum(CB8_QTDORI) as C9_QTDLIB, sum(isnull(CB8_QTDORI-CB8_SALDOE,0)) as CB9_QTESEP, 
		CB7_PEDIDO, CB7_XHRIE, CB7_XDTIE, CB7_XHRFE, CB7_XDTFE, max(isnull(CB7_XOPERE, '')) as CB9_CODSEP, 
		max(isnull(CB1_NOME,'')) as CB1_NOME
 from       CB7110 as CB7
 inner join CB8110 as CB8
 on         CB8_FILIAL = CB7_FILIAL
 and        CB8_PEDIDO = CB7_PEDIDO
 and        CB8.D_E_L_E_T_ <> '*'
 left  join SB1110 as SB1
 on         B1_COD = CB8_PROD 
 and        SB1.D_E_L_E_T_ <> '*' 
 inner join SC5110 as SC5
 on         C5_FILIAL = CB7_FILIAL
 and        C5_NUM = CB7_PEDIDO
  and        SC5.D_E_L_E_T_ <> '*'
 inner join SA1110 SA1
 on         A1_COD = C5_CLIENTE
 and        A1_LOJA = C5_LOJACLI
 and        SA1.D_E_L_E_T_ <> '*'
 inner join SA4110 SA4
 on         A4_COD = C5_TRANSP
 and        SA4.D_E_L_E_T_ <> '*'
 left  join CB1110 CB1
 on         CB1_CODOPE = CB7_XOPERE
 and        CB1.D_E_L_E_T_ <> '*'
 where      CB7_XDTFE = '' 
 and		CB7_XDTIE <> ''
 and        CB7.D_E_L_E_T_ <> '*'
 group by   C5_EMISSAO, C5_NUM, C5_CLIENTE, C5_LOJACLI, A1_NOME, CB7_PEDIDO, CB7_XHRIE, CB7_XDTIE, CB7_XHRFE, CB7_XDTFE, A4_NOME, C5_TRANSP, C5_NOTA, C5_SERIE, C5_XCUB
 ) A
 inner join SC5110 as B 
 on         B.C5_NUM = A.C5_NUM 
 and        B.D_E_L_E_T_ <> '*'
 GROUP BY   A.C5_EMISSAO, A.C5_NUM, A.C5_CLIENTE, A.C5_LOJACLI, A1_NOME, CB7_PEDIDO, CB7_XHRIE, CB7_XDTIE, CB7_XHRFE, CB7_XDTFE, A4_NOME, A.C5_TRANSP, A.C5_NOTA, A.C5_SERIE, A.C5_XCUB
";
		}

		//$sel.= " ORDER BY EMISSAO";
		$query = $this->Database->doQuery($sel);
		
		if($query > 0)
		{
			
			$num = $this->Database->num_rows($query);

			if($num > 0){

				$_RETURN['code'] = 200;

				if($params['tipo'] == 'embarcados' || $params['tipo'] == 'a-embarcar')
				{
					
					while($row = $this->Database->fetch_array($query))
				    {
				    	$sel = "SELECT
									NUM_NF,
									DT_INI_EMB,
									VALOR_NF AS VALOR,
									PESO_BRUTO,
									CUBAGEM
								FROM ".$this->Database->tbl->expedicao."
								WHERE   DT_FIM_EMB != '' 
										AND TRANSP = ".$row['TRANSP']."";
								
										//AND TRANSP = ".$row['TRANSP']."";

						$qry = $this->Database->doQuery($sel);
						$notas = array();
						$total_notas = $this->Database->num_rows($qry);

						while($r = $this->Database->fetch_array($qry))
						{

							$dt_ini_emb['br_date']   = '';
							$dt_ini_emb = $this->Common->validaData($r['DT_INI_EMB']);

							$notas[] = array(
										'NUM_NF' => trim($r['NUM_NF']),
										'DT_INI_EMB' => $dt_ini_emb['br_date'],
										'VALOR' => $r['VALOR'],
										'PESO_BRUTO' => $r['PESO_BRUTO'],
										'CUBAGEM' => $r['CUBAGEM'],
										);

						}

				    	$_RETURN['row'][] = array(
				    							'NOM_TRANSP' => trim($row['NOM_TRANSP']),
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
							WHERE   DT_FIM_EMB != '' ";
					$qry = $this->Database->doQuery($sel);
					$row = $this->Database->fetch_array($qry);

					$_RETURN['num'] = $row['TOTAL'];
					$_RETURN['num_peso'] = $row['PESO_TOTAL'];

				}else{

					$_RETURN['num'] = $num;

					$pesoBruto = array();

				    while($row = $this->Database->fetch_array($query))
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
				    							'NUM_NF' => trim($row['NUM_NF']),
				    							'SERIE_NF' => $row['SERIE_NF'],
				    							'COD_CLI' => $row['COD_CLI'],
				    							'NOM_CLI' => trim($row['NOM_CLI']),
				    							'TRANSP' => $row['TRANSP'],
												'NOM_TRANSP' => trim($row['NOM_TRANSP']),
												'CUBAGEM' => $row['CUBAGEM'],
				    							'QUANTIDADE' => $row['QUANTIDADE'],
												'HR_INI_EMB' => $hr_ini_emb['formatted'],
												'DT_INI_EMB' => $dt_ini_emb['br_date'],
												'QTD_EMB' => $row['QTD_EMB'],
												'HR_FIM_EMB' => $hr_fim_emb['formatted'],
												'DT_FIM_EMB' => $dt_fim_emb['br_date'],
												'COD_EMBARCADOR' => trim($row['COD_EMBARCADOR']),
												'NOM_EMBARCADOR' => trim($row['NOM_EMBARCADOR']),
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
			$_RETURN['error'] = $this->Database->dbError();
			$_RETURN['msg'] = 'Erro na query.';

		}

		return $_RETURN;
		
	}
	
}



