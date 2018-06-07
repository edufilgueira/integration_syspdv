<?php





//ADICIONAR PRODUTO PARA REQUISIÇÃO
$app->get('/requisicao/{data}&{produtoid}&{acao}', function($request, $response){
	$data         = $request->getAttribute('data');
	$produtoID = $request->getAttribute('produtoid');
	$acao         = $request->getAttribute('acao');
	$dataIni     = new DateTime($data);
	$dataIni     = $dataIni->format('Y-m-d');
	
	$produto = array();
	$produto = buscarProdutoPorId($produtoID);
	$precoVenda = $produto[0]["PROPRCVDAVAR"];
	
	$requisicaoID = salvarRequisicaoPorData($dataIni);

	if($acao == "ADD")
		salvarRequisicaoProduto($requisicaoID,$produtoID,$precoVenda);
	else if($acao == "DEL")
		deletarRequisicaoProduto($requisicaoID,$produtoID);
	
});

//SALVAR CABEÇALHO DA REQUISIÇÃO
$app->get('/requisicao/{data}', function($request, $response){
	$data = $request->getAttribute('data');
	$dataIni   = new DateTime($data);
	$dataIni   = $dataIni->format('Y-m-d');
	$requisicaoID = salvarRequisicaoPorData($dataIni);
	echo $requisicaoID;
});

//PEGA O CABEÇALHO DAS 100 ULTIMAS REQUISIÇÕES
$app->get('/requisicao', function($request, $response){
   echo json_encode(listarUltimasRequisicoes());
});

//EXCLUIR REQUISIÇÃO
$app->get('/requisicao-excluir/{id}', function($request, $response){
	$REQNUM = $request->getAttribute('id');
	excluirRequisicao($REQNUM);
});

//BUSCAR POR ID
$app->get('/requisicaoBuscarPorId/{id}', function($request, $response){
	$REQNUM = $request->getAttribute('id');
	echo json_encode(buscarRequisicaoPorId($REQNUM));
});

//EDITAR REQUISIÇÃO
$app->post('/requisicao', function($request, $response){
	$dados = $request->getParsedBody();

	$REQNUM = $dados['REQNUM'];
    $REQMODELO = $dados['REQMODELO'];
	$REQTIPO = $dados['REQTIPO'];
	$REQLOCORI = $dados['REQLOCORI']['LOCCOD'];
	$REQLOCDES = $dados['REQLOCDES']['LOCCOD'];
	$REQFUNCODSOL = '000001';
	

	$data = str_replace("/","-",$dados['REQDATEMI']);
	$dataIni     = new DateTime($data);
	$dataIni   = $dataIni->format('Y-m-d');
	$REQDATEMI = $dataIni;
	
	$REQOBS = $dados['REQOBS'];
	$REQSTATUS = "C";
	$REQSETCOD = $dados['REQSETCOD']['SETCOD'];

	echo finalizarRequisicao($REQNUM,$REQMODELO,$REQTIPO,$REQLOCORI,$REQLOCDES,$REQFUNCODSOL,$REQDATEMI,$REQOBS,$REQSTATUS,$REQSETCOD);
});


//FUNÇÕES DA REQUISICAO--------------------------------------------------------------------------
//ENVIAR A FINALIZAÇÃO PARA PRODUÇÃO
function finalizarRequisicao($REQNUM,$REQMODELO,$REQTIPO,$REQLOCORI,$REQLOCDES,$REQFUNCODSOL,$REQDATEMI,$REQOBS,$REQSTATUS,$REQSETCOD)
{	
	$sql = "UPDATE REQUISICAO SET 
	REQMODELO='$REQMODELO', REQTIPO='$REQTIPO', REQLOCORI='$REQLOCORI', REQLOCDES='$REQLOCDES', 
	REQFUNCODSOL='$REQFUNCODSOL', REQDATEMI='$REQDATEMI', REQOBS='$REQOBS', REQSTATUS='$REQSTATUS', REQSETCOD='$REQSETCOD'
	WHERE REQNUM = '$REQNUM'";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		return "Alterado com sucesso";
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
}
//SALVAR CABEÇALHO DA REQUISIÇÃO
function salvarRequisicaoPorData($dataIni){
	$requisicaoID = 0;
	$requisicaoID = existeRequisicao($dataIni);
	if($requisicaoID == "null")
		$requisicaoID = salvarRequisicao($dataIni);
	return $requisicaoID;
}

