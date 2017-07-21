<?php
  if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed. */
  $_SESSION['current_page'] = 'edit.php'; /* Set the page to return to if login link is clicked */

  include 'functions.php';

  // Connecting to & selecting database
  $link = db_connect();

  include 'header.inc.php'; /* Include header.inc.php */

  if ($_SESSION['authorized']) { /* Check if user is authorized */

    switch ($_REQUEST["write"]) {

    case 0: /* When user clicks edit movie from index.php this case will occur */

      // Clean up user input
      $movie_id = db_quote($_REQUEST['movie_id']);

      // Select single movie
      $query = "SELECT * from `movies` WHERE `movie_id` = $movie_id";
      $result = db_select($query);
      if($result === false) {
        die('Query Error: '.db_error());
      }

      // Display edit form
      if(!empty($result)){
        $row = $result[0];

        echo '<div class="form_block">';
        echo '<h3 class="page_name">Edit Movie</h3>';
        echo '<form name="input" action="edit.php" method="post">';
        echo '<table><tr><td class="label">Name:</td><td><input type="text" class="plain" name="movie_name" size="75" value="'.$row["movie_name"].'" id="movie_name"></td></tr>';
        echo '<tr><td class="label">aka:</td><td><input type="text" class="plain" name="movie_aka" size="75" value="'.$row["movie_aka"].'"></td></tr>';
        echo '<tr><td class="label">Year:</td><td><input type="text" class="plain" name="movie_year" size="10" value="'.$row["movie_year"].'"></td></tr>';
        echo '<tr><td class="label">Description:</td><td><textarea class="plain" name="movie_description" rows="5" cols="72"500>'.$row["movie_description"].'</textarea></td></tr>';
        echo '<tr><td class="label">Quote:</td><td><input type="text" class="plain" name="movie_quote" size="75" value="'.$row["movie_quote"].'"></td></tr>';
        echo '<tr><td class="label">IMDb:</td><td><input type="text" class="plain" name="movie_imdb" size="75" value="'.$row["movie_imdb"].'"></td></tr>';
        echo '<tr><td class="label">Wikipedia:</td><td><input type="text" class="plain" name="movie_wikipedia" size="75" value="'.$row["movie_wikipedia"].'"></td></tr>';
        echo '<tr><td class="label">Poster image:</td><td><input type="text" class="plain" name="movie_poster_image" size="75" id="movie_poster_image" value="'.$row["movie_poster_image"].'"></td><td><img id="refresh" src="refresh_16.png" title="Load poster images" /></td></tr>';
        echo '<tr><td class="label">Special guests:</td><td><input type="text" class="plain" name="movie_attendees" size="75" value="'.$row["movie_attendees"].'"></td></tr>';
        echo '<tr><td class="label">Date watched:</td>';
        echo '<td><select name="day">';
          for ($i = 1; $i <= 31; $i++) {
          if ($i == date('j', strtotime($row["movie_date_watched"]))) {
            echo "<option value=\"$i\" selected=\"selected\">$i</option>";
          }
          else {
            echo "<option value=\"$i\">$i</option>";
          }
          }
        echo '</select>';
        echo '<select name="month">';
          for ($i = 1; $i <= 12; $i++) {
          $monthname = date('M', mktime(12, 0, 0, $i, 1, 2007));
          if ($i == date('n', strtotime($row["movie_date_watched"]))) {
            echo "<option value=\"$i\" selected=\"selected\">$monthname</option>";
          }
          else {
            echo "<option value=\"$i\">$monthname</option>";
          }
          }
        echo '</select>';
        echo '<select name="year">';
          for ($i = 2007; $i <= 2075; $i++) {
          if ($i == date('Y', strtotime($row["movie_date_watched"]))) {
            echo "<option value=\"$i\" selected=\"selected\">$i</option>";
          }
          else {
            echo "<option value=\"$i\">$i</option>";
          }
          }
        echo '</select></td></tr>';
        echo '<tr><td class="label">Chosen by:</td><td><input type="text" class="plain" name="movie_chosen_by" size="10" value="'.$row["movie_chosen_by"].'"></td></tr>';
        echo '<tr><td class="label"></td><td><input type="submit" value="Submit">';
        echo '<input type="hidden" name="movie_id" value="'.$movie_id.'">';
        echo '<input type="hidden" name="write" value="1"></td></tr></table></form>';

        echo '<div id="images" style="margin-left: 85px"></div>';
        echo '</div>';
      }
      else{
        echo '<div class="form_block">';
        echo "<div>No movie found.</div>";
        echo '</div>';
      }

      mysqli_free_result($result); /* Free result set */
      mysqli_close($link);  /* Closing connection */
      include 'footer.inc.php'; /* Include footer.inc.php */

      break;

    case 1: /* When user sumbits edit to be written to database this case occurs */

      // Combine and format date option values to mysql format
      $movie_date_watched = date('Y\-m\-d', mktime(0,0,0,$_POST["month"],$_POST["day"],$_POST["year"]));

      // Clean up user input
      $movie_id = db_quote(trim($_POST["movie_id"]));
      $movie_name = db_quote(trim($_POST["movie_name"]));
      $movie_aka = db_quote(trim($_POST["movie_aka"]));
      $movie_year = db_quote(trim($_POST["movie_year"]));
      $movie_description = db_quote(trim($_POST["movie_description"]));
      $movie_imdb = db_quote(trim($_POST["movie_imdb"]));
      $movie_wikipedia = db_quote(trim($_POST["movie_wikipedia"]));
      $movie_poster_image = db_quote(trim($_POST["movie_poster_image"]));
      $movie_date_watched = db_quote(trim($movie_date_watched));
      $movie_chosen_by = db_quote(trim($_POST["movie_chosen_by"]));
      $movie_attendees = db_quote(trim($_POST["movie_attendees"]));
      $movie_quote = db_quote(trim($_POST["movie_quote"]));

      // Update single movie
      $query  = "UPDATE `movies` SET ";
      $query .= "`movie_name` = '$movie_name', ";
      $query .= "`movie_aka` = '$movie_aka', ";
      $query .= "`movie_year` = '$movie_year', ";
      $query .= "`movie_description` = '$movie_description', ";
      $query .= "`movie_imdb` = '$movie_imdb', ";
      $query .= "`movie_wikipedia` = '$movie_wikipedia', ";
      $query .= "`movie_poster_image` = '$movie_poster_image', ";
      $query .= "`movie_date_watched` = '$movie_date_watched', ";
      $query .= "`movie_chosen_by` = '$movie_chosen_by', ";
      $query .= "`movie_attendees` = '$movie_attendees', ";
      $query .= "`movie_quote` = '$movie_quote' ";
      $query .= "WHERE `movie_id` = $movie_id";

      $result = db_query($query);
      if($result === false) {
        die('Query Error: '.db_error());
      }

      // Retrieve poster image file if required
      if(!empty($movie_poster_image)){
        $url = $movie_poster_image;
        $file = explode('/',$url);
        $file = $file[count($file)-1];
        $img = '/tmp/'.$file;
        file_put_contents($img, file_get_contents($url));
        exec("convert /tmp/".$file." /tmp/b".$movie_id.".jpg");
        exec("convert /tmp/".$file." -resize x75 /tmp/".$movie_id.".jpg");
        copy("/tmp/b".$movie_id.".jpg", "posters/b".$movie_id.".jpg");
        copy("/tmp/".$movie_id.".jpg", "posters/".$movie_id.".jpg");
        db_query("UPDATE `movies` SET `movie_poster_image` = NULL WHERE `movie_id` = $movie_id");
      }

      mysqli_free_result($result); /* Free result set */
      mysqli_close($link); /* Closing connection */
      header("Location: index.php#$movie_id"); /* Redirect browser */
      break;
    }
  }
  else { /* User is not authorized so direct them to login.php */
    if (isset($_REQUEST['movie_id'])) {
      header('Location: login.php?movie_id='.$_REQUEST['movie_id']); /* Send movie_id to login.php so we can return to editing the movie after auth */
    }
    else {
      header('Location: login.php');
    }
  }
?>
