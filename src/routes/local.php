<?php




//Pegar todo o estoque ESTOQUE_AJUSTE_MOVIMENTACAO
$app->get('/local', function($request, $response){
    $sql = "SELECT LOCCOD,LOCDES,LOCSTATUS,LOCEXPFIS from LOCAL where LOCSTATUS = 'A'";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		
		$query = ibase_query($dbh, $sql);
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['LOCCOD'] = trim($row['LOCCOD']);
			$row['LOCSTATUS'] = trim($row['LOCSTATUS']);
			$row['LOCEXPFIS'] = trim($row['LOCEXPFIS']);
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