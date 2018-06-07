//Criacao do modulo principal da aplicacao
var appAjusteEstoque = angular.module("appAjusteEstoque", [ 'ngRoute' ]);

appAjusteEstoque.config(function($routeProvider, $locationProvider) {

	$routeProvider
	.when("/estoque", {
		templateUrl : './syspdv/static/view/estoque.html',
		controller : 'estoqueController'
	}).when("/estoque/:produtoId", {
		templateUrl : './syspdv/static/view/estoque-detalhe.html',
		controller : 'estoqueDetalheController'
	}).when("/requisicao", {
		templateUrl : './syspdv/static/view/requisicao.html',
		controller : 'requisicaoController'
	}).when("/requisicao-contagem", {
		templateUrl : './syspdv/static/view/requisicao-contagem.html',
		controller : 'requisicaoContagemController'
	}).when("/requisicao-contagem/:requisicaoId", {
		templateUrl : './syspdv/static/view/requisicao-contagem-item.html',
		controller : 'requisicaoContagemItemController'
	}).when("/requisicao-enviar", {
		templateUrl : './syspdv/static/view/requisicao-enviar.html',
		controller : 'requisicaoEnviarController'
	}).when("/requisicao-enviar/:requisicaoId", {
		templateUrl : './syspdv/static/view/requisicao-enviar-item.html',
		controller : 'requisicaoEnviarItemController'
	}).when("/acompanhar-producao", {
		templateUrl : './syspdv/static/view/acompanhar-producao.html',
		controller : 'acompanharProducaoController'
	}).otherwise({
		rediretTo : '/'
	});
	
	//$locationProvider.html5Mode(true);
	
});