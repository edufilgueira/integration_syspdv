appAjusteEstoque.controller("requisicaoEnviarItemController", function($scope, $http, $routeParams, $location){

	$scope.setores = [];
	$scope.locais = [];
	$scope.requisicao = {};
	$scope.requisicaoId = $routeParams.requisicaoId;

	//LISTAR TODO O SETOR
	$scope.buscarSetores = function(){
		$http({method:'GET', url:'syspdv/src/api.php/setor'})
		.then(function (response){
			$scope.setores=response.data;	
			if(response.data.length == 1)
				$scope.requisicao.REQSETCOD = {"SETCOD":response.data[0].SETCOD};
		});
    }
	
	//LISTAR TODO O LOCAL
	$scope.buscarLocais = function(){
		$http({method:'GET', url:'syspdv/src/api.php/local'})
		.then(function (response){
			$scope.locais=response.data;	
			if(response.data.length == 1)
			{
				$scope.requisicao.REQLOCORI = {"LOCCOD":response.data[0].LOCCOD};
				$scope.requisicao.REQLOCDES = {"LOCCOD":response.data[0].LOCCOD};
			}
			
		});
    }
	
	//ALTERAR O ESTOQUE
	$scope.salvar = function(){
		$http({
			method: 'POST',
			url: 'syspdv/src/api.php/requisicao',
			data: $scope.requisicao,
			headers: {'Content-Type': 'application/json'}
		}).then(function (response){
			console.log(response.data);
			$location.path('requisicao-enviar');
		})
    }
	
	//LISTAR TODO O ESTOQUE
	$scope.buscarPorId = function(){
		$http({method:'GET', url:'syspdv/src/api.php/requisicaoBuscarPorId/'+$scope.requisicaoId})
		.then(function (response){
			$scope.requisicao = response.data[0];
		});
    }
	
	$(document).ready(function() {
		$scope.buscarPorId();
		$scope.buscarSetores();
		$scope.buscarLocais();
		
	})
});