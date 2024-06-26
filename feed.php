<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Flux</title>         
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <img src="profilepic.jpg" alt="Logo de notre réseau social"/>
            <nav id="menu">
                <a href="news.php">Actualités</a>
                <a href="wall.php?user_id=5">Mur</a>
                <a href="feed.php?user_id=5">Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#">Profil</a>
                <ul>
                    <li><a href="settings.php?user_id=5">Paramètres</a></li>
                    <li><a href="followers.php?user_id=5">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php?user_id=5">Mes abonnements</a></li>
                </ul>

            </nav>
        </header>
        <div id="wrapper">
            <?php
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             */
            $userId = intval($_GET['user_id']);
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            ?>

            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //echo "<pre>" . print_r($user, 1) . "</pre>";
                ?>
                <img src="sunnyGin.jpg" alt="Portrait de l'utilisatrice" class="user-picture"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les messages des utilisatrices
                        auxquel est abonnée l'utilisatrice 
                        <a href="wall.php?user_id=<?php echo $user['id']; ?>">
                        <strong>
                            <em>
                        <?php echo $user["alias"]; ?>
                            </em>
                        </strong>
                        </a>
                        (n° <?php echo $user["id"]; ?>)
                    </p>

                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages des abonnements
                 */
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    users.alias as author_name, 
                    users.id as user_id, 
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM followers 
                    JOIN users ON users.id=followers.followed_user_id
                    JOIN posts ON posts.user_id=users.id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE followers.following_user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                while ($message = $lesInformations->fetch_assoc())
                {
                    //echo "<pre>" . print_r($message, 1) . "</pre>";
                ?>            
                <article>
                    <h3>
                        <time datetime='2020-02-01 11:12:13'><?php echo $message["created"];?></time>
                    </h3>
                    <address>
                        <a href="wall.php?user_id=<?php echo $message['user_id']; ?>">
                        <?php echo $message["author_name"]; ?>
                        </a>
                    </address>
                    <div>
                        <p><?php echo $message["content"]; ?></p>
                    </div>                                            
                    <footer>
                        <small>♥<?php echo $message["like_number"]; ?></small>
                        <a href=""><strong><?php echo $message["taglist"]; ?></strong></a>
                    </footer>
                </article>
                <?php } ?>
            </main>
        </div>
    </body>
</html>
