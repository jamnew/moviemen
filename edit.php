<?php
  ini_set('session.use_only_cookies', true); /* Sets PHP configuration directive that only cookies are to be used for session reference passing */
  if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed. */
  $_SESSION['current_page']='edit.php'; /* Set the page to return to if login link is clicked */
  $_SESSION['login_page']=FALSE; /* Is this login.php? */

  include 'functions.php';

  // Connecting to & selecting database
  $link = db_connect() or die('Could not connect: '.mysqli_error($link));

  include 'header.inc.php'; /* Include header.inc.php */

  if ($_SESSION['authorized'] == TRUE) { /* Check if user is authorized */

    switch ($_REQUEST["write"]) {

    case 0: /* When user clicks edit movie from index.php this case will occur */
    
      // Escape all GET & POST data used in database queries to prevent SQL injection attacks
      $movie_id = mysqli_real_escape_string($link, $_REQUEST["movie_id"]);

      // Display record from database to be edited  
      $result = mysqli_query($link, 'SELECT * FROM movies WHERE movie_id="'.$movie_id.'"') or die('Query failed: '.mysqli_error($link));
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

      echo '<hr>';
      
      echo '<form name="input" action="edit.php" method="post">';
      echo '<table><tr><td class="label">Name:</td><td><input type="text" name="movie_name" size="75" value="'.$row["movie_name"].'" id="movie_name"></td></tr>';
      echo '<tr><td class="label">aka:</td><td><input type="text" name="movie_aka" size="75" value="'.$row["movie_aka"].'"></td></tr>';
      echo '<tr><td class="label">Year:</td><td><input type="text" name="movie_year" size="10" value="'.$row["movie_year"].'"></td></tr>';
      echo '<tr><td class="label">Description:</td><td><textarea class="plain" name="movie_description" rows="5" cols="72"500>'.$row["movie_description"].'</textarea></td></tr>';
      echo '<tr><td class="label">Quote:</td><td><input type="text" name="movie_quote" size="75" value="'.$row["movie_quote"].'"></td></tr>';
      echo '<tr><td class="label">IMDb:</td><td><input type="text" name="movie_imdb" size="75" value="'.$row["movie_imdb"].'"></td></tr>';
      echo '<tr><td class="label">Wikipedia:</td><td><input type="text" name="movie_wikipedia" size="75" value="'.$row["movie_wikipedia"].'"></td></tr>';
      echo '<tr><td class="label">Poster image:</td><td><input type="text" name="movie_poster_image" size="75" id="movie_poster_image" value="'.$row["movie_poster_image"].'"></td><td><img id="refresh" src="refresh_16.png" title="Load poster images" /></td></tr>';
      echo '<tr><td class="label">Special guests:</td><td><input type="text" name="movie_attendees" size="75" value="'.$row["movie_attendees"].'"></td></tr>';
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
      echo '<tr><td class="label">Chosen by:</td><td><input type="text" name="movie_chosen_by" size="10" value="'.$row["movie_chosen_by"].'"></td></tr>';
      echo '<tr><td class="label"></td><td><input type="submit" value="Submit">';
      echo '<input type="hidden" name="movie_id" value="'.$movie_id.'">';
      echo '<input type="hidden" name="write" value="1"></td></tr></table></form>';

      echo '<div id="images" style="margin-left: 85px"></div>';

      mysqli_free_result($result); /* Free result set */
      mysqli_close($link);  /* Closing connection */
      include 'footer.inc.php'; /* Include footer.inc.php */

      break;
    
    case 1: /* When user sumbits edit to be written to database this case occurs */

      // Combine and format date option values to mysql format
      $movie_date_watched = date('Y\-m\-d', mktime(0,0,0,$_POST["month"],$_POST["day"],$_POST["year"]));

      // Escape all user input used in database queries to prevent SQL injection attacks
      $movie_id = trim(mysqli_real_escape_string($link, $_POST["movie_id"]));
      $movie_name = trim(mysqli_real_escape_string($link, $_POST["movie_name"]));
      $movie_aka = trim(mysqli_real_escape_string($link, $_POST["movie_aka"]));
      $movie_year = trim(mysqli_real_escape_string($link, $_POST["movie_year"]));
      $movie_description = trim(mysqli_real_escape_string($link, $_POST["movie_description"]));
      $movie_imdb = trim(mysqli_real_escape_string($link, $_POST["movie_imdb"]));
      $movie_wikipedia = trim(mysqli_real_escape_string($link, $_POST["movie_wikipedia"]));
      $movie_poster_image = trim(mysqli_real_escape_string($link, $_POST["movie_poster_image"]));
      $movie_date_watched = trim(mysqli_real_escape_string($link, $movie_date_watched));
      $movie_chosen_by = trim(mysqli_real_escape_string($link, $_POST["movie_chosen_by"]));
      $movie_attendees = trim(mysqli_real_escape_string($link, $_POST["movie_attendees"]));
      $movie_quote = trim(mysqli_real_escape_string($link, $_POST["movie_quote"]));

      // Commit edited record to database
      $result = mysqli_query($link, "UPDATE movies SET movie_name='".$movie_name."',movie_aka='".$movie_aka."',movie_year='".$movie_year."',movie_description='".$movie_description."',movie_imdb='".$movie_imdb."',movie_wikipedia='".$movie_wikipedia."',movie_poster_image='".$movie_poster_image."',movie_date_watched='".$movie_date_watched."',movie_chosen_by='".$movie_chosen_by."',movie_attendees='".$movie_attendees."',movie_quote='".$movie_quote."' WHERE movie_id='".$movie_id."'");
      
      // Action on result of update
      if($result) {
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
          mysqli_query($link, "UPDATE movies SET movie_poster_image = NULL WHERE movie_id='".$movie_id."'");
        }

        mysqli_close($link); /* Closing connection */
        header("Location: index.php"); /* Redirect browser */
      }
      else {
        echo '<hr>';
        echo 'Query failed: '.mysqli_error($link).'<br>'; /* Report error message */
        mysqli_close($link);  /* Closing connection */
        include 'footer.inc.php'; /* Include footer.inc.php */
      }
      break;
    }
  }
  else if (isset($_COOKIE['USER']) && isset($_COOKIE['PASS'])) { /* If user has entered credentials less than one day ago and automatically login */
    $user = mysqli_real_escape_string($link, $_COOKIE["USER"]); /* Add slashes to escape chars in case the user has hacked the cookie */
    $pass = mysqli_real_escape_string($link, $_COOKIE["PASS"]); /* Add slashes to escape chars in case the user has hacked the cookie */
          
    $result = mysqli_query($link, 'SELECT user_name FROM users WHERE user_name=\''.$user.'\' AND user_pass=\''.$pass.'\'') or die('Query failed: ' . mysqli_error($link)); /* Check if credentials supplied match */

    if (mysqli_num_rows($result) > 0) { /* If credentials match enter this block */
      if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed */
      $_SESSION['authorized'] = TRUE; /* Set user to authorized */
      if (isset($_REQUEST['movie_id'])) {
        header('Location: edit.php?movie_id='.$_REQUEST['movie_id']); /* Reload edit.php with user logged in and editing record */
      }
      else {
        header('Location: index.php'); /* User accessed edit.php directly and without movie_id, send to index.php and logged in */
      }
    }
    else { /* Cookie(s) have been modifed and user is not authorized so direct them to login.php */
      if (isset($_REQUEST['movie_id'])) {
        header('Location: login.php?movie_id='.$_REQUEST['movie_id']); /* Send movie_id to login.php so we can return to editing the movie after auth */
      }
      else {
        header('Location: login.php');
      }
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
