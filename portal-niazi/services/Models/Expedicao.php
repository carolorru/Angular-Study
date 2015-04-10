
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
	
	public function aFaturar()
	{
	
	}
	
	public function search($params)
	{
		If ($params['tipo'] == 'a-embarcar')
		{
			$sel = "select	F2_EMISSAO		as DATA,
							F2_DOC			as NF, 
							A4_NOME			as TRANSP,
							A1_NOME			as 'NOME_CLIENTE',
							C5_XCUB			as CUBAGEM,
							C5_PBRUTO		as PESO,
							F2_VALBRUT      as VALOR,
							CB7_XDTFE		as DT_EMB,
							CB7_XDTFP 		as DT_LIB,
							CB7_PEDIDO 		as COD_PEDIDO
					from (
					SELECT      C5_XCUB, 
								C5_NOTA, 
								C5_SERIE, 
								C5_TRANSP, 
								C5_EMISSAO,
								sum(C5_PBRUTO) as C5_PBRUTO, 
								A1_NOME,
								CB7_XDTFE, 
								CB7_PEDIDO, 
								CB7_XDTFP, 
								CB7_NOTA, 
								CB7_SERIE
					 from       CB7110 as CB7
					 inner join SC5110 as SC5
					 on         C5_FILIAL = CB7_FILIAL
					 and        C5_NUM = CB7_PEDIDO
					 and        SC5.D_E_L_E_T_ <> '*'
					 inner join SA1110 as SA1
					 on         A1_COD = C5_CLIENTE
					 and        A1_LOJA = C5_LOJACLI
					 and        SA1.D_E_L_E_T_ <> '*'
					 where      CB7_XDTFE = ''
					 and        CB7_XDTFP <> ''
					 and        CB7.D_E_L_E_T_ <> '*'
					 group by   C5_EMISSAO, 
					 			C5_TRANSP, 
					 			C5_NOTA, 
					 			C5_SERIE, 
					 			C5_XCUB, 
								A1_NOME,
					 			CB7_XDTFE, 
					 			CB7_XDTFP, 
					 			CB7_NOTA, 
					 			CB7_SERIE, 
					 			CB7_PEDIDO
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
							F2_VALBRUT      as VALOR,
							CB7_XDTFE		as DT_EMB
					from (
					SELECT      C5_XCUB, 
								C5_NOTA, 
								C5_SERIE, 
								C5_TRANSP, 
								C5_EMISSAO,
								sum(C5_PBRUTO) as C5_PBRUTO, 
								CB7_XDTFE, 
								CB7_NOTA, 
								CB7_SERIE
					 from       CB7110 as CB7
					 inner join SC5110 as SC5
					 on         C5_FILIAL = CB7_FILIAL
					 and        C5_NUM = CB7_PEDIDO
					 and        SC5.D_E_L_E_T_ <> '*'
					 /* where      CB7_XDTFE = convert(varchar(8), SYSDATETIME(), 112) */
					 where      CB7_XDTFE = '".$params['ref-date']."'
					 and        CB7_XDTFP <> ''
					 and        CB7.D_E_L_E_T_ <> '*'
					 group by   C5_EMISSAO, 
					 			C5_TRANSP, 
					 			C5_NOTA, 
					 			C5_SERIE, 
					 			C5_XCUB, 
					 			CB7_XDTFE, 
					 			CB7_NOTA, 
					 			CB7_SERIE
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
			if($num > 0)
			{
				$_RETURN['code'] = 200;
	
				if($params['tipo'] == 'embarcados' || $params['tipo'] == 'a-embarcar')
				{
					//echo "Expedicao 2";
					$return = array();
					$chaveValor = array();
					while($row = $this->Database->fetch_array($query))
				    {
					/*
					echo "<pre>";
					print_r($row);
					echo "</pre>";
					die();
					*/
						if($params['tipo'] == 'embarcados'){
						
							$chave = 'embarcados';
							
						}else{
							
							$chave = 'aembarcar';
							
							//verifica se esta atrasado
							$datetime1 = date_create(date("Y-m-d"));
							$datetime2 = date_create(implode("-",array_reverse(explode("/",$row['DATA']))));
							//$datetime2 = date_create("2015-04-03"); //MODELO DE TESTE
							$interval  = date_diff($datetime1, $datetime2);
							$atraso = $interval->format('%R%a');
							
						}
						
						$dt_ini_emb['br_date']   = '';
						$dt_ini_emb   = $this->Common->validaData($row['DATA']);
						$dt_liberacao = $this->Common->validaData($row['DT_LIB']); 

						$notas = array(
									'NOM_TRANSP'   => trim($row['TRANSP']),
									'NOME_CLIENTE' => trim($row['NOME_CLIENTE']),
									'DT_NF'		   => trim($row['DATA']),
									'NUM_NF'	   => trim($row['NF']),
									'NUM_PEDIDO'   => trim($row['COD_PEDIDO']),
									'DT_LIB'	   => trim($dt_liberacao['br_date']),
									'DT_INI_EMB'   => trim($dt_ini_emb['br_date']),
									'VALOR' 	   => trim($row['VALOR']),
									'PESO_BRUTO'   => trim($row['PESO']),
									'CUBAGEM' 	   => trim($row['CUBAGEM']),
									);
						
						if($chave == 'aembarcar'){
							
							$NF = trim($row['NF']);
							
							if($NF == ''){
								$adicional = 'aFaturar';
								$_RETURN[$adicional]['row']['NOTAS'][] 			  = $notas;
								$_RETURN[$adicional]['row']['TOTAL_PESO_BRUTO'][] = trim($row['PESO']);
								$_RETURN[$adicional]['row']['TOTAL_CUBAGEM'][]    = trim($row['CUBAGEM']);
								$_RETURN[$adicional]['row']['VALOR'][] 		      = trim($row['VALOR']);
								$_RETURN[$adicional]['row']['TOTAL_EMBARCADOS']   = count($_RETURN[$adicional]['row']['NOTAS']);
								$_RETURN[$adicional]['row']['TOTAL_NOTAS']		  = count($_RETURN[$adicional]['row']['NOTAS']);
							}
							if($atraso <= -4){
								$adicional = 'atrasados';
								$_RETURN[$adicional]['row']['NOTAS'][] 			  = $notas;
								$_RETURN[$adicional]['row']['TOTAL_PESO_BRUTO'][] = trim($row['PESO']);
								$_RETURN[$adicional]['row']['TOTAL_CUBAGEM'][]    = trim($row['CUBAGEM']);
								$_RETURN[$adicional]['row']['VALOR'][] 		      = trim($row['VALOR']);
								$_RETURN[$adicional]['row']['TOTAL_EMBARCADOS']   = count($_RETURN[$adicional]['row']['NOTAS']);
								$_RETURN[$adicional]['row']['TOTAL_NOTAS']		  = count($_RETURN[$adicional]['row']['NOTAS']);
							}
							

						}
						
						if($NF == '')
							$notas['NUM_NF'] = 'P'.$row['COD_PEDIDO'];
							
						$_RETURN[$chave]['row']['NOTAS'][] 			  = $notas;
						$_RETURN[$chave]['row']['TOTAL_PESO_BRUTO'][] = trim($row['PESO']);
						$_RETURN[$chave]['row']['TOTAL_CUBAGEM'][]    = trim($row['CUBAGEM']);
						$_RETURN[$chave]['row']['VALOR'][] 		      = trim($row['VALOR']);
						$_RETURN[$chave]['row']['TOTAL_EMBARCADOS']   = count($_RETURN[$chave]['row']['NOTAS']);
						$_RETURN[$chave]['row']['TOTAL_NOTAS']		  = count($_RETURN[$chave]['row']['NOTAS']);
						
					}

					foreach($_RETURN as $key => $value)
					{
					//echo $key."<br>";
						foreach($value as $k => $v)
						{

							$_RETURN[$key]['row']['TOTAL_PESO_BRUTO'] = array_sum(array_values($v['TOTAL_PESO_BRUTO']));
							$_RETURN[$key]['row']['TOTAL_CUBAGEM']    = array_sum(array_values($v['TOTAL_CUBAGEM']));
							$_RETURN[$key]['row']['VALOR'] 		  	  = array_sum(array_values($v['VALOR']));
							$_RETURN[$key]['row']['TOTAL_EMBARCADOS'] = count($v['NOTAS']);
							$_RETURN[$key]['row']['TOTAL_NOTAS']	  = count($v['NOTAS']);
							
						}
						
					}
					
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
