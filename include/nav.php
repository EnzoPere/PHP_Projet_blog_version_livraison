<nav id="nav">
	<ul>
        <li><a href="indexArticles.php">Blog</a></li>
        <li><a href="indexCV.php">CV</a></li>
        <li><a href="indexEXOS.php">Exercices</a></li>

    </ul>
    
	<form id="admin" action="indexAdminArticles.php" method="POST" >
		<input type="text" name="admin" id="password" placeholder="admin = admin">
		<input type="password" name="password" id="password" placeholder="password=admin">
		<input type="submit" value="ok">
	</form>	

	<form id="deconnexion" action="indexArticles.php" method="POST" >
		<input type="submit" name="deconnexion" value="deconnexion">
	</form>	
</nav>