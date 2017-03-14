<?php
	// paramètres de connexion
	$host='localhost';
	$sgbdname='mysql';
	$username = 'root';
	$password = 'root';
	$charset='utf8';

	$dbname='projet_blog';
	
	// dsn : data source name
	$dsn = $sgbdname.':host='.$host.';dbname='.$dbname.';charset='.$charset;

	// pour avoir des erreurs SQL plus claires 
	$erreur = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

	try {
	    $bdd = new PDO($dsn, $username, $password, $erreur);
	} catch (PDOException $e) {
	    die ('Connexion échouée : ' . $e->getMessage() );
	}
