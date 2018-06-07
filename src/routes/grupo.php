<?php

//Pegar todo o estoque ESTOQUE_AJUSTE_MOVIMENTACAO
$app->get('/grupo/{secao}', function($request, $response){
	$SECCOD = $request->getAttribute('secao');
    $sql = "SELECT GRPCOD, GRPDES from GRUPO
			WHERE SECCOD = $SECCOD";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		
		$query = ibase_query($dbh, $sql);
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['GRPCOD'] = trim($row['GRPCOD']);
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