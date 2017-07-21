<?php
  if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed. */
  $_SESSION['current_page']='index.php'; /* Set the page to return to if login link is clicked */
  $_SESSION['login_page']=FALSE; /* Is this login.php? */

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
    echo '<span class="link" id="edit"><a href="edit.php?movie_id='.$row["movie_id"].'">Edit</a></span>';
    if ($row["movie_imdb"] != "") {
      echo '<span class="link" id="imdb"><a href="'.$row["movie_imdb"].'">IMDb</a></span>'; }
    if ($row["movie_wikipedia"] != "") {
      echo '<span class="link" id="wikipedia"><a href="'.$row["movie_wikipedia"].'">Wikipedia</a></span>'; }
    if ($row["movie_quote"] != "") {
      echo '<span id="quoteicon" title="'.$row["movie_quote"].'">Quote</span>'; }
    if (file_exists('posters/'.$row["movie_id"].'.jpg')) {
      echo '<span id="postericon"><a href="posters/b'.$row["movie_id"].'.jpg" target="_blank">Poster</a></span>'; }
    echo '</div>'; // end movie_meta

    echo '</div>'; // end movie_block
  }

  mysqli_free_result($result); /* Free result set */
  mysqli_close($link);  /* Closing connection */
  include 'footer.inc.php'; /* Include footer.inc.php */
?>
