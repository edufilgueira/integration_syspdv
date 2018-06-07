<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//DATA ATUAL DO SERVIDOR
$app->get('/funcao-suporte-data-servidor', function(Request $request, Response $response){
	echo date ("d/m/Y");
});




?>