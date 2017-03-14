<?php
//On demarre les sessions
session_start();
?>

<DOCTYPE html>
<html>
    <head><?php include("include/head.php"); ?></head>     
<body>
<?php include("include/headerAdmin.php"); ?>
		 
<!-- INITIALISATION des variables -->
	<?php  // echo'POST : ';print_r($_POST);echo'<br/>SESSION : '; print_r($_SESSION);echo'<br/>GET : ';print_r($_GET);echo'<br/>';              
	$CSSvisible=''; // pour la class de l'article pour le CSS : '' article visible
	$nbMessages=3;
    $step=1; // la marche de montée et de descente
    $debut=0; // on affiche de debut à nbMessages-1
    $tousLesArticles=0; // on n'affiche pas tous les articles
    // echo 'CSSvisible: '.$CSSvisible.' - nbMessages: '.$nbMessages.' - step: '.$step.' - debut: '.$debut.' - tousLesArticles: '.$tousLesArticles;echo'<br/>';  
	?>

<!-- GESTION du mot de passe : si on entre avec le bon admin password, on set le $_SESSION['admin'] -->
	<?php if(isset($_POST['password']) AND htmlspecialchars($_POST['password'])=='admin'
	AND isset($_POST['admin']) AND htmlspecialchars($_POST['admin'])=='admin'){
		$_SESSION['admin'] = $_POST['admin'];
	}

	if(!isset($_SESSION['admin'])) {
		echo '<section>'; 
		echo'<br/>vous n\'avez pas l\'autorisation d\'afficher cette pageee'; 
		echo '</section>'; 
		include("include/footer.php");
		echo '</body> </html>';
		return;
	}?>		

<!-- Connexion à la BD : ça servira plus tard -->
    <?php include("include/connexion.php"); ?>

<!-- CAS DELETE DE L'ARTICLE : confirmation et rappel de la page -->
	<?php if(isset($_POST['supprimer'])) {
		$debut=$_POST['debut'];
	
		$reqSQL='DELETE FROM Articles WHERE id = :id';
		$requete=$bdd->prepare($reqSQL);
		// echo '<pre> print_r : '; print_r($requete);echo '</pre>';
		$resultat=$requete->execute(array(
			'id'=>(int)$_POST['id'] // il faut caster en (int) !!!
		)); // or die(print-r($bdd->errorInfo())) ;
		?>

		<section> 
		<form action="indexAdminArticles.php" method="POST" >
		<fieldset>
			<?php 
			if($requete->rowCount() ){ // pour tester le résultat : 0 si pas de DELETE
				echo '<br/>L\'article n°'.$_POST['id'].' a été supprimé';
			} else {
				echo '<br/>La suppression a échoué';
			} ?>
			<p><input type="submit" value="Valider"></p>
            <input type="hidden" name="debut" value='
                <?php // debut c'est la valeur poster de $debut, le $debut qui circule de page en page 
                    echo $debut; 
                ?>'>
		</fieldset>
		</form>					
		<?php $requete->closeCursor(); 
		echo '</section>'; 
		include("include/footer.php");
		echo '</body> </html>';
		return;	
	} ?>	

<!--  CAS AFFICHER OU CACHER DE L'ARTICLE : mise à jour de la BD et suite de la page -->
	<?php if(isset($_POST['cacher']) OR isset($_POST['afficher']) ){
		include("include/connexion.php"); // connexion à la BD
		$debut=$_POST['debut']; 
		$tousLesArticles=$_POST['tousLesArticles'];
		if(isset($_POST['cacher']) ){ 
			$visible=0;
			$CSSvisible=$_POST['cacher']; // pour la class de l'article pour le CSS	
		}
		else {
			$visible=1;				
		}
		$reqSQL='UPDATE  Articles SET visible='.$visible.' WHERE id = :id';
		$requete=$bdd->prepare($reqSQL);
		//echo '<pre> print_r : '; print_r($requete);echo '</pre>';
		$resultat=$requete->execute(array(
			'id'=>(int)$_POST['id'] // il faut caster en (int) !!!
		)); // or die(print-r($bdd->errorInfo())) ;
		$requete->closeCursor();
	} ?>

