<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <link href="mycinema.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="mycinema.js" rel="stylesheet"></script>
    <title>mycinema</title>
</head>

<body>
    <?php
    $host = "localhost";
    $dbname = "cinema";
    $username = "Guasette";
    $password = "wac";
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connecté à $dbname sur $host avec succès.";
    } catch (PDOException $e) {
        echo "Impossible de se connecter à la base de données $dbname :" .
            $e->getMessage();
    }
    ?>
    <!-- nav -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Vinchiné</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="mycinema.php">Home <span class="sr-only"></a>
                </li>
                <form action="" method="get">
                    <input type="text" name="recherche_de_user" placeholder="recherche user" />
                    <input type="text" name="recherche_de_user_lastname" placeholder="recherche user lastname" />
                    <input type="submit" name="submit" value="Send" />
                </form>
            </ul>
        </div>
    </nav>
    <!-- end nav  -->
    <br>

    <?php
    if (!empty($_POST)) {
        if (array_key_exists('supprimer', $_POST)) {
            $sql = "DELETE FROM membership WHERE id_user=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$_POST['user']]);
        } else {
            $date = new DateTime();
            $date = $date->format('Y-m-d H:i:s');

            $sub = $_POST['subscription'];
            $user = $_POST['user'];
            $action = $_POST['action'];

            if ($action === 'Ajouter') {
                $stmt = $conn->prepare("INSERT INTO membership (id_user, id_subscription, date_begin) VALUES (?, ?, ?)");
                $stmt->execute([$user, $sub, $date]);
            }

            if ($action === 'Modifier') {
                $stmt = $conn->prepare("UPDATE membership SET id_user=?, id_subscription=?, date_begin=? WHERE id_user=?");
                $stmt->execute([$user, $sub, $date, $user]);
            }
        }
    }
    ?>


    <div class='container'>
        <table class='table table-striped table-hover table-primary'>
            <thead>
                <tr>
                    <th>firstname</th>
                    <th>lastname</th>
                    <th>city</th>
                    <th>subscription</th>
                    <th>date begin</th>
                    <th>ajouter/modifier/supprimer</th>
                </tr>
            </thead>
            <?php
            $serch_firstname = isset($_GET["recherche_de_user"])
                ? $_GET["recherche_de_user"]
                : "";
            $serch_lastname = isset($_GET["recherche_de_user_lastname"])
                ? $_GET["recherche_de_user_lastname"]
                : "";
            $sql = "SELECT user.id AS user_id, lastname,firstname,user.city,subscription.name,membership.date_begin 
                    FROM user 
                    LEFT JOIN membership ON user.id = membership.id_user 
                    LEFT JOIN subscription ON subscription.id = membership.id_subscription
                    WHERE 1 ";
            // var_dump($sql);die;
            if (!empty($serch_firstname)) {
                $sql .= "AND firstname LIKE '%$serch_firstname%'";
            }
            if (!empty($serch_lastname)) {
                $sql .= "AND lastname LIKE '%$serch_lastname%'";
            }
            // var_dump($sql);die;
            $query = $conn->query($sql);
            // var_dump($query);die;
            $resultsfirstlastname = $query->fetchAll();
            $subscriptions = $conn->query('SELECT * FROM subscription')->fetchAll();
            foreach ($resultsfirstlastname as $value) {
                echo "<tr>
                    <td>" . $value["firstname"] . "</td>
                    <td>" . $value["lastname"] . "</td>
                    <td>" . $value["city"] . "</td>                    
                    <td>" . $value["name"] . "</td>
                    <td>" . $value["date_begin"] . "</td>";
                echo "<td>";

                echo "<form name='sub_form' method='POST' action=''>";
                echo "<select name='subscription' id='subscription'>";
                echo '<option value="">--Please choose a sub--</option>';
                foreach ($subscriptions as $subscription) {
                    echo '<option value="' . $subscription['id'] . '">' . $subscription['name'] . '</option>';
                }
                echo "</select>";
                $text = isset($value['name']) ? 'Modifier' : 'Ajouter';
                echo "<input type='hidden' name = 'user' value ='" . $value['user_id'] . "'/>";
                echo "<input type='submit' name = 'action' value ='" . $text . "'/>";
                echo "<input type='submit' name = 'supprimer' value ='Supprimer'/>";
                echo "</form>";

                echo "</td>";
                echo "</tr>";
            }
            $addjouter = isset($_GET["ajouter"]) ? $_GET["ajouter"] : "";
            $supprimer = isset($_GET["supprimer"]) ? $_GET["supprimer"] : "";
            $modifier = isset($_GET["modifier"]) ? $_GET["modifier"] : "";

            $mysql = "SELECT firstname,lastname,user.id AS userID , id_subscription AS subID ,subscription.name FROM user 
                    JOIN membership ON user.id = membership.id_user
                    JOIN subscription ON membership.id_subscription = subscription.id";
            ?>
        </table>
    </div>
</body>

</html>