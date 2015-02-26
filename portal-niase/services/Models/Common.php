<?php

class Common
{

	public function validaData($date)
	{

		//data somente com numeros, formato: yyyymmdd
		if(strlen($date) == 8){

			$date = substr($date,0,4)."/".substr($date,4,2)."/".substr($date,6,2);

		}

		$data = str_replace('/','-',trim($date));
		$ano = substr($data,0,-6);

		if(!is_numeric($ano)){
			
			$data = implode("-",array_reverse(explode("-",$data)));

		}

		$year  = substr($data,0,4);
		$month = substr($data,5,2);
		$day   = substr($data,8,2);

		if(checkdate($month, $day, $year)){

			$_RETURN['code'] = 200;
			$_RETURN['date'] = $data;
			$_RETURN['br_date'] = implode("/",array_reverse(explode("-",$data)));
			$_RETURN['year'] = $year;
			$_RETURN['month'] = $month;
			$_RETURN['day'] = $day;

		}else{

			$_RETURN['code'] = 500;

		}

		$_RETURN['posted'] = $date;

		return $_RETURN;

	}

	public function validaHora($hora)
	{

		//data somente com numeros, formato: yyyymmdd
		if(strlen($hora) == 6){

			$formatted = substr($hora,0,2).":".substr($hora,2,2).":".substr($hora,4,2);

			$_RETURN['code'] = 200;
			$_RETURN['formatted'] = $formatted;
			$_RETURN['hour'] = substr($hora,0,2);
			$_RETURN['minutes'] = substr($hora,2,2);
			$_RETURN['seconds'] = substr($hora,4,2);

		}

		$_RETURN['posted'] = $hora;

		return $_RETURN;

	}
	
}



