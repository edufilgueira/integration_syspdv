<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require 'config/db.php';

$app = new \Slim\App;


// Customer Routes
require 'routes/estoque.php';
require 'routes/local.php';
require 'routes/produto.php';
require 'routes/grupo.php';
require 'routes/secao.php';
require 'routes/requisicao.php';
require 'routes/requisicao-produto.php';
require 'routes/funcao-suporte.php';
require 'routes/setor.php';
require 'routes/venda.php';

$app->run();
