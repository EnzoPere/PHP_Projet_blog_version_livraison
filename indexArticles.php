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
<!-- INITIALISATION des variables -->
    <?php  // echo'DEBUG : POST : ';print_r($_POST);echo'<br/>SESSION : '; print_r($_SESSION);echo'<br/>GET : ';print_r($_GET);echo'<br/>';           
    $nbMessages=3; // nombre de messages affichés en même temps
    $step=1; // la marche de montée et de descente
    $debut=0; // on affiche les message de debut à nbMessages-1
    ?> 

<!-- ENTREE par DECONNEXION de l'admin -->
    <?php if(isset($_POST['deconnexion'])) unset($_SESSION['admin']); ?>

<!-- Connexion à la BD : ça servira plus tard -->
    <?php include("include/connexion.php"); ?>

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
    //echo 'limit: '.$limit; echo'<br/>'; 
    ?>  

<!-- AFFICHAGE DE LA PAGE  : d'abord les boutons -->
    <form action="indexArticles.php" method="POST">
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

<!-- AFFICHAGE DE LA PAGE  : affichage des articles -->
    <?php $reqSQL='SELECT id, titre, contenu, dateCreation FROM articles
    WHERE visible=1   
    ORDER BY dateCreation DESC '.$limit;   
    //  $reqSQL='SELECT id, titre, contenu, DATE_FORMAT(date_creation, \'%d/%m/%Y à %Hh%imin\') AS date_creation_fr 
    $requete=$bdd->query($reqSQL);
    // Affichage de chaque message - htmlspecialchars pour nettoyer les données si ça n'avait pas été fait
    while($ligne=$requete->fetch()){ ?>
        <div class="toutArticle">
            <h3>
                <?php echo htmlspecialchars($ligne['titre']).' - id:'.htmlspecialchars($ligne['id']); ?>
            </h3>
           
            <div class="articleEtDate">           
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
                $reqSQL2='SELECT count(*) as nbCommentaires FROM commentaires WHERE idArticle=' .$ligne['id']. ' AND visible=1';

                $requete2=$bdd->query($reqSQL2);
                $ligne2=$requete2->fetch(); ?>

                <em><a href="indexCommentaires.php?idArticle=<?php echo $ligne['id'] ?>&amp;debut=<?php echo $debut ?>&amp;tousLesArticles=<?php echo $tousLesArticles ?>">
                    <br/> <?php echo $ligne2['nbCommentaires']?> Commentaires
                </a></em>
            </p> 
            </div>                     
        </div> 
    <?php } // Fin de la boucle des billets
    // echo 'debut : '.$debut;
    $requete->closeCursor(); ?>

</article>

<aside class="aside">
    Mon aside à remplir
</aside>

</section>
<?php include("include/footer.php"); ?>

</body>
</html>