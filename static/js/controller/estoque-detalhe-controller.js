appAjusteEstoque.controller("estoqueDetalheController", function ($scope, $routeParams, $http){
	
	
	$scope.estoque= {};
	$scope.desabilitarBotao = $routeParams.produtoId == 0 ? true : false;
	$scope.locais = [];
	$scope.produtos = [];
	$scope.estoqueAual = {};
	$scope.desabilitarTxtObservacao = false;
	
	//$scope.estoque.EAMMTV = "ADD";
	
	//CARREGAR SELECT LOCAL
	$http.get('syspdv/src/api.php/local')
	.then(function (response){
		$scope.locais= response.data;
		if(response.data.length == 1)
				$scope.estoque.LOCCOD = {"LOCCOD":response.data[0].LOCCOD};
	}, function (response){
		console.log(response);	
	});
	
	//CARREGAR SELECT PRODUTO
	$http.get('syspdv/src/api.php/produto').then(function (response){
		$scope.produtos= response.data;
		$scope.estoque.PROCOD = {"PROCOD":$routeParams.produtoId};
	}, function (response){
		console.log(response);	
	});

	//BUSCAR ITEM DO ESTOQUE POR ID
	$scope.buscarEstoquePorId = function(){
		$http.get('syspdv/src/api.php/estoque-atual/'+$routeParams.produtoId).then(function (response){
			$scope.estoqueAual = response.data[0].ESTATU;
			$scope.estoqueMinimo = response.data[0].PROESTMIN;
		}, function (response){
			console.log(response);	
		});
	}
	
	$scope.novo = function (){
		$scope.desabilitarBotao = false;
	}
	
	$scope.cancelar = function (){
		$scope.estoque={};
		$scope.desabilitarBotao = true;
		$scope.frmEstoque.$setPristine(true);
	}
	
	$scope.salvar = function(){
		if ($scope.frmEstoque.$valid)
		{
			/*$http({method:'POST', url:'estoque/src/api.php/estoque/',data:$scope.estoque})
			.then(function (response){
				$scope.cancelar();
				$scope.frmEstoque.$setPristine(true);
				console.log(response.data);	
			})*/
			
			$http({
				method: 'POST',
				url: 'syspdv/src/api.php/estoque',
				data: $scope.estoque,
				headers: {'Content-Type': 'application/json'}
			}).then(function (response){
				$scope.cancelar();
				$scope.frmEstoque.$setPristine(true);
				console.log(response.data);	
			})
								
		}else {
			window.alert("Dados inválidos!");
		}
	}
	
	$scope.adicionar = function(adicionar, numero){
		
		texto = document.getElementById('txtQuantidade').value == "" ? 0 : document.getElementById('txtQuantidade').value;
		valor = parseInt(texto);
		if(adicionar)
			valor += numero;
		else
			valor -= numero;
		if(valor >= 0)
		{
			document.getElementById('txtQuantidade').value = valor;
			$scope.estoque.EAMQTD = valor.toString();
		}
		else
			document.getElementById('txtQuantidade').value = 0;
		
		if(valor > 0)
			$scope.frmEstoque.txtQuantidade.$setValidity('required', true);
		else
			$scope.frmEstoque.txtQuantidade.$setValidity('required', false);
	}
	
	$scope.mostrarTxtObservacao = function(exibir){
		if(exibir)
		{
			$scope.desabilitarTxtObservacao = true;
			$scope.frmEstoque.txtObservacao.$setValidity('required', false);
		}
		else
		{
			$scope.desabilitarTxtObservacao = false;
			$scope.frmEstoque.txtObservacao.$setValidity('required', true);
		}
	}
	
	$scope.estoqueAlerta = function(){
		if($scope.estoqueAual > $scope.estoqueMinimo)
			return "alert alert-success";
		else
			return  "alert alert-danger";
	}
	
	$(document).ready(function() {
		//AGUARDAR O CARREGAMENTO DA PAGINA
		if($routeParams.produtoId > 0)
			$scope.buscarEstoquePorId();
	});	
	
} );