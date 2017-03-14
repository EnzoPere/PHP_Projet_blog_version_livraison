<DOCTYPE html>
<html>
    <head><?php include("include/head.php"); ?></head>      
    <body>
        <?php include("include/headerAdmin.php"); ?>
		<section>
			<?php
			// CAS INSERTION DE L'ARTICLE
			if(isset($_POST['titre']) AND isset($_POST['texte']) ){ 
				include("include/connexion.php");
			
				$reqSQL=
				'INSERT INTO Articles (id, titre, contenu, dateCreation)
				VALUES (NULL, :titre, :texte, CURRENT_TIMESTAMP)';

				$requete=$bdd->prepare($reqSQL);
				// echo '<pre> print_r : '; print_r($requete);echo '</pre>';
				$resultat=$requete->execute(array(
					'titre'=>$_POST['titre'], 
					'texte'=>$_POST['texte'], 
				)); // or die(print-r($bdd->errorInfo())) ;
				$requete->closeCursor();
			?>
				<form action="indexAdminArticles.php" method="POST" >
					<fieldset>
						<?php if($resultat){
							echo '<br/>Le INSERT a été effectué';

						} else {
							echo '<br/>Le INSERT a échoué';
						} ?>
						<p><input type="submit" value="Valider"></p>
					</fieldset>
				</form>					
			<?php 
				return;
			}			
			?>

			<!-- CAS AFFICHAGE DU FORMULAIRE DE SAISIE DE L'ARTICLE -->
			<p><a href="indexAdminArticles.php">Retour aux articles</a></p>  

			<form action="indexAdminNouvelArticle.php" method="POST" >
				<fieldset>
					<legend>Saisie d'un nouvel article</legend>

					<p><label for="titre">Titre de l'article</label>
					<input type="text" name="titre" id="titre" placeholder="titre"></p>

					<textarea name="texte" id="texte" rows="25" cols="120"
					>Saisissez votre article = value</textarea>
					
				  <p><input type="submit" value="Valider"></p>
				</fieldset>
			</form>
						
		</section>		
        <?php include("include/footer.php"); ?>
    </body>
</html>