<!--  CAS UPDATE DE L'ARTICLE : confirmation et rappel de la page -->
	<?php if(isset($_POST['validerUpdate']) AND isset($_POST['valider'])) { // il faut les deux pour que l'annuler ne fasse rien
		$debut=$_POST['debut'];
		include("include/connexion.php"); // connexion à la BD
	
		$reqSQL='UPDATE Articles SET titre=:titre, contenu=:contenu WHERE id = :id';
		$requete=$bdd->prepare($reqSQL);
		// echo '<pre> print_r : '; print_r($requete);echo '</pre>';
		$resultat=$requete->execute(array(
			'titre'=>$_POST['titre'], // il faut caster en (int) !!!
			'contenu'=>$_POST['contenu'], // il faut caster en (int) !!!
			'id'=>(int)$_POST['id'] // il faut caster en (int) !!!
		)); // or die(print-r($bdd->errorInfo())) ;
		?>
		
		<section> 
		<form action="indexAdminArticles.php" method="POST" >
			<fieldset>
				<?php 
				if($requete->rowCount() ){ // pour tester le résultat : 0 si pas de DELETE
					echo '<br/>L\'article n°'.$_POST['id'].' a été modifié';
				} else {
					echo '<br/>La modification a échoué';
				} ?>
				<p><input type="submit" value="Valider"></p>
	            <input type="hidden" name="debut" value='
	                <?php // debut c'est la valeur poster de $debut, le $debut qui circule de page en page 
	                    echo $debut; 
	                ?>'>
			</fieldset>
		</form>					
		<?php $requete->closeCursor();
		echo '</section>'; 
		include("include/footer.php");
		echo '</body> </html>';
		return;	
	} ?>

<!--  CAS UPDATE DE L'ARTICLE : affichage du formulaire de saisie -->
	<?php if(isset($_POST['modifier'])) {
		$debut=$_POST['debut'];
		include("include/connexion.php"); // connexion à la BD
	
		$reqSQL='SELECT id, titre, contenu, visible, dateCreation FROM Articles WHERE id = :id';
		$requete=$bdd->prepare($reqSQL);
		// echo '<pre> print_r : '; print_r($requete);echo '</pre>';
		$resultat=$requete->execute(array(
			'id'=>(int)$_POST['id'] // il faut caster en (int) !!!
		)); // or die(print-r($bdd->errorInfo())) ;
		$ligne=$requete->fetch();
		$requete->closeCursor(); 
		//echo '<pre>'; print_r($ligne);echo '</pre>'; 
		?>
		
		<section> 
		<form action="indexAdminArticles.php" method="POST" >
			<fieldset>
				<legend>Modification de l'article n° <?php echo $ligne['id']; ?> </legend>

				<p><label for="titre">Titre de l'article</label>
					<input type="text" name="titre" id="titre"
						value=<?php echo '"'.$ligne['titre'].'"'; ?>
					>
				</p>

				<!-- le texte doit être collé juste après le > : sinon les espaces s'ajoutent dans le texte -->
				<textarea name="contenu" id="contenu" rows="25" cols="120"><?php echo $ligne['contenu']; ?>
				</textarea>
				
			  <p><input type="submit" value="valider" name="validerUpdate">
			  <input type="submit" value="annuler" name="annulerrUpdate"></p>	
	      	  <input type="hidden" name="id" value='
	                <?php // id de l'article
	                    echo $ligne['id']; 
	                ?>'>
	          <input type="hidden" name="debut" value='
	                <?php // debut c'est la valeur poster de $debut, le $debut qui circule de page en page 
	                    echo $debut; 
	                ?>'>					  				
			</fieldset>
		</form>				
		<?php echo '</section>'; 
		include("include/footer.php");
		echo '</body> </html>';
		return;	
	} ?>



<!--  CAS GENERAL : le $_SESSION['admin'] est setté. C'est le cas sinon on a fait un return -->
	<section><article> 
