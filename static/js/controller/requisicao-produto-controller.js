appAjusteEstoque.controller("requisicaoProdutoController", function($scope, $http, $timeout){

	$scope.requisicoes=[];
	$scope.requisicao={};

	//LISTAR TODO O ESTOQUE
	$scope.consultar = function(){
		$http({method:'GET', url:'syspdv/src/api.php/requisicao'})
		.then(function (response){
			$scope.requisicoes=response.data;	
		});
    }
	
	//LISTAR TODO O ESTOQUE
	$scope.excluir = function(requisicao){
		index = $scope.requisicoes.indexOf(requisicao);//
		$scope.requisicoes.splice(index, 1);
		
		$http({method:'GET', url:'syspdv/src/api.php/requisicao-excluir/'+requisicao.REQNUM})
		.then(function (response){
			console.log(response.data);
		});
    }

	$(document).ready(function() {
		$scope.consultar();
	})
});