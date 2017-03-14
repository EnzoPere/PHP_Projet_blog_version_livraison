<?php
//On demarre les sessions
session_start();
?>
<DOCTYPE html>
<html>
    <head><?php include("include/head.php"); ?></head>     
<body>
<?php include("include/header.php"); ?>
<section class="section">
    
<article> 
<!-- CALCULS D'ENTREE DANS LA PAGE -->
    <?php // echo'DEBUG : POST : ';print_r($_POST);echo'<br/>SESSION : '; print_r($_SESSION);echo'<br/>GET : ';print_r($_GET);echo'<br/>'; ?>           

<!-- INITIALISATION  des variables -->
    <?php if (isset($_POST['debut'])) $debut=$_POST['debut'];
    if (isset($_GET['debut'])) $debut=$_GET['debut'];

    if (isset($_POST['idArticle'])) $idArticle=$_POST['idArticle'];
    if (isset($_GET['idArticle'])) $idArticle=$_GET['idArticle'];

    if (isset($POST['tousLesArticles'])) $tousLesArticles=$POST['tousLesArticles'];
    if (isset($_GET['tousLesArticles'])) $tousLesArticles=$_GET['tousLesArticles'];
    // echo 'XXX idArticle à l entrée : ' . $idArticle.'<br/>';?>  

<!-- Connexion à la BD : ça servira plus tard -->
    <?php include("include/connexion.php"); ?>

<!-- INSERT d'un nouveau commentaire dans la BD : si on a tous les champs -->
    <?php  if( 
        isset($_POST['auteur']) AND 
        isset($_POST['message']) AND 
        isset($_POST['idArticle']) AND 
        ltrim($_POST['auteur'])!='' AND 
        ltrim($_POST['message'])!='' 
    ) // REMARQUE : si les champs sont à vide, on enregistre quand même le tuple dans la BD
    // le NOT NULL ne sert à rien puisqu'on passe une valeur à '', ce qui n'est pas NULL
    {
        // echo 'POST: idArticle:'.$_POST['idArticle'].' -auteur:'. $_POST['auteur'].' -message:'.$_POST['message'];
        $reqSQL='INSERT INTO commentaires (idArticle, auteur, contenu, dateCommentaire) VALUES(?, ?, ?, CURRENT_TIMESTAMP)';       
        $requete = $bdd->prepare($reqSQL);
        $requete->execute(array((int)$_POST['idArticle'], $_POST['auteur'], $_POST['message']));
        $requete->closeCursor();
        // REMARQUE : 
    } ?>

<!-- AFFICHAGE DE LA PAGE  : d'abord le lien vers le retour aux articles--> 
    <p><a href="indexArticles.php?debut=<?php echo $debut ?>&amp;tousLesArticles=<?php echo $tousLesArticles ?>">Retour à la liste des articles</a></p>      

 <!-- AFFICHAGE DE LA PAGE  : affichage de l'article -->              
    <?php $reqSQL=' SELECT id, titre, contenu, DATE_FORMAT(dateCreation, \'%d/%m/%Y à %Hh%imin%ss\') AS dateCreationFR 
        FROM articles WHERE id ='.$idArticle;
        // on peut mettre le $idArticle dans la reqSQL (pas de ?) car on controle son contenu
    $requete = $bdd->prepare($reqSQL);
    $requete->execute(array());
        // $requete->execute(array($idArticle)); pour la version à ?
        // print_r($requete); echo'<br/>';
    $ligne = $requete->fetch();
    $requete->closeCursor(); ?>  

    <div class="commentaires">
        <h3>
            <?php echo htmlspecialchars($ligne['titre']);
            // echo ' - id: '.htmlspecialchars($ligne['id']); 
            ?>
        </h3>
        <p class="date">le <?php echo $ligne['dateCreationFR']; ?></p>               
        <p>
            <?php echo nl2br(htmlspecialchars($ligne['contenu'])); ?>
        </p>
    </div>

 <!-- AFFICHAGE DE LA PAGE  : affichage des commentaires -->  
    <h2>Commentaires</h2>             
    <?php
        $reqSQL='
            SELECT auteur, contenu, DATE_FORMAT(dateCommentaire, \'%d/%m/%Y à %Hh%imin%ss\') AS dateCommentaireFR 
            FROM commentaires 
            WHERE idArticle = ?  AND visible = 1 ORDER BY dateCommentaire
        ';
        $requete = $bdd->prepare($reqSQL);
        $requete->execute(array($idArticle));
        // print_r($requete); echo'<br/>';             
        while ($ligne = $requete->fetch())
        {
    ?>
            <p><strong><?php echo htmlspecialchars($ligne['auteur']); ?></strong> le <?php echo $ligne['dateCommentaireFR']; ?></p>
            <p><?php echo nl2br(htmlspecialchars($ligne['contenu'])); ?></p>
    <?php
        }
        $requete->closeCursor();
    ?>       

<!-- saisie d'un nouveau commentaire -->
    <h2>Entrez un nouveau commentaire :</h2>
    <form action="indexCommentaires.php" method="POST">
    <p>
        <label for="auteur">Auteur</label> : <input type="text" name="auteur" id="auteur" /><br />
        <label for="message">Message</label> : <input type="text" name="message" id="message" /><br />
        <input type="hidden" name="idArticle" value=' <?php echo $idArticle; ?>'> 
        <input type="hidden" name="debut" value=' <?php echo $debut; ?>'>     
        <input type="hidden" name="limit" value=' <?php echo $limit; ?>'>                  
        <input type="submit" value="Envoyer" />
    </p>
    </form>

</article>

<aside class="aside">
    Mon aside à remplir
</aside>

</section>
<?php include("include/footer.php"); ?>
</body>
</html>