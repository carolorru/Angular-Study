<?php
class Consulta
{

	public $Database;
	public $Common;
	
	public function __construct()
	{

		// Dados do banco
		$this->Database = new Database;
		$this->Common = new Common;

	}

	public function search()
	{

		$sel = "SELECT * FROM (
								select '0001' as COD_CLI,
									   'NIAZI CHOHFI' as NOME_CLI,
									   '050333' as NUM_PEDIDO,
									   '06/10/2015' as DATA_PEDIDO,
									   '066000' as NUM_OS,
									   '10/10/2015' as DATA_OS,
									   '047000' as NOTA_FISCAL,
									   '11/10/2015' as DATA_NF,
									   '10/10/2015' as INI_SEP,
									   'JOAO' as SEPARADOR,
									   '10/10/2015' as FIM_SEP,
									   '11/10/2015' as INI_CONF,
									   'FLAVIO' as CONFERENTE,
									   '11/10/2015' as FIM_CONF,
									   '11/10/2015' as DATA_PESAGEM,
									   'GORDAO' as PESADOR,
									   '12/10/2015' as DATA_EMBARQUE,
									   'ROBSON' as EMBARCADOR,
									   '20/10/2010' as DATA_ENTREGA,
									   '15:00:00' as HORA_ENTREGA,
									   '01-Entrega Realizada Normalmente' as OCORRENCIA,
									   'Recebido por Vitoria' as INF_COMPL,
									   'GRAN CARGO' as TRANSPORTADORA
							) Z
				WHERE ".$this->filter_where."  = '".$this->filter_q."'";

		$query = $this->Database->doQuery($sel);

		if($query > 0)
		{
			
			$num = $this->Database->num_rows($query);

			if($num > 0){

				$_RETURN['num'] = $num;
				$_RETURN['code'] = 200;

				while($row = $this->Database->fetch_array($query))
			    {

			    	$_RETURN['row'][] = array(
			    							'COD_CLI'  	     => $row['COD_CLI'],
			    							'NOME_CLI' 	     => $row['NOME_CLI'],
			    							'NUM_PEDIDO'     => $row['NUM_PEDIDO'],
			    							'DATA_OS' 	     => $row['DATA_OS'],
			    							'NUM_OS' 	     => $row['NUM_OS'],
			    							'DATA_PEDIDO'    => $row['DATA_PEDIDO'],
			    							'NOTA_FISCAL'    => $row['NOTA_FISCAL'],
			    							'DATA_NF' 	     => $row['DATA_NF'],
			    							'INI_SEP'	     => $row['INI_SEP'],
			    							'SEPARADOR'      => $row['SEPARADOR'],
			    							'FIM_SEP' 	     => $row['FIM_SEP'],
			    							'INI_CONF' 	     => $row['INI_CONF'],
			    							'CONFERENTE'     => $row['CONFERENTE'],
			    							'FIM_CONF'       => $row['FIM_CONF'],
			    							'DATA_PESAGEM'   => $row['DATA_PESAGEM'],
			    							'PESADOR' 		 => $row['PESADOR'],
			    							'DATA_EMBARQUE'  => $row['DATA_EMBARQUE'],
			    							'EMBARCADOR' 	 => $row['EMBARCADOR'],
			    							'DATA_ENTREGA' 	 => $row['DATA_ENTREGA'],
			    							'HORA_ENTREGA' 	 => $row['HORA_ENTREGA'],
			    							'OCORRENCIA' 	 => $row['OCORRENCIA'],
			    							'INF_COMPL' 	 => $row['INF_COMPL'],
			    							'TRANSPORTADORA' => $row['TRANSPORTADORA'],
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
			$_RETURN['error'] = $this->Database->dbError();
			$_RETURN['msg'] = 'Erro na query.';

		}

		return $_RETURN;
		
	}
	
}

