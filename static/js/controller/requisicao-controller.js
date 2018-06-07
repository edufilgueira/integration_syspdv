appAjusteEstoque.controller("requisicaoController", function($scope, $http, $location, $timeout){

	$scope.estoques=[];
	$scope.estoque={};
	$scope.grupos=[];
	$scope.secoes=[];
	$scope.data = {},
	$scope.exibirListaProdutos = false;
	$scope.exibirBotaoEnviarRequisicao = true;
	$scope.requisicaoId = 0;
	
	$http({method:'GET', url:'syspdv/src/api.php/funcao-suporte-data-servidor'})
	.then(function (response){
		$scope.data=response.data;
	});
	
	$scope.abrirRequisicao = function (){
		if($scope.data != null)
		{	
			$http({method:'GET', url:'syspdv/src/api.php/requisicao/'+replaceAll($scope.data,"/","-")})
			.then(function (response){
				$scope.requisicaoId = response.data;
				$scope.exibirBotaoEnviarRequisicao = false;
			});	
			$scope.exibirListaProdutos = true;
			$scope.consultar();
			
		}
		else
			$scope.exibirListaProdutos = false;
	}
	
	
	$http({method:'GET', url:'syspdv/src/api.php/secao'})
	.then(function (response){
		$scope.secoes=response.data;
	});
	
	//SELECIONAR PRODUTO PARA REQUISIÇÃO
	$scope.selecionarProduto = function(PROCOD){
		data = replaceAll($scope.data, "/", "-");
		var check = document.getElementById(PROCOD);
		
		acao = (check.checked == true) ? "ADD":"DEL";
		
		$http({method:'GET', url:'syspdv/src/api.php/requisicao/'+data+"&"+PROCOD+"&"+acao})
		.then(function (response){
			console.log(response.data);
		});
			
    }
	
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
			$http({method:'GET', url:'syspdv/src/api.php/estoque/'+$scope.estoque.secao.SECCOD+"&"+$scope.estoque.grupo.GRPCOD+"&"+replaceAll($scope.data,"/","-")})
			.then(function (response){
				$scope.estoques=response.data;	
				$scope.sortColumn = "";				
			});
		}
		else $scope.consultar();
    }
	
	//LISTAR TODO O ESTOQUE
	$scope.consultar = function(){
		dataini = replaceAll($scope.data,"/","-");
		$http({method:'GET', url:'syspdv/src/api.php/estoque/'+dataini})
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
	
	//MARCAR CHECKBOX QUANDO ENCONTRA O PRODUTO REQUISITADO NA TABELA REQUISIÇÃO_PRODUTO
	$scope.setCheckboxEstoque = function(REQPRO){
		return (REQPRO > 0) ? true : false;
	}
	
	$scope.marcarLinhaEstoque = function(REQPRO){
		return (REQPRO > 0) ? "marcar-linha-estoque" : "";
	}
	
	//EXECUTAR APOS CARREGAMENTO DA PAGINA
	$(document).ready(function() {
		$scope.estoque.secao = {"SECCOD":'01'};
		$scope.consultarPorSecao();
	})
	$scope.botaEnviarRequisicao = function(){
		$location.path('requisicao-contagem/'+$scope.requisicaoId);
	}
	

	

});