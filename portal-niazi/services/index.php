<?php

set_time_limit(0);

header('Access-Control-Allow-Origin: *');

//error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ALL);
ini_set("display_errors",1);

session_start();

require 'vendor/autoload.php';
require 'vendor/slim/slim/Slim/Slim.php';

require 'Models/Common.php';
require 'Models/Database.php';
require 'Models/Separacao.php';
require 'Models/Expedicao.php';
require 'Models/Consulta.php';
//require 'Models/ExpedicaoDois.php';
require 'Models/Conferencia.php';
require 'Models/Usuarios.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$authenticateForRole = function($role = ''){
    
    return function () use ( $role ) {

    	//print_r($_SESSION);
        
        if(!isset($_SESSION['auth']) && $role != '')
        {

        	$_RETURN['code'] = 500;
        	$_RETURN['redir'] = 'login';
        	$_RETURN['msg'] = 'Usuário não autenticado.';

        	header("Content-Type: application/json");
			echo json_encode($_RETURN);
			die();

        }else{

        	$go = false;
        	foreach($_SESSION['menu'] as $k => $v)
        	{

        		if($role == $v['slug'])
        			$go = 1;

        	}

        	if($go == 0)
        	{

        		$_RETURN['code'] = 500;
	        	$_RETURN['msg'] = 'Usuário sem permissão.';

	        	header("Content-Type: application/json");
				echo json_encode($_RETURN);
				die();

        	}

        }

    };

};

// CONSULTAS
$app->group('/consultas', $authenticateForRole('pedidos'), function() use ($app){

	$app->map('/',function () {

		if(!isset($_REQUEST['filter_q']) || !isset($_REQUEST['filter_where']))
		{

			$search = array('code' => 404, 'msg' => 'Selecione os filtros para consulta.');
			
			header("Content-Type: application/json");
			echo json_encode($search);

		}else{

		    $Consulta = new Consulta();

		    switch ($_REQUEST['filter_where']) {
		    	case 'por número de Cliente':
		    		$filter_where = 'NUM_PEDIDO';
		    		break;
		    	case 'por número da OS':
		    		$filter_where = 'NUM_OS';
		    		break;
		    	case 'por número da NF':
		    		$filter_where = 'NOTA_FISCAL';
		    		break;
		    }

		    $Consulta->filter_q = addslashes($_REQUEST['filter_q']);
		    $Consulta->filter_where = $filter_where;
		    
		    $search = $Consulta->search();

		    header("Content-Type: application/json");
			echo json_encode($search);

		}
		
	})->via('GET', 'POST');

});

