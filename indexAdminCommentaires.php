<?php
//On demarre les sessions
session_start();
?>

<DOCTYPE html>
    <html>
    <head><?php include("include/head.php"); ?></head>
<body>
    <?php include("include/headerAdmin.php"); ?>

<?php 
// DEBUG POST GET SESSION  
    // echo'POST : ';print_r($_POST);echo'<br/>GET : ';print_r($_GET);echo'<br/>SESSION : '; print_r($_SESSION);echo'<br/>'; 
    $auteurModifier='';
    $messageModifier='';
    $ajouterOuModifierCommenataire='Entrez un nouveau commentaire :';
    $boutonAjouterOuModifier='boutonAjouter';
    $idCommentaire=-1; // on initialise pour gérer l'ajout et modification
    $visible=1; // on initialise pour gérer l'ajout et modification
    $tousLesArticles=0;
  
// Récupération des variables par POST ou GET
    if (isset($_POST['visible'])) $visible=$_POST['visible'];
    if (isset($_GET['visible'])) $visible=$_GET['visible'];
    
    if (isset($_POST['debut'])) $debut=$_POST['debut'];
    if (isset($_GET['debut'])) $debut=$_GET['debut'];
     
    if (isset($_POST['tousLesArticles'])) $tousLesArticles=$_POST['tousLesArticles'];
    if (isset($_GET['tousLesArticles'])) $tousLesArticles=$_GET['tousLesArticles'];

    if (isset($_POST['idArticle'])) $idArticle=$_POST['idArticle'];
    if (isset($_GET['idArticle'])) $idArticle=$_GET['idArticle'];

    if (isset($_POST['idCommentaire'])) $idCommentaire=$_POST['idCommentaire'];
    if (isset($_GET['idCommentaire'])) $idArticle=$_GET['idCommentaire'];


// CAS $_SESSION['admin'] non setté -->
    if(!isset($_SESSION['admin'])){
        echo '<section>'; // pour une page formatée avec le CSS
        echo'<br/>vous n\'avez pas l\'autorisation d\'afficher cette page'; 
        // on affiche la structure de la page : section, footer
        echo '</section> </body> </html>'; include("include/footer.php"); 
        return;
    }

// CAS DELETE DU COMMENTAIRE : confirmation et rappel de la page -->
   if(isset($_POST['supprimer'])) {
        include("include/connexion.php"); // connexion à la BD
    
        $reqSQL='DELETE FROM Commentaires WHERE id = :id';
        $requete=$bdd->prepare($reqSQL);
        // echo '<pre> print_r : '; print_r($requete);echo '</pre>';
        $resultat=$requete->execute(array(
            'id'=>(int)$idCommentaire // il faut caster en (int) !!!
        )); // or die(print-r($bdd->errorInfo())) ;
        $requete->closeCursor(); ?>

        <section> <!-- pour une page formatée avec le CSS -->    

        <form action="indexAdminCommentaires.php" method="POST" >
            <fieldset>
                <?php if($requete->rowCount() ){ // pour tester le résultat : 0 si pas de DELETE
                    echo '<br/>Le commentaire n°'.$idCommentaire.' a été supprimé';
                } else {
                    echo '<br/>La suppression a échoué';
                } ?>
                <input type="hidden" name="idArticle" 
                    value=' <?php echo $idArticle; ?> '> 
                <input type="hidden" name="debut" 
                    value=' <?php echo $debut; ?>'> 
                <p><input type="submit" value="Valider"></p>

                <input type="hidden" name="idCommentaire" value=' <?php echo $idCommentaire; ?>'> 
                <input type="hidden" name="idArticle" value=' <?php echo $idArticle; ?>'>
                <input type="hidden" name="debut" value=' <?php echo $debut; ?>'>                    
                <input type="hidden" name="visible" value=' <?php echo $visible; ?>'> 
                <input type="hidden" name="tousLesArticles" value=' <?php echo $tousLesArticles; ?>'> 

            </fieldset>
        </form> 

        <?php // on affiche la structure de la page : section, footer
        echo '</section> </body> </html>'; include("include/footer.php"); 
        return;
    }

