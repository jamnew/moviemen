<?php
	ini_set('session.use_only_cookies', true); /* Sets PHP configuration directive that only cookies are to be used for session reference passing */
	if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed */
?>
<html>
<head>
	<title>Movie Men (MMVII)</title>
	<link href="screen.css" media="screen" rel="stylesheet" title="Default" type="text/css">
	<link href="handheld.css" media="handheld" rel="stylesheet" title="Handheld" type="text/css">
</head>
<body>

<!--Login link-->
<?php
	echo '<div class="mm_login">';
	
	if (!$_SESSION['login_page']) {
		if ($_SESSION['authorized'] == TRUE) { /* Check if user has been recently authorized */
			echo '<a href="feed/"><img style = "vertical-align:text-top;" src = "feed/small_rss.png" /></a>';
			echo ' | Logged in as '.$_COOKIE['USER'];
			echo ' | <a href="add.php" class="menu"><b>Add Movie</b></a>';
			echo '| <a href="edit_quotes.php" class="menu"><b>Edit Quotes</b></a>';
			echo '| <a href="login.php?action=2" class="menu"><b>Logout</b></a>';
		}
		else if (isset($_COOKIE['USER']) && isset($_COOKIE['PASS'])) { /* If user entered credentials less than one day ago automatically login */
			$link = mysql_connect('localhost', 'mm', '') or die('Could not connect: '.mysql_error()); /* Connect to mysql */
			mysql_select_db('mm') or die('Could not select database'); /* Select database */
			
			$user = mysql_real_escape_string($_COOKIE["USER"]); /* Add slashes to escape chars in case the user has hacked the cookie */
			$pass = mysql_real_escape_string($_COOKIE["PASS"]); /* Add slashes to escape chars in case the user has hacked the cookie */
						
			$result = mysql_query('SELECT user_name FROM users WHERE user_name=\''.$user.'\' AND user_pass=\''.$pass.'\'') or die('Query failed: ' . mysql_error()); /* Check if credentials supplied match */

			if (mysql_num_rows($result) > 0) { /* If credentials match enter this block */
				if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed */
				$_SESSION['authorized'] = TRUE; /* Set user to authorized */
					echo '<a href="feed/"><img style = "vertical-align:text-top;" src = "feed/small_rss.png" /></a>';
					echo ' | Logged in as '.$_COOKIE['USER'];
					echo ' | <a href="add.php" class="menu"><b>Add Movie</b></a>';
					echo '| <a href="edit_quotes.php" class="menu"><b>Edit Quotes</b></a>';
					echo '| <a href="login.php?action=2" class="menu"><b>Logout</b></a>';
			}
			else { /* If credentials do not match display login link */
				echo '<a href="feed/"><img style = "vertical-align:text-top;" src = "feed/small_rss.png" /></a>';
				echo ' | <a href="login.php?action=0" class="menu"><b>Login</b></a>';
			}
		}
		else { /* No session & no cookie so just display login link */
			echo '<a href="feed/"><img style = "vertical-align:text-top;" src = "feed/small_rss.png" /></a>';
			echo ' | <a href="login.php?action=0" class="menu"><b>Login</b></a>';
		}
	}
	
	echo '</div>';	
?>


<!--Title block-->
<div id="mm_title">
<?php
	//Pick random movies for quote and posters
	$link = mysql_connect('localhost', 'mm', '') or die('Could not connect: '.mysql_error());
	mysql_select_db('mm') or die('Could not select database');
	$result = mysql_query('SELECT movie_id, movie_name, movie_quote FROM movies ORDER BY movie_id ASC') or die('Query failed: ' . mysql_error());
	$aom = array();
	$aon = array();
	for ($i = 0; $i <= mysql_num_rows($result)-1; $i++) {
		$aon[] = FALSE;
	}
	for ($i = 0; $i <= 12; $i++) {
		$rqi = rand(0,mysql_num_rows($result)-1);
		//echo '1: '.$rqi.' # ';
		while($aon[$rqi]) {
			$rqi = rand(0,mysql_num_rows($result)-1);
			//echo 'W: '.$rqi.' # ';
		}
		$aon[$rqi] = TRUE;
		//echo '<br/>';
		mysql_data_seek($result, $rqi) or die ('Row index out of bounds');
		$aom[] = mysql_fetch_array($result, MYSQL_ASSOC);
	}
	
	$rqi = rand(0,12);
	while($aom[$rqi]["movie_quote"] == '') {
		$rqi = rand(0,12);
	}

	//Display title
	echo '<h1 class="title"><a href="index.php" class="title" title="'.mysql_num_rows($result).' movies and counting...">Movie Men</a></h1>';

	//Display quote
	echo '<div class="mm_quote" title="'.$aom[$rqi]["movie_name"].'">'.$aom[$rqi]["movie_quote"].'</div>';

	//Display posters
	echo '<div class="movie_posters">';
	echo '<table width=800><tr>';
	echo '<td align=center><a href="posters/b'.$aom[0]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[0]["movie_id"].'.jpg" title="'.$aom[0]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[1]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[1]["movie_id"].'.jpg" title="'.$aom[1]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[2]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[2]["movie_id"].'.jpg" title="'.$aom[2]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[3]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[3]["movie_id"].'.jpg" title="'.$aom[3]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[4]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[4]["movie_id"].'.jpg" title="'.$aom[4]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[5]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[5]["movie_id"].'.jpg" title="'.$aom[5]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[6]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[6]["movie_id"].'.jpg" title="'.$aom[6]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[7]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[7]["movie_id"].'.jpg" title="'.$aom[7]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[8]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[8]["movie_id"].'.jpg" title="'.$aom[8]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[9]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[9]["movie_id"].'.jpg" title="'.$aom[9]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[10]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[10]["movie_id"].'.jpg" title="'.$aom[10]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[11]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[11]["movie_id"].'.jpg" title="'.$aom[11]["movie_name"].'"></a></td>';
	echo '<td align=center><a href="posters/b'.$aom[12]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[12]["movie_id"].'.jpg" title="'.$aom[12]["movie_name"].'"></a></td>';
	echo '</tr></table>';
	echo '</div>';
?>
</div>
