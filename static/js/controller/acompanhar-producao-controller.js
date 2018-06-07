appAjusteEstoque.controller("acompanharProducaoController", function($scope, $http, $location, $timeout){

	$scope.estoques=[];
	$scope.estoque={};
	$scope.data = {},
	$scope.requisicaoId = 0;
	$scope.requisicao = {};
	
	
	$http({method:'GET', url:'syspdv/src/api.php/funcao-suporte-data-servidor'})
	.then(function (response){
		$scope.data=response.data;
		$scope.abrirRequisicao();
	});
	
	$scope.abrirRequisicao = function (){
		if($scope.data != null)
		{	
			$http({method:'GET', url:'syspdv/src/api.php/requisicao/'+replaceAll($scope.data,"/","-")})
			.then(function (response){
				$scope.requisicaoId = response.data;
				$scope.requisicaoBuscarPorId();
			});	
			$scope.consultar();
			
		}
	}

	//BUSCAR REQUSICAO POR ID
	$scope.requisicaoBuscarPorId = function(){
		dataini = replaceAll($scope.data,"/","-");
		$http({method:'GET', url:'syspdv/src/api.php/requisicaoBuscarPorId/'+$scope.requisicaoId})
		.then(function (response){
			$scope.requisicao=response.data[0];	
		});
    }	
	
	//LISTAR TODO O ESTOQUE
	$scope.consultar = function(){
		dataini = replaceAll($scope.data,"/","-");
		$http({method:'GET', url:'syspdv/src/api.php/estoque-acompanhar-producao/'+dataini})
		.then(function (response){
			$scope.estoques=response.data;	
		});
    }
	
	//ATUALIZAR OS PRODUTOS ENTREGUA PELA PRODUÇÃO
	$scope.adicionar = function(estoque, adicionar, numero){

		index = $scope.estoques.indexOf(estoque);
		texto = $scope.estoques[index].RQPQTDENT;
		valor = parseInt(texto);
		
		valor = (adicionar) ? valor + numero : valor - numero;
		valor = (valor >= 0) ? valor : 0;
		$scope.estoques[index].RQPQTDENT = valor;
		
		$scope.atualizarRequisicaoProduto(estoque.ID,valor);
	}
	
	$scope.adicionarText = function(estoque){

		index = $scope.estoques.indexOf(estoque);
		texto = $scope.estoques[index].RQPQTDENT;
		valor = parseInt(texto);

		$scope.estoques[index].RQPQTDENT = valor;
		$scope.atualizarRequisicaoProduto(estoque.ID,valor);
	}
	
	$scope.atualizarRequisicaoProduto = function(id, qtd){
		$http({method:'GET', url:'syspdv/src/api.php/requisicao-producao-produto/'+id+"&"+qtd})
		.then(function (response){

		});
	}
	
	//ALTERNAR A FONTE DA COLUNA DO GRUPO
	$scope.fonteColuna = "";
	$scope.alterarFonteColunaGrupo = function(GRPDES){
		console.log("antigo:"+$scope.fonteColuna);
		console.log("atual:"+GRPDES);
		estilo = "";
		if($scope.fonteColuna != GRPDES)
			estilo = "fonteColuna1";
		$scope.fonteColuna = GRPDES;
		return estilo;
	}
	
	$scope.marcarVermelho = function(RQPQTDENT,RQPQTD){
		if(parseInt(RQPQTDENT) > 0)
		{
			if(parseInt(RQPQTDENT) < parseInt(RQPQTD))
				return "texto-amarelo fonte-bold";
			else
				return "texto-verde fonte-bold";
			
		}
		else
			return "texto-vermelho fonte-bold";
			
		//return  (parseInt(RQPQTDENT) > 0) ? "texto-verde fonte-bold" : "texto-vermelho fonte-bold";
	}
	
	$scope.exibirObservacao = function(){
		return  ($scope.requisicao.REQOBS != null) ? true : false;
	}
	
	//EXECUTAR APOS CARREGAMENTO DA PAGINA
	$(document).ready(function() {
	})
	
});