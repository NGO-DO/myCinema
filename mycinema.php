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
        <li class="nav-item">
          <a class="nav-link" href="mycinema2.php">Admin <span class="sr-only"></a>
        </li>

        <form action="" method="get">
          <input type="text" name="recherche" placeholder="Titre de film" />
          <input type="text" name="recherche_de_distributor" placeholder="Nom de distributor" />
          <!-- <input type="text" name="recherche_de_user" placeholder="chercher des user" /> -->
          <input type="submit" name="submit" value="Send" />
          <label for="type-select">Choose a type:</label>
          <select name="choose" id="type-select">
            <option value="">--Please choose an type--</option>
            <!-- select -->
            <?php
            $query = $conn->query("SELECT * FROM genre");
            $resultsGenre = $query->fetchAll();
            $html = '';
            foreach ($resultsGenre as $genre) {
              $html .= '<option value="' . $genre['id'] . '">' . $genre['name'] . '</option>';
            }
            echo $html;
            ?>
            <!-- end select -->
          </select>
        </form>
        </span>
        <li class="nav-item">
          <a class="nav-link" href="#"></a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- end nav  -->
  <br>
  <div class='container'>
    <table class='table table-striped table-hover table-primary'>
      <thead>
        <tr>
          <th>Title</th>
          <th>Distributor</th>
          <th>Genre</th>
          <th>Rating</th>
          <th>Duration</th>
          <th>Realease date</th>
        </tr>
      </thead>
      <?php
      $titre = isset($_GET["recherche"]) ? $_GET["recherche"] : "";
      $type = isset($_GET["choose"]) ? $_GET["choose"] : "";
      $distributor = isset($_GET["recherche_de_distributor"]) ? $_GET["recherche_de_distributor"] : "";

      $sql = "SELECT title, distributor.name AS distributor_name, rating, duration, release_date, genre.name AS genre_name
        FROM movie 
        JOIN distributor ON distributor.id = movie.id_distributor
        JOIN movie_genre ON movie.id = movie_genre.id_movie
        JOIN genre ON genre.id = movie_genre.id_genre
        WHERE 1 ";     if (!empty($titre)) {
        $sql .= "AND title LIKE '%$titre%'";
      }
      if (!empty($distributor)) {
        $sql .= "AND distributor.name LIKE '%$distributor%'";
      }
      if (!empty($type)) {
        $sql .= "AND id_genre LIKE '%$type%'";
      }
      $query = $conn->query($sql);
      $results = $query->fetchAll();
      foreach ($results as $value) {
        echo ("<tr>
      <td>" . $value['title'] . "</td>
                <td>" . $value['distributor_name'] . "</td>
                <td>" . $value['genre_name'] . "</td>
                <td>" . $value['rating'] . "</td>
                <td>" . $value['duration'] . "</td>
                <td>" . $value['release_date'] . "</td>
            </tr>");
      }
 
      ?>
    </table>
  </div>
</body>

</html>