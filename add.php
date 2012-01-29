<?php
	ini_set('session.use_only_cookies', true); /* Sets PHP configuration directive that only cookies are to be used for session reference passing */
	if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed. */
	$_SESSION['current_page']='add.php'; /* Set the page to return to if login link is clicked */
	$_SESSION['login_page']=FALSE; /* Is this login.php? */

	if ($_SESSION['authorized'] == TRUE) { /* Check if user is authorized */

		// Connecting to & selecting database
		$link = mysql_connect('localhost', 'mm', '') or die('Could not connect: '.mysql_error());
		mysql_select_db('mm') or die('Could not select database');

		switch ($_REQUEST["insert"]) {

		case 0: /* When user clicks add movie from index.php this case will occur */
		
			include 'header.inc.php'; /* Include header.inc.php */
		
			echo '<hr>';
			
			echo '<form name="input" action="add.php" method="post">';
			echo '<table><tr><td class="label">Name:</td><td><input type="text" name="movie_name" size="75"></td></tr>';
			echo '<tr><td class="label">Description:</td><td><textarea class="plain" name="movie_description" rows="5" cols="72"></textarea></td></tr>';
			echo '<tr><td class="label">Quote:</td><td><input type="text" name="movie_quote" size="75"></td></tr>';
			echo '<tr><td class="label">IMDb:</td><td><input type="text" name="movie_imdb" size="75"></td></tr>';
			echo '<tr><td class="label">Wikipedia:</td><td><input type="text" name="movie_wikipedia" size="75"></td></tr>';
			echo '<tr><td class="label">Special guests:</td><td><input type="text" name="movie_attendees" size="75"></td></tr>';
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
			echo '<tr><td class="label">Chosen by:</td><td><input type="text" name="movie_chosen_by" size="10"></td></tr>';
			echo '<tr><td class="label"></td><td><input type="submit" value="Submit">';
			echo '<input type="hidden" name="movie_id" value="'.$movie_id.'">';
			echo '<input type="hidden" name="insert" value="1"></td></tr></table></form>';

			include 'footer.inc.php'; /* Include footer.inc.php */

			break;
		
		case 1: /* When user sumbits addition to be written to database this case occurs */

			// Combine and format date option values to mysql format
			$movie_date_watched = date('Y\-m\-d', mktime(0,0,0,$_POST["month"],$_POST["day"],$_POST["year"]));
			
			// Escape all user input used in database queries to prevent SQL injection attacks
			$movie_name = mysql_real_escape_string($_POST["movie_name"]);
			$movie_description = mysql_real_escape_string($_POST["movie_description"]);
			$movie_imdb = mysql_real_escape_string($_POST["movie_imdb"]);
			$movie_wikipedia = mysql_real_escape_string($_POST["movie_wikipedia"]);
			$movie_date_watched = mysql_real_escape_string($movie_date_watched);
			$movie_chosen_by = mysql_real_escape_string($_POST["movie_chosen_by"]);
			$movie_attendees = mysql_real_escape_string($_POST["movie_attendees"]);
			$movie_quote = mysql_real_escape_string($_POST["movie_quote"]);

			// Commit edited record to database
			$result = mysql_query("INSERT INTO movies VALUES ('','".$movie_name."','".$movie_description."','".$movie_imdb."','".$movie_wikipedia."','".$movie_date_watched."','".$movie_chosen_by."','".$movie_attendees."','".$movie_quote."')");
			
			// Action on result of update
			if($result) {
				mysql_close($link); /* Closing connection */
				header("Location: index.php"); /* Redirect browser */
			}
			else {
				include 'header.inc.php'; /* Include header.inc.php */
				echo '<hr>';
				echo 'Query failed: '.mysql_error().'<br>'; /* Report error message */
				mysql_close($link);	/* Closing connection */
				include 'footer.inc.php'; /* Include footer.inc.php */
			}
			break;
		}
	}
	else if (isset($_COOKIE['USER']) && isset($_COOKIE['PASS'])) { /* If user has entered credentials less than one day ago and automatically login */
		$link = mysql_connect('localhost', 'mm', '') or die('Could not connect: '.mysql_error()); /* Connect to mysql */
		mysql_select_db('mm') or die('Could not select database'); /* Select database */
		
		$user = mysql_real_escape_string($_COOKIE["USER"]); /* Add slashes to escape chars in case the user has hacked the cookie */
		$pass = mysql_real_escape_string($_COOKIE["PASS"]); /* Add slashes to escape chars in case the user has hacked the cookie */
					
		$result = mysql_query('SELECT user_name FROM users WHERE user_name=\''.$user.'\' AND user_pass=\''.$pass.'\'') or die('Query failed: ' . mysql_error()); /* Check if credentials supplied match */

		if (mysql_num_rows($result) > 0) { /* If credentials match enter this block */
			if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed */
			$_SESSION['authorized'] = TRUE; /* Set user to authorized */
			header('Location: add.php'); /* Reload add.php with user logged in */
		}
		
		else { /* Cookie(s) have been modifed and user is not authorized so direct them to login.php */
			header('Location: login.php');
		}
	}	
	else { /* User is not authorized so direct them to login.php */
		header('Location: login.php');
	}
?>
