<?php
  if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed. */
  $_SESSION['current_page'] = 'index.php'; /* Set the page to return to if login link is clicked */

  include 'functions.php';

  // Connecting to & selecting database
  $link = db_connect();

  include 'header.inc.php'; /* Include header.inc.php */

  // Select all movies in descending order by date watched
  $result = db_select("SELECT * from `movies` ORDER BY `movie_date_watched` DESC, `movie_id` DESC");
  if($result === false) {
    die('Query Error: '.db_error());
  }
?>

<!--Repeating movie block-->
<?php
  foreach($result as $row){
    echo '<div class="movie_block">';
    echo '<a name="' .$row["movie_id"]. '"></a>';
    if ($row["movie_aka"] == ""){
      echo '<h3 class="movie_name">'.$row["movie_name"].' ('.$row["movie_year"].')</h3>';
    }
    else {
      echo '<h3 class="movie_name">'.$row["movie_name"].' ('.$row["movie_year"].') aka '.$row["movie_aka"].'</h3>';
    }
    echo '<div class="movie_description">'.nl2br($row["movie_description"]).'</div>';
    echo '<div class="movie_attendees">';
    if ($row["movie_attendees"] != "") {
      echo 'Watched on '.$row["movie_date_watched"].', chosen by '.$row["movie_chosen_by"].'. Special guests '.$row["movie_attendees"].'.';
    }
    else {
      echo 'Watched on '.$row["movie_date_watched"].', chosen by '.$row["movie_chosen_by"].'.';
    }
    echo '</div>'; // end movie_attendees

    echo '<div class="movie_meta">';
    echo '<a id="edit" href="edit.php?movie_id='.$row["movie_id"].'">Edit</a>';
    if ($row["movie_imdb"] != "") {
      echo '<a id="imdb" href="'.$row["movie_imdb"].'">IMDb</a>'; }
    if ($row["movie_wikipedia"] != "") {
      echo '<a id="wikipedia" href="'.$row["movie_wikipedia"].'">Wikipedia</a>'; }
    if ($row["movie_quote"] != "") {
      echo '<div id="quote" title="'.$row["movie_quote"].'">Quote</div>'; }

    // Poster
    $config = parse_ini_file('../config.ini');
    $image_file = sprintf('b%s.%s', $row['movie_id'], $config['posters_image_format']);
    $image_path = sprintf('%s/%s', $config['posters_path'], $image_file);
    if (file_exists($image_path)) {
      echo '<div id="poster" onclick="lightbox_toggle(\'poster.php?movie_id='.$row["movie_id"].'\')">Poster</div>'; }

    echo '</div>'; // end movie_meta

    echo '</div>'; // end movie_block
  }

  mysqli_close($link);  /* Closing connection */
  include 'footer.inc.php'; /* Include footer.inc.php */
?>