// CAS AFFICHER OU CACHER UN COMMENTAIRE : mise à jour de la BD et suite de la page -->
    if(isset($_POST['cacher']) OR isset($_POST['afficher']) ){
        include("include/connexion.php"); // connexion à la BD
        if(isset($_POST['cacher']) ){ 
            $visible=0;
            $CSSvisible=$_POST['cacher']; // pour la class de l'article pour le CSS 
        }
        else {
            $visible=1;             
        }
        $reqSQL='UPDATE Commentaires SET visible='.$visible.' WHERE id = :id';
        $requete=$bdd->prepare($reqSQL);
        // echo '<pre> print_r : '; print_r($requete);echo '</pre>';
        $resultat=$requete->execute(array(
            'id'=>(int)$_POST['idCommentaire'] // il faut caster en (int) !!!
        )); // or die(print-r($bdd->errorInfo())) ;
        $requete->closeCursor(); 
    }

// INSERT d'un nouveau commentaire dans la BD : si on a tous les champs -->
    if( 
        isset($_POST['boutonAjouter']) AND        
        isset($_POST['auteur']) AND 
        isset($_POST['message']) AND 
        isset($_POST['idArticle']) AND 
        ltrim($_POST['auteur'])!='' AND 
        ltrim($_POST['message'])!='' 
    ) // REMARQUE : si les champs sont à vide, on enregistre quand même le tuple dans la BD
    // le NOT NULL ne sert à rien puisqu'on passe une valeur à '', ce qui n'est pas NULL
    {
        include("include/connexion.php"); // connexion à la BD
        // echo 'POST: idArticle:'.$_POST['idArticle'].' -auteur:'. $_POST['auteur'].' -message:'.$_POST['message'];
        $reqSQL='INSERT INTO commentaires (idArticle, auteur, contenu, dateCommentaire, visible) VALUES(?, ?, ?, CURRENT_TIMESTAMP,1)';       
        $requete = $bdd->prepare($reqSQL);
        $requete->execute(array((int)$_POST['idArticle'], $_POST['auteur'], $_POST['message']));
        $requete->closeCursor();
    }

// UPDATE d'un nouveau commentaire dans la BD : si on a tous les champs -->
    if( 
        isset($_POST['boutonModifier']) AND        
        isset($_POST['auteur']) AND 
        isset($_POST['message']) AND 
        isset($_POST['idArticle']) AND 
        ltrim($_POST['auteur'])!='' AND 
        ltrim($_POST['message'])!='' 
    ) // REMARQUE : si les champs sont à vide, on enregistre quand même le tuple dans la BD
    // le NOT NULL ne sert à rien puisqu'on passe une valeur à '', ce qui n'est pas NULL
    {
        include("include/connexion.php"); // connexion à la BD
        // echo 'POST: idArticle:'.$_POST['idArticle'].' -auteur:'. $_POST['auteur'].' -message:'.$_POST['message'];
        $reqSQL=
        'UPDATE commentaires
        SET auteur=?, contenu=? 
        WHERE id=?
        ';      
        $requete = $bdd->prepare($reqSQL);
        $requete->execute(array($_POST['auteur'], $_POST['message'], (int)$idCommentaire));
        $requete->closeCursor();
    }

// CAS UPDATE DU COMMENTAIRE : affichage du formulaire de saisie -->
    if(isset($_POST['modifier'])) {
        include("include/connexion.php"); // connexion à la BD
    
        $reqSQL='SELECT id, auteur, contenu, visible, dateCommentaire FROM Commentaires WHERE id = :id';
        $requete=$bdd->prepare($reqSQL);
        echo '<pre> print_r : '; print_r($requete);echo '</pre>'; echo 'idCommentaire: '.$idCommentaire.'<BR/>';
        $resultat=$requete->execute(array(
            'id'=>(int)$idCommentaire // il faut caster en (int) !!!
        )); // or die(print-r($bdd->errorInfo())) ;
        $ligne=$requete->fetch();
        $requete->closeCursor(); 
        //echo '<pre>'; print_r($ligne);echo '</pre>';
        $auteurModifier=$ligne['auteur'];
        $messageModifier=$ligne['contenu']; 
        $ajouterOuModifierCommenataire='Modifier le commentaire n°'.$ligne['id'];
        $boutonAjouterOuModifier='boutonModifier';
    } 

?>

<!-- VUE HTML -->
        
<!-- LIEN vers la liste des articles -->
    <p><a href="indexAdminArticles.php?debut=<?php echo $debut ?>&amp;tousLesArticles=<?php echo $tousLesArticles ?>">Retour à la liste des articles</a></p>  

