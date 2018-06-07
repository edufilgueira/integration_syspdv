appAjusteEstoque.controller("requisicaoContagemItemController", function($scope, $http, $routeParams, $location){

	$scope.estoques=[];
	$scope.estoque={};
	$scope.grupos=[];
	$scope.secoes=[];
	
	$scope.requisicaoId = $routeParams.requisicaoId;
		
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
			$http({method:'GET', url:'syspdv/src/api.php/requisicao-produto/'+$scope.requisicaoId+"&"+$scope.estoque.secao.SECCOD+"&"+$scope.estoque.grupo.GRPCOD})
			.then(function (response){
				$scope.estoques=response.data;	
				$scope.sortColumn = "";				
			});
		}
		else $scope.consultar();
    }
	
	//LISTAR TODO O ESTOQUE
	$scope.consultar = function(){
		$http({method:'GET', url:'syspdv/src/api.php/requisicao-produto/'+$scope.requisicaoId})
		.then(function (response){
			$scope.estoques=response.data;	
			$scope.estoque.grupo = {};
		});
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
	
	$scope.adicionar = function(estoque, adicionar, numero){

		index = $scope.estoques.indexOf(estoque);
		texto = $scope.estoques[index].RQPQTD;
		valor = parseInt(texto);
		
		valor = (adicionar) ? valor + numero : valor - numero;
		valor = (valor >= 0) ? valor : 0;
		$scope.estoques[index].RQPQTD = valor;
		
		$scope.atualizarRequisicaoProduto(estoque.ID,valor);
	}
	
	$scope.adicionarText = function(estoque){

		index = $scope.estoques.indexOf(estoque);
		texto = $scope.estoques[index].RQPQTD;
		valor = parseInt(texto);

		$scope.estoques[index].RQPQTD = valor;
		$scope.atualizarRequisicaoProduto(estoque.ID,valor);
	}
	
	$scope.atualizarRequisicaoProduto = function(id, qtd){
		$http({method:'GET', url:'syspdv/src/api.php/requisicao-produto/'+id+"&"+qtd})
		.then(function (response){

		});
	}
	
	$scope.botaEnviarEnviar = function(){
		$location.path('requisicao-enviar/'+$scope.requisicaoId);
	}
	
	
});