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

//SERVIÇO FILTRAR ESTOQUE POR SEÇÃO E GRUPO DE PRODUTO
$app->get('/estoque/{secao}&{grupo}&{dataini}', function($request, $response){
	
	$SECCOD = $request->getAttribute('secao');
	$GRPCOD = $request->getAttribute('grupo');
	$data = $request->getAttribute('dataini');
	$dataIni   = new DateTime($data);
	$dataIni   = $dataIni->format('Y-m-d');
	
    $sql = "SELECT PRODES, PRODUTO.PROCOD, ESTATU, PRODUTO.PROESTMIN,
			(SELECT COUNT(PROCOD) from REQUISICAO_PRODUTO as RP
			INNER JOIN REQUISICAO ON REQUISICAO.REQNUM = RP.REQNUM
			WHERE RP.PROCOD = ESTOQUE.PROCOD AND REQUISICAO.REQDATEMI='$dataIni') as REQPRO
			from ESTOQUE
			INNER JOIN PRODUTO ON PRODUTO.PROCOD = ESTOQUE.PROCOD
			WHERE PRODUTO.GRPCOD = '$GRPCOD' AND PRODUTO.SECCOD = '$SECCOD'
			order by ESTATU ASC";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		
		$query = ibase_query($dbh, $sql);
		
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['ID'] =  (int)trim($row['PROCOD']);
			$row['PROCOD'] = trim($row['PROCOD']);
			$row['ESTATU'] = number_format($row['ESTATU'], 2, ',', '.');	
			array_push($json_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);

        echo json_encode($json_data);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//SERVIÇO FILTRAR ESTOQUE POR SEÇÃO E GRUPO DE PRODUTO
$app->get('/estoque/{secao}&{grupo}', function($request, $response){
	
	$SECCOD = $request->getAttribute('secao');
	$GRPCOD = $request->getAttribute('grupo');
	
    $sql = "SELECT PRODES, PRODUTO.PROCOD, ESTATU, PRODUTO.PROESTMIN from ESTOQUE
			INNER JOIN PRODUTO ON PRODUTO.PROCOD = ESTOQUE.PROCOD
			WHERE PRODUTO.GRPCOD = '$GRPCOD' AND PRODUTO.SECCOD = '$SECCOD'
			order by ESTATU ASC";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		
		$query = ibase_query($dbh, $sql);
		
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['ID'] =  (int)trim($row['PROCOD']);
			$row['PROCOD'] = trim($row['PROCOD']);
			$row['ESTATU'] = number_format($row['ESTATU'], 2, ',', '.');	
			array_push($json_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);

        echo json_encode($json_data);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//LISTAR TODO ESTOQUE FILTRANDO POR DATA
$app->get('/estoque/{dataemi}', function($request, $response){
	$data = $request->getAttribute('dataemi');
	$dataemi     = new DateTime($data);
	$dataemi     = $dataemi->format('Y-m-d');
	
    $sql = "SELECT PRODES, PRODUTO.PROCOD, ESTATU, PRODUTO.PROESTMIN,
			(SELECT COUNT(PROCOD) from REQUISICAO_PRODUTO as RP
			INNER JOIN REQUISICAO ON REQUISICAO.REQNUM = RP.REQNUM
			WHERE RP.PROCOD = ESTOQUE.PROCOD AND REQUISICAO.REQDATEMI='$dataemi') as REQPRO
			from ESTOQUE
			INNER JOIN PRODUTO ON PRODUTO.PROCOD = ESTOQUE.PROCOD
			order by ESTATU ASC";
    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		
		$query = ibase_query($dbh, $sql);
		
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['ID'] =  (int)trim($row['PROCOD']);
			$row['PROCOD'] = trim($row['PROCOD']);
			$row['ESTATU'] = number_format($row['ESTATU'], 2, ',', '.');	
			array_push($json_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);

        echo json_encode($json_data);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//LISTAR TODO ESTOQUE
$app->get('/estoque', function($request, $response){
    $sql = 'SELECT PRODES, PRODUTO.PROCOD, ESTATU, PRODUTO.PROESTMIN from ESTOQUE 
			INNER JOIN PRODUTO ON PRODUTO.PROCOD = ESTOQUE.PROCOD
			order by ESTATU ASC';

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		
		$query = ibase_query($dbh, $sql);
		
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['ID'] =  (int)trim($row['PROCOD']);
			$row['PROCOD'] = trim($row['PROCOD']);
			$row['ESTATU'] = number_format($row['ESTATU'], 2, ',', '.');	
			array_push($json_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);

        echo json_encode($json_data);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//LISTAR TODO ESTOQUE PARA ACOMPANHAR PRODUÇÃO
$app->get('/estoque-acompanhar-producao/{dataemi}', function($request, $response){
	$data = $request->getAttribute('dataemi');
	$dataemi     = new DateTime($data);
	$dataemi     = $dataemi->format('Y-m-d');
	
    $sql = "SELECT ID, RPR.REQNUM, PRO.PROCOD, PRODES, RQPQTD, RQPQTDENT, GRU.GRPDES, GRU.GRPCOD
			FROM REQUISICAO_PRODUTO AS RPR
			INNER JOIN PRODUTO AS PRO ON PRO.PROCOD = RPR.PROCOD
			INNER JOIN GRUPO AS GRU ON GRU.GRPCOD = PRO.GRPCOD
			INNER JOIN SECAO AS SEC ON SEC.SECCOD = GRU.SECCOD
			INNER JOIN REQUISICAO AS REQ ON REQ.REQNUM = RPR.REQNUM
			WHERE REQ.REQDATEMI = '$dataemi' AND PRO.SECCOD = SEC.SECCOD
            ORDER BY GRU.GRPDES, PRODES";
    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['REQNUM'] = trim($row['REQNUM']);
			$row['PROCOD'] = trim($row['PROCOD']);
			$row['RQPQTD'] = number_format($row['RQPQTD'], 1, ',', '.');	
			$row['RQPQTDENT'] = number_format($row['RQPQTDENT'], 1, ',', '.');	
			array_push($json_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);
        echo json_encode($json_data);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});


//SERVIÇO BUSCAR QUANTIDADE ESTOQUE ATUAL POR IDPRODUTO
$app->get('/estoque-atual/{produto}', function(Request $request, Response $response){
    $PROCOD = $request->getAttribute('produto');
    $sql = "SELECT ESTATU, PRODUTO.PROESTMIN FROM ESTOQUE
			INNER JOIN PRODUTO ON PRODUTO.PROCOD = ESTOQUE.PROCOD
			WHERE ESTOQUE.PROCOD = '$PROCOD'; ";
    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['ESTATU'] = number_format($row['ESTATU'], 2, ',', '.');			
			array_push($json_data, $row);
		}
		ibase_free_result($query); 
		ibase_close($dbh);
		echo json_encode($json_data);
		
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//SERVIÇO ADICIONAR ESTOQUE
$app->post('/estoque', function(Request $request, Response $response){
	
	$data = $request->getParsedBody();
	
    $LOCCOD = $data['LOCCOD']['LOCCOD'];
    $PROCOD = $data['PROCOD']['PROCOD'];
    $AJUCOD = $data['AJUCOD'];
    $EAMQTD = $data['EAMQTD'];
	$EAMMTV = $data['EAMMTV'];
	$arr = array();
	$arr = buscarProdutoPorId($PROCOD);
	$EAMPROPRC1 = $arr[0]["PROPRCVDAVAR"];
	$EAMPROPRC2 = $arr[0]["PROPRCVDA2"];
	$EAMPROPRC3 = $arr[0]["PROPRCVDA3"];
    $EAMVLRCST = 0;
	$EAMDAT = date ("m/d/Y");
	$FUNCOD = "000001";
	$EAMCSTFISFSC = "0";
	$EAMCSTMEDFSC ="0";
	//echo $LOCCOD." ".$PROCOD." ".$AJUCOD." ".$EAMQTD." ".$EAMMTV." pr. ".$EAMPROPRC1." ".$EAMPROPRC2." ".$EAMPROPRC3;
	$EAMQTD = intval($EAMQTD);
	if($AJUCOD == "02")
		$EAMQTD = intval($EAMQTD)*(-1);
	
    $sql = "INSERT INTO ESTOQUE_AJUSTE_MOVIMENTACAO 
	(LOCCOD,PROCOD,AJUCOD,EAMQTD,EAMVLRCST,EAMPROPRC1,EAMPROPRC2,EAMPROPRC3,EAMDAT,FUNCOD,EAMMTV,EAMCSTFISFSC,EAMCSTMEDFSC) VALUES
    ('$LOCCOD','$PROCOD','$AJUCOD','$EAMQTD','$EAMVLRCST','$EAMPROPRC1','$EAMPROPRC2','$EAMPROPRC3','$EAMDAT','$FUNCOD','$EAMMTV','$EAMCSTFISFSC','$EAMCSTMEDFSC'); ";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
		salvarEstoqueMovimentacao($LOCCOD,$PROCOD,$EAMDAT,$EAMQTD,$EAMMTV);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});



//FUNÇÕES RELACIONADA A ESTOQUE--------------------------------------------------------------------------

function salvarEstoqueMovimentacao($LOCCOD,$PROCOD,$MOVDAT,$MOVQTD,$MOVMTV){
	$MOVDOC = "AJUSTE MANUAL";
	$FATCOD = "016";
	$FUNCOD = "000001";
	$MOVPRC = "N";
	$sql = "INSERT INTO ESTOQUE_MOVIMENTACAO
		(LOCCOD,PROCOD,MOVDAT,MOVQTD,MOVDOC,MOVMTV,FATCOD,FUNCOD,MOVPRC) values
		('$LOCCOD','$PROCOD','$MOVDAT','$MOVQTD','$MOVDOC','$MOVMTV','$FATCOD','$FUNCOD','$MOVPRC'); ";
	echo $sql;
	try{
        $dbh = new db();
        $dbh = $dbh->connect();
		$query = ibase_query($dbh, $sql);
        echo '{"notice": {"text": "Movimentação ok"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
}




/*
// Get Single Customer
$app->get('/api/customer/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');

    $sql = "SELECT * FROM customers WHERE id = $id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $customer = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customer);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});


// Update Customer
$app->put('/api/customer/update/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $first_name = $request->getParam('first_name');
    $last_name = $request->getParam('last_name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $address = $request->getParam('address');
    $city = $request->getParam('city');
    $state = $request->getParam('state');

    $sql = "UPDATE customers SET
				first_name 	= :first_name,
				last_name 	= :last_name,
                phone		= :phone,
                email		= :email,
                address 	= :address,
                city 		= :city,
                state		= :state
			WHERE id = $id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name',  $last_name);
        $stmt->bindParam(':phone',      $phone);
        $stmt->bindParam(':email',      $email);
        $stmt->bindParam(':address',    $address);
        $stmt->bindParam(':city',       $city);
        $stmt->bindParam(':state',      $state);

        $stmt->execute();

        echo '{"notice": {"text": "Customer Updated"}';

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Delete Customer
$app->delete('/api/customer/delete/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');

    $sql = "DELETE FROM customers WHERE id = $id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Customer Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});*/
?>