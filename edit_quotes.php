<?php
	ini_set('session.use_only_cookies', true); /* Sets PHP configuration directive that only cookies are to be used for session reference passing */
	if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed. */
	$_SESSION['current_page']='edit.php'; /* Set the page to return to if login link is clicked */
	$_SESSION['login_page']=FALSE; /* Is this login.php? */

	if ($_SESSION['authorized'] == TRUE) { /* Check if user is authorized */

		// Connecting to & selecting database
		$link = mysqli_connect('localhost', 'mm', '%PASSWORD%') or die('Could not connect: '.mysqli_error($link));
                mysqli_set_charset('utf8');
		mysqli_select_db($link, 'mm') or die('Could not select database');

		switch ($_REQUEST["write"]) {

		case 0: /* When user clicks edit movie from index.php this case will occur */
		
			include 'header.inc.php'; /* Include header.inc.php */
		
			// Display record from database to be edited	
			$result = mysqli_query($link, 'SELECT movie_id,movie_name,movie_quote FROM movies ORDER BY movie_date_watched DESC') or die('Query failed: '.mysqli_error($link));

			echo '<hr>';
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				echo '<form name="input" action="edit_quotes.php" method="post">';
				echo '<table><tr><td class="label" id="left">'.$row["movie_name"].'</td></tr>';
				echo '<tr><td><input type="text" name="movie_quote" size="75" value="'.$row["movie_quote"].'">';
				echo '<input type="hidden" name="movie_id" value="'.$row["movie_id"].'">';
				echo '<input type="hidden" name="write" value="1"></td>';
				echo '<td><input type="submit" value="Submit"></td></tr></table></form>';
			}
		
			mysqli_free_result($result); /* Free result set */
			mysqli_close($link);	/* Closing connection */
			include 'footer.inc.php'; /* Include footer.inc.php */

			break;
		
		case 1: /* When user sumbits edit to be written to database this case occurs */

			// Escape all user input used in database queries to prevent SQL injection attacks
			$movie_id = mysqli_real_escape_string($link, $_POST["movie_id"]);
			$movie_quote = mysqli_real_escape_string($link, $_POST["movie_quote"]);

			// Commit edited record to database
			$result = mysqli_query($link, "UPDATE movies SET movie_quote='".$movie_quote."' WHERE movie_id='".$movie_id."'");
			
			// Action on result of update
			if($result) {
				mysqli_close($link); /* Closing connection */
				header("Location: index.php"); /* Redirect browser */
			}
			else {
				include 'header.inc.php'; /* Include header.inc.php */
				echo '<hr>';
				echo 'Query failed: '.mysqli_error($link).'<br>'; /* Report error message */
				mysqli_close($link);	/* Closing connection */
				include 'footer.inc.php'; /* Include footer.inc.php */
			}
			break;
		}
	}
	else if (isset($_COOKIE['USER']) && isset($_COOKIE['PASS'])) { /* If user has entered credentials less than one day ago and automatically login */
		$link = mysqli_connect('localhost', 'mm', '%PASSWORD%') or die('Could not connect: '.mysqli_error($link)); /* Connect to mysql */
                mysqli_set_charset('utf8');
		mysqli_select_db($link, 'mm') or die('Could not select database'); /* Select database */
		
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
