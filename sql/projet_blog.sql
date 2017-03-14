DROP DATABASE IF EXISTS PROJET_BLOG;
CREATE DATABASE PROJET_BLOG;
USE PROJET_BLOG;

CREATE TABLE  Articles (
  id int(11) PRIMARY KEY AUTO_INCREMENT,
  titre varchar(255) NOT NULL,
  contenu text NOT NULL,
  visible tinyint(1) NOT NULL DEFAULT 1,
  dateCreation datetime NOT NULL
);

INSERT INTO Articles (titre, contenu, dateCreation) VALUES
('Bienvenue sur mon blog !', 
'Je vous souhaite à toutes et à tous la bienvenue sur mon blog qui parlera de... PHP bien sûr !', 
from_days(to_days(current_date)-30) ),
('PHP, c''est aussi MySQL ou plutôt MariaDB pour rester en pur opensource', 
'MySQL était le compagnon de toujours de PHP. \r\n Mais MySQL s''est laissé séduire par le Soleil et sa Java ! Et le Soleil s''est laissée séduire par un petit neveu de l''ancienne Pythie. 
Les parents de MySQL ont donc engendré sa petite soeur : "MariaDB"',
from_days(to_days(current_date)-10) ),
('qui dit PHP dit aussi HTML et CSS', 
'Le PHP sert d''abord à dynamiser les sites web, HTML5 et CSS3 aujourd''hui',
from_days(to_days(current_date)-9) ),
('Et les Framework ? Symfony ou Zend ?', 
'On ne peut plus réinventer la roue aujourd''hui, ça prend trop de temps ! Alors il faut savoir utiliser les frameworks,
Symfony et Zend sont les plus connus, mais il y en a bien d''autres !',
from_days(to_days(current_date)-8) ),
('Quid des CMS ? Wordpress ou Dotclear', 
'Bien sûr ce petit blog est un peu simpliste. Avec un framework ce serait mieux. Mais surtout, avec un CMS direcement, ce serait parfait !
Wordpress ou Dotclear par exemple. Et ce qui est bien, c''est que pour bien utiler Wordpress il vaut mieux connaître HTML, CSS et... PHP !!!
Let''go pour un parcours complet!',
from_days(to_days(current_date)-7) );


CREATE TABLE Commentaires (
  id int(11) PRIMARY KEY AUTO_INCREMENT,
  idArticle int(11) NOT NULL,
  auteur varchar(255) NOT NULL,
  contenu text NOT NULL,
  visible tinyint(1) NOT NULL DEFAULT 1,  
  dateCommentaire datetime
);
--  FOREIGN KEY (idArticle) references ARTICLES(id)

INSERT INTO Commentaires (idArticle, auteur, contenu, dateCommentaire) VALUES
(1, 'Bertrand', 'Un peu court ce billet !', from_days(to_days(current_date)-29)),
(1, 'Maxime', 'Oui, ça commence pas très fort ce blog...', from_days(to_days(current_date)-28)),
(1, 'MultiKiller', '+1 !', from_days(to_days(current_date)-27)),
(2, 'John', 'Preum''s !', from_days(to_days(current_date)-9)),
(2, 'Maxime', 'Excellente analyse de la situation !\r\nIl y arrivera plus tôt qu''on ne le pense !', from_days(to_days(current_date)-8)),
(3, 'Bertrand', 'Sans oublier le javascript !', from_days(to_days(current_date)-7)),
(3, 'Maxime', 'Et Jquery bien sur !', from_days(to_days(current_date)-6)),
(4, 'Bertrand', 'J''ai une préférence pour Symfony !', from_days(to_days(current_date)-6)),
(5, 'Maxime', 'J''ai une préférence pour Wordpress !', from_days(to_days(current_date)-5));


