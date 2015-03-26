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
		If ($params['tipo'] == 'a-embarcar')
		{
			$sel = "select	F2_EMISSAO	as DATA,
							F2_DOC		as NF, 
							A4_NOME			as TRANSP,
							C5_XCUB			as CUBAGEM,
							C5_PBRUTO		as PESO,
							F2_VALBRUT      as VALOR
							,CB7_XDTFE		as DT_EMB
					from (
					SELECT      C5_XCUB, C5_NOTA, C5_SERIE, C5_TRANSP, C5_EMISSAO,sum(C5_PBRUTO) as C5_PBRUTO, CB7_XDTFE, CB7_NOTA, CB7_SERIE
					 from       CB7110 as CB7
					 inner join SC5110 as SC5
					 on         C5_FILIAL = CB7_FILIAL
					 and        C5_NUM = CB7_PEDIDO
					 and        SC5.D_E_L_E_T_ <> '*'
					 where      CB7_XDTFE = ''
					 and        CB7_XDTFP <> ''
					 and        CB7.D_E_L_E_T_ <> '*'
					 group by   C5_EMISSAO, C5_TRANSP, C5_NOTA, C5_SERIE, C5_XCUB, CB7_XDTFE, CB7_NOTA, CB7_SERIE
					 ) A
					 left  join SF2110 as SF2
					 on         F2_DOC = CB7_NOTA
					 and        F2_SERIE = CB7_SERIE
					 and        SF2.D_E_L_E_T_ <> '*'
					 left join  SA4110 as SA4
					 on         A4_COD = C5_TRANSP
					 and        SA4.D_E_L_E_T_ <> '*'
					order by 	DT_EMB, A4_NOME, F2_DOC ";
		}
		If ($params['tipo'] == 'embarcados')
		{
			$sel = "select	F2_EMISSAO		as DATA,
							F2_DOC			as NF, 
							A4_NOME			as TRANSP,
							C5_XCUB			as CUBAGEM,
							C5_PBRUTO		as PESO,
							F2_VALBRUT      as VALOR
							,CB7_XDTFE		as DT_EMB
					from (
					SELECT      C5_XCUB, C5_NOTA, C5_SERIE, C5_TRANSP, C5_EMISSAO,sum(C5_PBRUTO) as C5_PBRUTO, CB7_XDTFE, CB7_NOTA, CB7_SERIE
					 from       CB7110 as CB7
					 inner join SC5110 as SC5
					 on         C5_FILIAL = CB7_FILIAL
					 and        C5_NUM = CB7_PEDIDO
					 and        SC5.D_E_L_E_T_ <> '*'
					 where      CB7_XDTFE = convert(varchar(8), SYSDATETIME(), 112)
					 and        CB7_XDTFP <> ''
					 and        CB7.D_E_L_E_T_ <> '*'
					 group by   C5_EMISSAO, C5_TRANSP, C5_NOTA, C5_SERIE, C5_XCUB, CB7_XDTFE, CB7_NOTA, CB7_SERIE
					 ) A
					 left  join SF2110 as SF2
					 on         F2_DOC = CB7_NOTA
					 and        F2_SERIE = CB7_SERIE
					 and        SF2.D_E_L_E_T_ <> '*'
					 left join  SA4110 as SA4
					 on         A4_COD = C5_TRANSP
					 and        SA4.D_E_L_E_T_ <> '*'
					order by 	DT_EMB, A4_NOME, F2_DOC ";
		}
//				 where      (CB7_XDTFE = convert(varchar(8), SYSDATETIME(), 112) OR CB7_XDTFE = '')

		$query = $this->Database->doQuery($sel);
		
		if($query > 0)
		{
			$num = $this->Database->num_rows($query);
			if($num > 0){
				$_RETURN['code'] = 200;
				if($params['tipo'] == 'embarcados' || $params['tipo'] == 'a-embarcar')
				{
					$n_y = 0;
					$transp = '';
					$primeiro = 0;
					while($row = $this->Database->fetch_array($query))
				    {
							If($primeiro==0)
							{
								$transp = '';
								$dt_ini_emb['br_date']   = '';
								$dt_ini_emb = $this->Common->validaData($row['DATA']);
								$notas[] = array('NUM_NF'=>$row['NF'],
												 'DT_INI_EMB'=>$dt_ini_emb['br_date'],
												 'VALOR'=>$row['VALOR'],
												 'PESO_BRUTO'=>$row['PESO'],
												 'CUBAGEM'=>$row['CUBAGEM'],
												 );
								$total_notas += 1;
								$total_cubg += $row['CUBAGEM'];
								$total_peso += $row['PESO'];
								$total_vlor += $row['VALOR'];
							}
							If(trim($row['TRANSP'])<> $transp)
							{
								If($primeiro == 1)
								{
									$_RETURN['row'][] = array(
															'NOM_TRANSP' => $transp,
															'TOTAL_EMBARCADOS' => $total_notas,
															'TOTAL_CUBAGEM' => $total_cubg,
															'TOTAL_PESO_BRUTO' => $total_peso,
															'VALOR' => $total_vlor,
															'NOTAS' => $notas,
															'TOTAL_NOTAS' => $total_notas
															);
								}
								$primeiro = 1;
								$notas = array();
								$total_notas = 0;
								$total_cubg = 0;
								$total_peso = 0;
								$total_vlor = 0;
							}

							$dt_ini_emb['br_date']   = '';
							$dt_ini_emb = $this->Common->validaData($row['DATA']);
							$notas[] = array('NUM_NF'=>$row['NF'],
											 'DT_INI_EMB'=>$dt_ini_emb['br_date'],
											 'VALOR'=>$row['VALOR'],
											 'PESO_BRUTO'=>$row['PESO'],
											 'CUBAGEM'=>$row['CUBAGEM'],
											 );
							$total_notas += 1;
							$total_cubg += $row['CUBAGEM'];
							$total_peso += $row['PESO'];
							$total_vlor += $row['VALOR'];

							$transp = trim($row['TRANSP']);
					}
					$_RETURN['row'][] = array(
											'NOM_TRANSP' => $transp,
											'TOTAL_EMBARCADOS' => $total_notas,
											'TOTAL_CUBAGEM' => $total_cubg,
											'TOTAL_PESO_BRUTO' => $total_peso,
											'VALOR' => $total_vlor,
											'NOTAS' => $notas,
											'TOTAL_NOTAS' => $total_notas
											);
					$_RETURN['num'] = 1;
					$_RETURN['num_peso'] = 1;

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