// PEDIDOS
$app->group('/pedidos', $authenticateForRole('pedidos'), function() use ($app){

	$app->get('/',function () {
	        
	    $Separacao = new Separacao();
	    $search = $Separacao->search(array('tipo' => ''));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

	$app->get('/a-separar',function () {
	        
	    $Separacao = new Separacao();
	    $search = $Separacao->search(array('tipo' => 'a-separar'));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

	$app->get('/separados',function () {
	        
	    $Separacao = new Separacao();
	    $search = $Separacao->search(array('tipo' => 'separados', 'ref-date' => $_GET['ref-date']));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

	$app->get('/em-separacao',function () {
	        
	    $Separacao = new Separacao();
	    $search = $Separacao->search(array('tipo' => 'em-separacao'));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

});

// EXPEDICAO
$app->group('/expedicao', $authenticateForRole('expedicao'), function() use ($app){

	$app->get('/',function () {

	    $Expedicao = new Expedicao();
	    $search = $Expedicao->search(array('tipo' => ''));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

	$app->get('/a-embarcar',function () {
	        
	    $Expedicao = new Expedicao();
	    $search = $Expedicao->search(array('tipo' => 'a-embarcar'));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});
	/*
	$app->get('/a-embarcar2',function () {
	        
	    $Expedicao = new ExpedicaoDois();
	    $search = $Expedicao->search(array('tipo' => 'a-embarcar'));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});
	$app->get('/embarcados2',function () {
	        
	    $Expedicao = new ExpedicaoDois();
	    $search = $Expedicao->search(array('tipo' => 'embarcados', 'ref-date' => $_GET['ref-date']));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});
*/
	$app->get('/embarcados',function () {
	        
	    $Expedicao = new Expedicao();
	    $search = $Expedicao->search(array('tipo' => 'embarcados', 'ref-date' => $_GET['ref-date']));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

	$app->get('/embarcando',function () {
	        
	    $Expedicao = new Expedicao();
	    $search = $Expedicao->search(array('tipo' => 'embarcando'));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

});

// CONFERENCIA
$app->group('/conferencia', $authenticateForRole('conferencia'), function() use ($app){

	$app->get('/',function () {
	        
	    $Conferencia = new Conferencia();
	    $search = $Conferencia->search(array('tipo' => ''));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

	$app->get('/a-conferir',function () {
	        
	    $Conferencia = new Conferencia();
	    $search = $Conferencia->search(array('tipo' => 'a-conferir'));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

	$app->get('/conferidos',function () {
	        
	    $Conferencia = new Conferencia();
	    $search = $Conferencia->search(array('tipo' => 'conferidos', 'ref-date' => $_GET['ref-date']));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

	$app->get('/em-conferencia',function () {
	        
	    $Conferencia = new Conferencia();
	    $search = $Conferencia->search(array('tipo' => 'em-conferencia'));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

});

// USUÁRIOS
//retorna as permissoes do sistema
$app->get('/permissoes',function () {
        
    $Usuarios = new Usuarios();
    $permissoes = $Usuarios->permissoes();

    header("Content-Type: application/json");
	echo json_encode($permissoes);

});

//loga no sistema
$app->post('/login',function () {
    /*
    echo "<pre>";
    print_r($_POST);
    echo "<br>Request:";
    print_r($_REQUEST);
    echo "</pre>";
    */
    $Usuarios = new Usuarios();
    $login = $Usuarios->login(array('email' => $_POST['email'], 'pass' => $_POST['pass']));

    header("Content-Type: application/json");
	echo json_encode($login);

});

//desloga do sistema
$app->get('/logout',function () {
        
    $Usuarios = new Usuarios();
    $logout = $Usuarios->logout();

    header("Content-Type: application/json");
	echo json_encode($logout);

});

$app->group('/usuarios', $authenticateForRole('usuarios'), function() use ($app){

	//trocar senha
	$app->get('/troca-senha',function () {
	        
	    $Usuarios = new Usuarios();
	    $trocar_senha = $Usuarios->trocar_senha($_GET);

	    header("Content-Type: application/json");
		echo json_encode($trocar_senha);

	});

	//cria usuario
	$app->post('/',function () {
	        
	    $Usuarios = new Usuarios();
	    $createUser = $Usuarios->createUser($_POST);

	    header("Content-Type: application/json");
		echo json_encode($createUser);

	});

	//cria usuario
	$app->put('/',function () {
	        
	    $Usuarios = new Usuarios();
	    $updateUser = $Usuarios->updateUser($_POST);

	    header("Content-Type: application/json");
		echo json_encode($updateUser);

	});

	//remove usuario
	$app->delete('/',function () {
	        
	    $Usuarios = new Usuarios();
	    $removeUser = $Usuarios->removeUser(array('id' => $_POST['id']));

	    header("Content-Type: application/json");
		echo json_encode($removeUser);

	});

	//lista todos
	$app->get('/',function () {
	        
	    $Usuarios = new Usuarios();
	    $search = $Usuarios->search(array('tipo' => ''));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

	//lista especifico
	$app->get('/:id',function ($id) {
	        
	    $Usuarios = new Usuarios();
	    $search = $Usuarios->search(array('id' => $id));

	    header("Content-Type: application/json");
		echo json_encode($search);

	});

	

});


$app->run();
