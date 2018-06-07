<?php
//BUSCAR TODOS OS PRODUTOS REQUISITADOS
$app->get('/requisicao-produto/{requisicaiId}&{secao}&{grupo}', function($request, $response){
	$REQNUM = $request->getAttribute('requisicaiId');
	$secao = $request->getAttribute('secao');
	$grupo = $request->getAttribute('grupo');
	echo json_encode(buscarProdutosPorRequisicaoIdSecaoGrupo($REQNUM,$secao,$grupo));
});

//ATUALIZAR A QUANTIDADE DA REQUISIÇÃO
$app->get('/requisicao-produto/{id}&{qtd}', function($request, $response){
	$ID  = $request->getAttribute('id');
	$QTD = $request->getAttribute('qtd');
	echo atualizarRequisicaoProduto($ID, $QTD);
});


//ATUALIZAR A QUANTIDADE DA REQUISIÇÃO NA PRODUÇÃO
$app->get('/requisicao-producao-produto/{id}&{qtd}', function($request, $response){
	$ID  = $request->getAttribute('id');
	$QTD = $request->getAttribute('qtd');
	echo atualizarProducaoRequisicaoProduto($ID, $QTD);
});


//BUSCAR TODOS OS PRODUTOS REQUISITADOS
$app->get('/requisicao-produto/{requisicaiId}', function($request, $response){
	$REQNUM = $request->getAttribute('requisicaiId');
	echo json_encode(buscarProdutosPorRequisicaoId($REQNUM));
});




//FUNÇÕES DA REQUISICAO PRODUTO------------------------------------------------------------------

function atualizarRequisicaoProduto($ID, $QTD){
	$sql = "UPDATE REQUISICAO_PRODUTO SET RQPQTD = '$QTD' WHERE ID = '$ID'";
	
	try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		echo "Produto requisição adicionado.";
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }

}

function atualizarProducaoRequisicaoProduto($ID, $QTD){
	$sql = "UPDATE REQUISICAO_PRODUTO SET RQPQTDENT = '$QTD' WHERE ID = '$ID'";
	
	try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		echo "Produto requisição adicionado.";
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }

}

function buscarProdutosPorRequisicaoId($REQNUM){

	$sql = "SELECT ID, REQNUM, RPR.PROCOD, PRO.PRODES, PRO.PROESTMIN, RQPPRCVDA, RQPQTD,GRP.GRPDES,
			(SELECT ESTATU FROM ESTOQUE AS EST WHERE EST.PROCOD = RPR.PROCOD ) as ESTATU
			FROM REQUISICAO_PRODUTO RPR
			INNER JOIN PRODUTO PRO ON PRO.PROCOD = RPR.PROCOD
			INNER JOIN GRUPO AS GRP ON GRP.GRPCOD = PRO.GRPCOD
			INNER JOIN SECAO AS SEC ON SEC.SECCOD = GRP.SECCOD
            WHERE PRO.SECCOD = SEC.SECCOD AND  REQNUM = '$REQNUM'
            ORDER BY PRO.GRPCOD, PRO.PRODES";
    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		$arr_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['REQNUM'] = trim($row['REQNUM']);
			$row['PROCOD'] = trim($row['PROCOD']);
			$row['ESTATU'] = number_format($row['ESTATU'], 1, ',', '.');
			$row['RQPQTD'] = number_format($row['RQPQTD'], 0, ',', '.');
			array_push($arr_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);

        return $arr_data;
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }	
}

function buscarProdutosPorRequisicaoIdSecaoGrupo($REQNUM,$SECCOD,$GRPCOD){

	$sql = "SELECT ID, REQNUM, RPR.PROCOD, PRO.PRODES, PRO.PROESTMIN, RQPPRCVDA, RQPQTD,GRP.GRPDES,
			(SELECT ESTATU FROM ESTOQUE AS EST WHERE EST.PROCOD = RPR.PROCOD ) as ESTATU
			FROM REQUISICAO_PRODUTO RPR
			INNER JOIN PRODUTO PRO ON PRO.PROCOD = RPR.PROCOD
			INNER JOIN GRUPO AS GRP ON GRP.GRPCOD = PRO.GRPCOD
			INNER JOIN SECAO AS SEC ON SEC.SECCOD = GRP.SECCOD
            WHERE PRO.SECCOD = SEC.SECCOD AND  REQNUM = '$REQNUM' AND SEC.SECCOD = '$SECCOD' AND GRP.GRPCOD = '$GRPCOD'
            ORDER BY PRO.GRPCOD, PRO.PRODES";
    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		$arr_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['REQNUM'] = trim($row['REQNUM']);
			$row['PROCOD'] = trim($row['PROCOD']);
			$row['ESTATU'] = number_format($row['ESTATU'], 1, ',', '.');
			$row['RQPQTD'] = number_format($row['RQPQTD'], 0, ',', '.');
			array_push($arr_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);

        return $arr_data;
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }	
}
?>