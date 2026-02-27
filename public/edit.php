<?php
  include 'functions.php';
  manage_session(array('current_page' => 'edit', 'current_id' => $_REQUEST['movie_id']));

  // Connecting to & selecting database
  $link = db_connect();

  if (!empty($_SESSION['authorised'])) { /* Check if user is authorised */

    $step = empty($_REQUEST["write"]) ? 0 : $_REQUEST["write"];

    switch ($step) {

    case 0: /* When user clicks edit movie from index.php this case will occur */

      include '_header.php'; /* Include _header.php */

      // Clean up user input
      $movie_id = db_quote($_REQUEST['movie_id']);

      // Select single movie
      $query = "SELECT * from `movies` WHERE `movie_id` = $movie_id";
      $result = db_select($query);
      if($result === false) {
        die('Query Error: '.db_error());
      }

      /* TODO */
      // Display current poster image and thumbnail (dimensions etc) so the user can determine whether it needs updating

      // Display edit form
      if(!empty($result)){
        $row = $result[0];

        echo '<div class="form_block">';
        echo '<h3 class="page_name">Edit Movie</h3>';
        echo '<form name="input" action="edit.php" method="post">';
        echo '<table><tr><td class="label">Name:</td><td><input type="text" class="plain" id="movie_name" name="movie_name" size="75" value="'.$row["movie_name"].'"></td></tr>';
        echo '<tr><td class="label">aka:</td><td><input type="text" class="plain" id="movie_aka" name="movie_aka" size="75" value="'.$row["movie_aka"].'"></td></tr>';
        echo '<tr><td class="label">Year:</td><td><input type="text" class="plain" id="movie_year" name="movie_year" size="10" value="'.$row["movie_year"].'"></td></tr>';
        echo '<tr><td class="label">Description:</td><td><textarea class="plain" id="movie_description" name="movie_description" rows="5" cols="72">'.$row["movie_description"].'</textarea></td></tr>';
        echo '<tr><td class="label">Quote:</td><td><input type="text" class="plain" id="movie_quote" name="movie_quote" size="75" value="'.$row["movie_quote"].'"></td></tr>';
        echo '<tr><td class="label">IMDb:</td><td><input type="text" class="plain" id="movie_imdb" name="movie_imdb" size="75" value="'.$row["movie_imdb"].'"></td></tr>';
        echo '<tr><td class="label">Wikipedia:</td><td><input type="text" class="plain" id="movie_wikipedia" name="movie_wikipedia" size="75" value="'.$row["movie_wikipedia"].'"></td></tr>';
        echo '<tr><td class="label">Poster image:</td><td><input type="text" class="plain" id="movie_poster_image" name="movie_poster_image" size="75" value="'.$row["movie_poster_image"].'"></td><td><img id="refresh" src="assets/images/refresh_icon_256x256.png" height="16" width="16" title="Load poster images" /></td></tr>';
        echo '<tr><td class="label">Special guests:</td><td><input type="text" class="plain" id="movie_attendees" name="movie_attendees" size="75" value="'.$row["movie_attendees"].'"></td></tr>';
        echo '<tr><td class="label">Event:</td><td><input type="text" class="plain" id="movie_event" name="movie_event" size="75" value="'.$row["movie_event"].'"></td></tr>';
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

      mysqli_close($link);  /* Closing connection */
      include '_footer.php'; /* Include _footer.php */

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
      $movie_event = db_quote(trim($_POST["movie_event"]));

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
      $query .= "`movie_quote` = '$movie_quote', ";
      $query .= "`movie_event` = '$movie_event' ";
      $query .= "WHERE `movie_id` = $movie_id";

      $result = db_query($query);
      if($result === false) {
        die('Query Error: '.db_error());
      }

      // Retrieve poster image file if required
      if(!empty($movie_poster_image)){
        $config = app_config();

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

        // Set path for poster original image (new)
        $poster_original_file = sprintf('orig_%s.%s', $movie_id, $config['posters_image_format']);
        $poster_original_path = sprintf('%s/%s', $config['posters_path'], $poster_original_file);

        // Set path for poster display image (ex bxxx.jpg)
        $poster_display_file = sprintf('disp_%s.%s', $movie_id, $config['posters_image_format']);
        $poster_display_path = sprintf('%s/%s', $config['posters_path'], $poster_display_file);

        // Set path for poster thumbnail (ex xxx.jpg)
        $poster_thumbnail_file = sprintf('thum_%s.%s', $movie_id, $config['posters_image_format']);
        $poster_thumbnail_path = sprintf('%s/%s', $config['posters_path'], $poster_thumbnail_file);

        // Get height of poster image
        $height = exec("identify $temp_path |cut -d' ' -f3 |cut -d'x' -f2");

        // Convert and resize poster images
        exec("convert $temp_path $poster_original_path");
        if($height > 800){
          exec("convert $temp_path -resize x800 $poster_display_path");
        }
        else{
          exec("cp $temp_path $poster_display_path");
        }
        if($height > 300){
          exec("convert $temp_path -resize x300 $poster_thumbnail_path");
        }
        else{
          exec("cp $temp_path $poster_thumbnail_path");
        }

        // Set permissions on poster image and thumbnail
        chmod($poster_original_path, 0664);
        chmod($poster_display_path, 0664);
        chmod($poster_thumbnail_path, 0664);

        // Remove stored poster image URL
        db_query("UPDATE `movies` SET `movie_poster_image` = NULL WHERE `movie_id` = $movie_id");
      }

      mysqli_close($link); /* Closing connection */
      header("Location: index.php#$movie_id"); /* Redirect browser */
      break;
    }
  }
  else { /* User is not authorised so direct them to login.php */
    if (isset($_REQUEST['movie_id'])) {
      header('Location: login.php?movie_id='.$_REQUEST['movie_id']); /* Send movie_id to login.php so we can return to editing the movie after auth */
    }
    else {
      header('Location: login.php');
    }
  }
?>
