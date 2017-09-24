<?php
  include 'functions.php';
  manage_session(array('current_page' => 'add', 'current_id' => NULL));

  // Connecting to & selecting database
  $link = db_connect();

  if ($_SESSION['authorised']) { /* Check if user is authorised */

    $step = empty($_REQUEST["insert"]) ? 0 : $_REQUEST["insert"];

    switch ($step) {

    case 0: /* When user clicks add movie from index.php this case will occur */

      include 'header.inc.php'; /* Include header.inc.php */

      echo '<div class="form_block">';
      echo '<h3 class="page_name">Add Movie</h3>';
      echo '<form name="input" action="add.php" method="post">';
      echo '<table><tr><td class="label">Name:</td><td><input type="text" class="plain" id="movie_name" name="movie_name" size="75" autofocus></td></tr>';
      echo '<tr><td class="label">aka:</td><td><input type="text" class="plain" id="movie_aka" name="movie_aka" size="75"></td></tr>';
      echo '<tr><td class="label">Year:</td><td><input type="text" class="plain" id="movie_year" name="movie_year" size="10"></td></tr>';
      echo '<tr><td class="label">Description:</td><td><textarea class="plain" id="movie_description" name="movie_description" rows="5" cols="72"></textarea></td></tr>';
      echo '<tr><td class="label">Quote:</td><td><input type="text" class="plain" id="movie_quote" name="movie_quote" size="75"></td></tr>';
      echo '<tr><td class="label">IMDb:</td><td><input type="text" class="plain" id="movie_imdb" name="movie_imdb" size="75"></td></tr>';
      echo '<tr><td class="label">Wikipedia:</td><td><input type="text" class="plain" id="movie_wikipedia" name="movie_wikipedia" size="75"></td></tr>';
      echo '<tr><td class="label">Poster image:</td><td><input type="text" class="plain" id="movie_poster_image" name="movie_poster_image" size="75"></td></tr>';
      echo '<tr><td class="label">Special guests:</td><td><input type="text" class="plain" id="movie_attendees" name="movie_attendees" size="75"></td></tr>';
      echo '<tr><td class="label">Event:</td><td><input type="text" class="plain" id="movie_event" name="movie_event" size="75"></td></tr>';
      echo '<tr><td class="label">Date watched:</td>';
      echo '<td><select name="day">';
        for ($i = 1; $i <= 31; $i++) {
        if ($i == date('j', strtotime(date('Y\-m\-d')))) {
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
        if ($i == date('n', strtotime(date('Y\-m\-d')))) {
          echo "<option value=\"$i\" selected=\"selected\">$monthname</option>";
        }
        else {
          echo "<option value=\"$i\">$monthname</option>";
        }
        }
      echo '</select>';
      echo '<select name="year">';
        for ($i = 2007; $i <= 2075; $i++) {
        if ($i == date('Y', strtotime(date('Y\-m\-d')))) {
          echo "<option value=\"$i\" selected=\"selected\">$i</option>";
        }
        else {
          echo "<option value=\"$i\">$i</option>";
        }
        }
      echo '</select></td></tr>';
      echo '<tr><td class="label">Chosen by:</td><td><input type="text" class="plain" name="movie_chosen_by" size="10"></td></tr>';
      echo '<tr><td class="label"></td><td><input type="submit" value="Submit">';
      echo '<input type="hidden" name="insert" value="1"></td></tr></table></form>';

      echo '<div id="images" class="poster_selection"></div>';

      echo '</div>';

      include 'footer.inc.php'; /* Include footer.inc.php */
      break;

    case 1: /* When user sumbits addition to be written to database this case occurs */

      // Combine and format date option values to mysql format
      $movie_date_watched = date('Y\-m\-d', mktime(0,0,0,$_POST["month"],$_POST["day"],$_POST["year"]));

      // Clean up user input
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
      $movie_event = db_quote(trim($_POST["movie_event"]));

      // Insert single movie
      $query  = "INSERT INTO `movies` ";
      $query .= "(";
      $query .= "`movie_name`, ";
      $query .= "`movie_aka`, ";
      $query .= "`movie_year`, ";
      $query .= "`movie_description`, ";
      $query .= "`movie_imdb`, ";
      $query .= "`movie_wikipedia`, ";
      $query .= "`movie_poster_image`, ";
      $query .= "`movie_date_watched`, ";
      $query .= "`movie_chosen_by`, ";
      $query .= "`movie_attendees`, ";
      $query .= "`movie_quote`, ";
      $query .= "`movie_event`";
      $query .= ") ";
      $query .= "VALUES ";
      $query .= "(";
      $query .= "'$movie_name', ";
      $query .= "'$movie_aka', ";
      $query .= "'$movie_year', ";
      $query .= "'$movie_description', ";
      $query .= "'$movie_imdb', ";
      $query .= "'$movie_wikipedia', ";
      $query .= "'$movie_poster_image', ";
      $query .= "'$movie_date_watched', ";
      $query .= "'$movie_chosen_by', ";
      $query .= "'$movie_attendees', ";
      $query .= "'$movie_quote', ";
      $query .= "'$movie_event'";
      $query .= ")";

      $result = db_query($query);
      if($result === false) {
        die('Query Error: '.db_error());
      }

      // Get auto_incremented id of last insert (per connection)
      $movie_id = mysqli_insert_id($link);

      // Retrieve poster image file if required
      if(!empty($movie_poster_image)){
        // Parse config file
        $config = parse_ini_file('../config.ini');

        /* TODO */
        // Check path is configured and use default if not
        // Check path exists
        // Check path can be created if doesn't exist
        // Create path if doesn't exist
        // Check path is writable

        // Get image filename from poster image URL
        $temp_file = explode('/', $movie_poster_image);
        $temp_file = $temp_file[count($temp_file) - 1];

        // Set temp path for poster image retrieval
        $temp_path = sprintf('%s/%s', $config['temp_path'], $temp_file);

        // Retrieve poster image
        file_put_contents($temp_path, file_get_contents($movie_poster_image));

        // Set path for poster image
        $image_file = sprintf('b%s.%s', $movie_id, $config['posters_image_format']);
        $image_path = sprintf('%s/%s', $config['posters_path'], $image_file);

        // Set path for poster thumbnail
        $thumbnail_file = sprintf('%s.%s', $movie_id, $config['posters_image_format']);
        $thumbnail_path = sprintf('%s/%s', $config['posters_path'], $thumbnail_file);

        // Convert poster image
        exec("convert $temp_path $image_path");

        // Convert and resize poster thumbnail
        exec("convert $temp_path -resize x75 $thumbnail_path");

        // Set permissions on poster image and thumbnail
        chmod($image_path, 0664);
        chmod($thumbnail_path, 0664);

        // Remove stored poster image URL
        db_query("UPDATE `movies` SET `movie_poster_image` = NULL WHERE `movie_id` = $movie_id");
      }

      mysqli_close($link); /* Closing connection */
      header("Location: index.php"); /* Redirect browser */
      break;
    }
  }
  else { /* User is not authorised so direct them to login.php */
    header('Location: login.php');
  }
?>
