<?php

//Pegar todo o estoque ESTOQUE_AJUSTE_MOVIMENTACAO
$app->get('/setor', function($request, $response){
	echo json_encode(buscarTodos());
});

function buscarTodos(){
	
    $sql = "SELECT SETCOD,SETDES from SETOR";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		
		$query = ibase_query($dbh, $sql);
		$arr_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['SETCOD'] = trim($row['SETCOD']);
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