<!-- MISE à jour de $tousLesArticles : à faire avant la gestion des boutons haut, bas, etc. -->
<!-- ENTREE POST par BOUTON boutonTousArticles ou par hidden sur haut, bas, monter descendre ou ENTREE par le retour GET des commentaires -->
    <?php if(
        ( isset($_POST['tousLesArticles']) AND $_POST['tousLesArticles']==1 ) OR 
        ( isset($_GET['tousLesArticles'])  AND $_GET['tousLesArticles']==1 ) OR
        isset($_POST['boutonTousLesArticles']) 
    ) $tousLesArticles=1;
    else $tousLesArticles=0;
    // echo 'tousLesArticles : '.$tousLesArticles;echo'<br/>'; ?>    

<!-- ENTREE par BOUTONS haut, bas, monter, descendre : POST -->
<!-- ENTREE par RETOUR de commentaires : GET -->
<!-- il faut redonner la bonne valeur à $debut -->
    <?php // si on revient d'un commmentaire : c'est par GET
    if(isset($_GET['debut'])) $debut=$_GET['debut'];

    //  si on a cliqué sur début, fin, suivant, précédent :
    //  $debut, c'est l'indice du premier message à afficher dans la table : utilisé dans le SELECT    
    //  on récupère la valeur du $debut de la page d'appel dans $_POST['debut']
    //  on va calculer la nouvelle valeur de $debut selon les cas 

     // si on veut monter tout en haut, $debut repasse à 0 (le haut est à 0)
    if(isset($_POST['haut']) ){ $debut=0; $tousLesArticles=0; }
    // on veut monter d'un step : aller vers 0 en décrémentant debut de step
    // si la décrémentation reste >=0 on la fait, sinon debut passe à 0
    elseif(isset($_POST['monter']) AND $tousLesArticles==0 ) {
        if ($_POST['debut']-$step>=0) $debut=$_POST['debut']-$step;
        else $debut=0;
    }
    // si on veut descendre ou aller tout en bas, il faut le nombre total de messages
    elseif(isset($_POST['bas']) OR (isset($_POST['descendre']) AND !$tousLesArticles )){ 
        $tousLesArticles=0;             
        $reqSQL='SELECT count(*) as countMessages FROM articles';
        $requete=$bdd->query($reqSQL);
        $ligne=$requete->fetch();
        $countMessages=$ligne['countMessages'];
        $requete->closeCursor();

        // par défaut, $debut reste à $_POST['debut'] : si le $_POST['debut'] était déjà en bas (proche de countMEssage)
        $debut=$_POST['debut'];

        // on veut descendre d'un step : aller vers countMessage en incrémentant $_POST['debut'] de step
        // si l'incrémentation reste < à countmessage on la fait (sinon on ne fait rien et on reste à debut post)
        // si c'est descendre, on in incremente de step
        // si c'est bas, on passe à countmessage -nbMEssage
        if($_POST['debut']+$nbMessages < $countMessages){
            // si c'est descendre, on augemnte de step
            if(isset($_POST['descendre']) ) $debut=$_POST['debut']+$step;
            // sinon c'est tout en bas, on passe à countmessage - nbmessages
            else $debut=$countMessages-$nbMessages;
        }
    } ?>

<!-- MISE à jour de LIMIT -->
<!-- ENTREE POST par BOUTON tous les articles ou ENTREE par le retour GET des commentaires -->
    <?php if($tousLesArticles) $limit='';
    else $limit='LIMIT ' .$debut. ', ' .$nbMessages;
    // echo 'limit: '.$limit; echo'<br/>'; 
    ?>  

