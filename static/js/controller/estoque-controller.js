appAjusteEstoque.controller("estoqueController", function($scope, $http, $timeout){

	$scope.estoques=[];
	$scope.estoque={};
	$scope.grupos=[];
	$scope.secoes=[];
	
	$http({method:'GET', url:'syspdv/src/api.php/secao'})
	.then(function (response){
		$scope.secoes=response.data;
	});
	
	//CARREGAR GRUPO PASSANDO UMA SESSAO
	$scope.consultarPorSecao = function(){
		if($scope.estoque.secao != null)
		{
			$http({method:'GET', url:'syspdv/src/api.php/grupo/'+$scope.estoque.secao.SECCOD})
			.then(function (response){
				$scope.grupos=response.data;
				$scope.sortColumn = "";
			});
		}
    }
	
	//LISTAR ESTOQUE POR SESSÃO E GRUPO
	$scope.consultarPorGrupo = function(){
		if($scope.estoque.grupo != null)
		{
			$http({method:'GET', url:'syspdv/src/api.php/estoque/'+$scope.estoque.secao.SECCOD+"&"+$scope.estoque.grupo.GRPCOD})
			.then(function (response){
				$scope.estoques=response.data;	
				$scope.sortColumn = "";				
			});
		}
    }
	
	//LISTAR TODO O ESTOQUE
	$scope.consultar = function(){
		$http({method:'GET', url:'syspdv/src/api.php/estoque'})
		.then(function (response){
			$scope.estoques=response.data;	
			$scope.estoque.grupo = {};
		});
    }
	
		
	//ORDENAR GRIDVIEW
	$scope.sortColumn = "";
	$scope.reverseSort = false;
	
	$scope.sortData = function(column){
		$scope.reverseSort = ($scope.sortColumn == column) ? !$scope.reverseSort : false;
		$scope.sortColumn = column;
	}
	
	$scope.getSortClass = function(column){
		if($scope.sortColumn == column)
			return $scope.reverseSort ? "glyphicon glyphicon-triangle-bottom" : "glyphicon glyphicon-triangle-top";
		return "";
	}
	
	
	//MARCAR COMO ESTOQUE MINIMO
	$scope.setEstoqueMin = function(estoque,estoqueMin){
		return (estoque <= estoqueMin) ? "celula-estoquemin texto-vermelho" : "";
	}
	
	//EXECUTAR APOS CARREGAMENTO DA PAGINA
	$(document).ready(function() {
		$scope.consultar();
		$scope.estoque.secao = {"SECCOD":'01'};
		$scope.consultarPorSecao();
	})
});