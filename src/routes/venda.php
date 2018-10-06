<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

//RETORNA O VALOR DE UMA VENDA
$app->get('/venda_valor/{codigo}', function($request, $response){
	
	$CODIGO = $request->getAttribute('codigo');
	
    $sql = "SELECT FIRST 1 TRNVLR FROM TRANSACAO WHERE TRNSEQ = '$CODIGO'";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		
		$query = ibase_query($dbh, $sql);
		
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {	
			array_push($json_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);

        echo json_encode($json_data);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//RETORNA O VALOR DE UMA VENDA
$app->get('/venda_produtos/{codigo}', function($request, $response){
	
	$CODIGO = $request->getAttribute('codigo');
	
    $sql = "SELECT IVD.TRNSEQ, PRO.PRODES, PRO.PROUNID, IVD.ITVQTDVDA, IVD.ITVVLRTOT FROM ITEVDA IVD
			INNER JOIN PRODUTO PRO ON PRO.PROCOD = IVD.PROCOD
			WHERE TRNSEQ = '$CODIGO' AND ITVTIP = 1";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		
		$query = ibase_query($dbh, $sql);
		
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {	
			$row['TRNSEQ'] = trim($row['TRNSEQ']);
			$row['PROUNID'] = trim($row['PROUNID']);
			array_push($json_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);

        echo json_encode($json_data);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
?>