//BUSCAR POR ID
function buscarRequisicaoPorId($REQNUM){
	$sql = "SELECT * from REQUISICAO WHERE REQNUM = '$REQNUM'";
	try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		$arr_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$data = new DateTime($row['REQDATEMI']);
			$row['REQDATEMI'] = $data->format('d/m/Y');
			
			$row['ID'] =  (int)trim($row['REQNUM']);
			$row['REQNUM'] = trim($row['REQNUM']);
			$row['REQTIPO'] = trim($row['REQTIPO']);
			$row['REQMODELO'] = trim($row['REQMODELO']);
			$row['REQMODELO'] = trim($row['REQMODELO']);
			$row['REQLOCDES'] = trim($row['REQLOCDES']);
			$row['REQFUNCODSOL'] = trim($row['REQFUNCODSOL']);
			$row['REQSTATUS'] = trim($row['REQSTATUS']);
			$row['REQSETCOD'] = trim($row['REQSETCOD']);
			$row['REQLOCORI'] = trim($row['REQLOCORI']);
			$row['REQSTATUS'] = ($row['REQSTATUS'] == "C") ? "OK" : "?";
			array_push($arr_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);

        return $arr_data;
	} catch(PDOException $e){
		echo '{"error": {"text": '.$e->getMessage().'}';
	}	
}

//CADASTRAR CABEÇALJO DA REQUISIÇÃO
function listarUltimasRequisicoes(){
	$sql = "SELECT FIRST 100 REQ.*,
			(SELECT COUNT(*) FROM REQUISICAO_PRODUTO AS RPR WHERE RPR.REQNUM = REQ.REQNUM) AS QTDPRO
			from REQUISICAO AS REQ ORDER BY REQDATEMI DESC;";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$data = new DateTime($row['REQDATEMI']);
			$row['REQDATEMI'] = $data->format('d/m/Y');
			
			$row['ID'] =  (int)trim($row['REQNUM']);
			$row['REQNUM'] = trim($row['REQNUM']);
			$row['REQTIPO'] = trim($row['REQTIPO']);
			$row['REQMODELO'] = trim($row['REQMODELO']);
			$row['REQMODELO'] = trim($row['REQMODELO']);
			$row['REQLOCDES'] = trim($row['REQLOCDES']);
			$row['REQFUNCODSOL'] = trim($row['REQFUNCODSOL']);
			$row['REQSTATUS'] = trim($row['REQSTATUS']);
			$row['REQSETCOD'] = trim($row['REQSETCOD']);
			$row['REQLOCORI'] = trim($row['REQLOCORI']);
			$row['REQSTATUS'] = ($row['REQSTATUS'] == "C") ? "OK" : "?";
			array_push($json_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);

        return $json_data;
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }	
	
}

//BUSCAR PROXIMO ID
function maxRequisicaoId(){
	$sql = "SELECT max(REQNUM) as ID FROM REQUISICAO;";
	try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		$ID = 0; 
		while ($row = ibase_fetch_assoc($query)) {
			$ID = (int)$row['ID'];
			$ID +=1;
			$ID = str_pad($ID, 6, "0", STR_PAD_LEFT);
		}
		ibase_free_result($query); 
		ibase_close($dbh);
		return $ID;
	} catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
}

function excluirRequisicao($REQNUM){
	$sql = "DELETE FROM REQUISICAO WHERE REQNUM = '$REQNUM'";
	//echo $sql;
    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
}

function salvarRequisicao($REQDATEMI){
	$maxId = maxRequisicaoId();

	$sql = "INSERT INTO REQUISICAO (REQNUM,REQDATEMI,REQSTATUS) VALUES ('$maxId','$REQDATEMI','A'); ";
	//echo $sql;
    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
        return $maxId;
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
}

function salvarRequisicaoProduto($REQNUM,$PROCOD,$RQPPRCVDA){
	
    $sql = "INSERT INTO REQUISICAO_PRODUTO 
	(REQNUM,PROCOD,RQPPRCVDA) VALUES
    ('$REQNUM','$PROCOD','$RQPPRCVDA'); ";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }	
}

function deletarRequisicaoProduto($REQNUM,$PROCOD){
	
    $sql = "DELETE FROM REQUISICAO_PRODUTO 
			WHERE REQNUM = $REQNUM AND PROCOD = $PROCOD; ";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }	
}

function existeRequisicao($dataIni){
	$dataIni = new DateTime($dataIni);
	$dataIni = $dataIni->format('Y-m-d');

	$sql = "SELECT FIRST 1 * FROM REQUISICAO
			WHERE  REQDATEMI = '$dataIni' ;";	
	try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		
		$id = null;
		while($row = ibase_fetch_assoc($query)) {
			$id = $row['REQNUM'];
		}
		ibase_free_result($query); 
		ibase_close($dbh);
		return ($id != null) ? trim($id) : "null";
	} catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
}

//VERIFICAR SE EXISTE REQUISIÇÃO PRODUTO JA CADASTRADO
function existeRequisicaoProduto($REQNUM,$PROCOD){
	
    $sql = "SELECT REQNUM REQUISICAO_PRODUTO 
			WHERE REQNUM = '$REQNUM' AND PROCOD = '$PROCOD'; ";
echo $sql;
	try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		
		$id = null;
		while($row = ibase_fetch_assoc($query)) {
			$id = $row['REQNUM'];
		}
		ibase_free_result($query); 
		ibase_close($dbh);
		return ($id != null) ? trim($id) : "null";
	} catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
}


?>