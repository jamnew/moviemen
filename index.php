<?php
	ini_set('session.use_only_cookies', true); /* Sets PHP configuration directive that only cookies are to be used for session reference passing */
	if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed. */
	$_SESSION['current_page']='index.php'; /* Set the page to return to if login link is clicked */
	$_SESSION['login_page']=FALSE; /* Is this login.php? */

	include 'header.inc.php'; /* Include header.inc.php */

	// Connecting to & selecting database
	$link = mysql_connect('localhost', 'mm', '') or die('Could not connect: '.mysql_error());
	mysql_select_db('mm') or die('Could not select database');
	
	// Display all records from database in descending order by date	
	$result = mysql_query('SELECT * FROM movies ORDER BY movie_date_watched DESC, movie_id DESC') or die('Query failed: ' . mysql_error());
?>

<!--Repeating movie block-->
<?php
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
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
		echo '</div>'; // end movie_meta
		
		echo '</div>'; // end movie_block
	}

	mysql_free_result($result); /* Free result set */
	mysql_close($link);	/* Closing connection */
	include 'footer.inc.php'; /* Include footer.inc.php */
?>