<!-- AFFICHAGE DE L'ARTICLE -->
    <?php include("include/connexion.php"); // connexion à la BD                

    $reqSQL='
        SELECT id, titre, contenu, visible, 
        DATE_FORMAT(dateCreation, \'%d/%m/%Y à %Hh%imin%ss\') AS dateCreationFR 
        FROM articles 
        WHERE id ='.$idArticle;
        // WHERE id = ?'; Version à ?
    $requete = $bdd->prepare($reqSQL);
    $requete->execute(array());
    // $requete->execute(array($idArticle)); pour la version à ?
    // print_r($requete); echo'<br/>';
    $ligne = $requete->fetch();
    $requete->closeCursor(); ?> 

    <div class="commentaires">
        <h3>
            <?php echo 
                htmlspecialchars($ligne['titre']).' - id: '.
                htmlspecialchars($ligne['id']). ' - visible: '.
                htmlspecialchars($ligne['visible']); ?>
        </h3>
        <p class="date">le <?php echo $ligne['dateCreationFR']; ?></p>               
        <p>
            <?php echo nl2br(htmlspecialchars($ligne['contenu'])); ?>
        </p>
    </div>

<!-- AFFICHAGE DES COMMENTAIRES -->
    <h2>Commentaires</h2>             

    <?php $reqSQL='
        SELECT id, auteur, contenu, visible, 
        DATE_FORMAT(dateCommentaire, \'%d/%m/%Y à %Hh%imin%ss\') AS dateCommentaireFR 
        FROM commentaires 
        WHERE idArticle = ? ORDER BY dateCommentaire DESC
    ';
    $requete = $bdd->prepare($reqSQL);
    $requete->execute(array($idArticle));
    // print_r($requete); echo'<br/>';  

    while ($ligne = $requete->fetch())
    { 
        // print_r($ligne); echo'<br/>';  
        if($ligne['visible']) echo '<div class="adminCommentaire">'; // class avec couleur fonction de visible
        else echo '<div class="adminCommentaireCacher">';  
        ?>
            <p>
                <strong><?php echo htmlspecialchars($ligne['auteur']); ?></strong> 
                le <?php echo 
                    $ligne['dateCommentaireFR'].' - id: '.
                    htmlspecialchars($ligne['id']). ' - visible: '.
                    htmlspecialchars($ligne['visible']); ?> 
            </p>
            <p><?php echo nl2br(htmlspecialchars($ligne['contenu'])); ?></p>
            <form action="indexAdminCommentaires.php?idArticle=<?php echo $idArticle ?>" method="POST">
                <input type="submit" name="supprimer" value="supprimer">
                <input type="submit" name="modifier" value="modifier">
                <input type="submit" name="cacher" value="cacher">
                <input type="submit" name="afficher" value="afficher">

                <input type="hidden" name="idCommentaire" value=' <?php echo $ligne['id']; ?>'> 
                <input type="hidden" name="idArticle" value=' <?php echo $idArticle; ?>'>
                <input type="hidden" name="debut" value=' <?php echo $debut; ?>'>                    
                <input type="hidden" name="visible" value=' <?php echo $visible; ?>'> 
                <input type="hidden" name="tousLesArticles" value=' <?php echo $tousLesArticles; ?>'> 
            </form>
        </div>
    <?php } // fin de la boucle des commentaires
    $requete->closeCursor(); // fermeture des requêtes ?> 
 
 <!-- saisie d'un nouveau commentaire -->
    <h2><?php echo $ajouterOuModifierCommenataire ?></h2>
    <form action="indexAdminCommentaires.php" method="POST">
    <p>
        <label for="auteur">Auteur</label> : <input type="text" name="auteur" value="<?php echo $auteurModifier ?>" id="auteur" /><br />
        <label for="message">Message</label> : <input type="text" name="message" value="<?php echo $messageModifier ?>" id="message" /><br />

        <input type="submit" value="Valider" name="<?php echo $boutonAjouterOuModifier ?>"/>
        <input type="submit" value="Annuler" />

        <input type="hidden" name="idCommentaire" value=' <?php echo $idCommentaire; ?>'> 
        <input type="hidden" name="idArticle" value=' <?php echo $idArticle; ?>'>
        <input type="hidden" name="debut" value=' <?php echo $debut; ?>'>                    
        <input type="hidden" name="visible" value=' <?php echo $visible; ?>'> 
        <input type="hidden" name="tousLesArticles" value=' <?php echo $tousLesArticles; ?>'> 
    </p>
    </form>             

<!-- FIN DE LA PAGE HTML -->
<?php include("include/footer.php"); ?>
</body>
</html>