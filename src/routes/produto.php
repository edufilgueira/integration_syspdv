<?php

//SERVIÇO LISTA TODOS OS PRODUTOS (ID, DESCRIÇÃO)
$app->get('/produto', function($request, $response){
    $sql = "SELECT PROCOD, PRODES from PRODUTO";

    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		
		$query = ibase_query($dbh, $sql);
		$json_data = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			$row['ID'] =  (int)trim($row['PROCOD']);
			$row['PROCOD'] = trim($row['PROCOD']);
			array_push($json_data, $row);
		}

		ibase_free_result($query); 
		ibase_close($dbh);

        echo json_encode($json_data);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});


//FUNÇÕES RELACIONADA A PRODUTO--------------------------------------------------------------------------

function buscarProdutoPorId($PROCOD){
	$sql = "SELECT * from PRODUTO where PROCOD = '$PROCOD'";
    try{
        $dbh = new db();
        $dbh = $dbh->connect();
		
		$query = ibase_query($dbh, $sql);
		$arr = array(); 
		while ($row = ibase_fetch_assoc($query)) {
			array_push($arr, $row);
		}
		
		ibase_free_result($query); 
		ibase_close($dbh);
		return $arr;
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
}

?>