<?php
    class db{
        // Properties
        private $dbhost = 'localhost:C:/syspdv_srv2.fdb';
        private $dbuser = 'SYSDBA';
        private $dbpass = 'masterkey';
        

        // Connect
        public function connect(){
            /*$connect_str = "firebird:dbname=$this->dbhost";
            $dbConnection = new PDO($connect_str, $this->dbuser, $this->dbpass);
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbConnection;*/
			
			$servidor = 'localhost:C:/syspdv_srv.fdb';
			if (!($dbh=ibase_connect($servidor, 'SYSDBA', 'masterkey', 'utf8')))
			die('Erro ao conectar: ' . ibase_errmsg());

			$dbh=ibase_connect($servidor, 'SYSDBA', 'masterkey','utf8');
			
			return $dbh;
        }
    }