<!-- AFFICHAGE DE LA PAGE  : d'abord les boutons -->
    <form action="indexAdminArticles.php" method="POST">
    <p> Circuler dans les articles du blog :
        <input type="submit" name="haut" value="haut">
        <input type="submit" name="descendre" value="descendre">
        <input type="submit" name="monter" value="monter">
        <input type="submit" name="bas" value="bas">
        <input type="submit" name="boutonTousLesArticles" value="Tous les articles">
        <input type="hidden" name="debut" 
            value=' <?php  echo $debut; // debut circule de page en page ?>'>
         <input type="hidden" name="tousLesArticles" 
            value=' <?php  echo $tousLesArticles; // debut circule de page en page ?>'>      
     </p>
    </form>

    <form action="indexAdminNouvelArticle.php" method="POST">
        <p> Ecrire un article :
            <input type="submit" name="Nouvel article" value="nouvelArticle">
            <!-- // début repasse à 0 si on écrit un nouvel article -->
        </p>
    </form>
	  
    <?php $reqSQL='SELECT id, titre, contenu, visible, dateCreation FROM articles ORDER BY dateCreation DESC '.$limit;
    $requete=$bdd->query($reqSQL);
    // print_r($requete);
    // Affichage de chaque message - htmlspecialchars pour nettoyer les données si ça n'avait pas été fait
    while($ligne=$requete->fetch()){ // echo'<pre>';print_r($ligne);echo'</pre>'; ?>
	    <div class="toutArticle">
	        <h3>
	            <?php echo 
	            	htmlspecialchars($ligne['titre']).' - id: '.
	            	htmlspecialchars($ligne['id']). ' - visible: '.
	            	htmlspecialchars($ligne['visible']); ?>
	        </h3>
	       	
	       	<?php 
	       	if($ligne['visible']) echo '<div class="articleEtDate">'; // class avec couleur fonction de visible
	        else echo '<div class="articleEtDateCacher">'; 
	       	?>    
		        <p class="dateArticle"><em><?php 
	                // AFFICHAGE DE LA DATE
	                // Version avec format MySQL
	                // on bricole la date pour retirer les secondes
	                // echo substr('<br/>'.$ligne['date_creation_fr'],0,strlen($ligne['date_creation_fr']) -3); 
	                
	                // Version sans format MySQL
	                $date=date_create($ligne['dateCreation']); // on crée un objet date
	                echo date_format($date,'d M Y').'<br/>';
	                // équivalent  à echo $date->format('d M Y').'<br/>';
	                // pas de solution simple pour afficher la date en français
	                // une solution par MySQL et un DECODE
	                echo date_format($date,'H:i:s');
		        ?></em></p>

		        <p class="article"> <?php 
		            // AFFICHAGE DU CONTENU DU BILLET
		            // nl2br : new ligne to br : transforme les passages à la ligne du texte en <br> html
		            echo nl2br(htmlspecialchars($ligne['contenu']));

		            // AFFICHAGE DU NOMBRE DE COMMENTAIRES
		            $reqSQL2='
		                SELECT count(*) as nbCommentaires FROM commentaires 
		                WHERE idArticle=' .$ligne['id']
		            ;

		            $requete2=$bdd->query($reqSQL2);
		            $ligne2=$requete2->fetch();
					?>
		            <em><a href="indexAdminCommentaires.php?idArticle=<?php echo $ligne['id'] ?>
		            	&amp;debut=<?php echo $debut ?>
		            	&amp;tousLesArticles=<?php echo $tousLesArticles ?>">
		                <br/> <?php echo $ligne2['nbCommentaires']?> Commentaires
		            </a></em>
	        	</p> 
	        </div>                     
	    </div> <!-- class="toutArticle" -->

	    <form action="indexAdminArticles.php" method="post">
            <input type="submit" name="supprimer" value="supprimer">
            <input type="submit" name="modifier" value="modifier">
            <input type="submit" name="cacher" value="cacher">
            <input type="submit" name="afficher" value="afficher">
            <input type="hidden" name="tousLesArticles" value='
                <?php // Est-ce qu'on affiche tous les articles
                    echo $tousLesArticles; 
                ?>'>				         
            <input type="hidden" name="id" value='
                <?php // id de l'article
                    echo $ligne['id']; 
                ?>'>
            <input type="hidden" name="debut" value='
                <?php // debut c'est la valeur poster de $debut, le $debut qui circule de page en page 
                    echo $debut; 
                ?>'>
	    </form> <?php
    } // Fin de la boucle des billets

    // echo 'debut : '.$debut ;// DEBUG
    $requete->closeCursor(); ?>
	</section>		
<?php include("include/footer.php"); ?>
</body>
</html>
