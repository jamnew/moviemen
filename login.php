<?php
	include 'functions.php';
	manage_session();

	// Connecting to & selecting database
	$link = db_connect();

	$step = empty($_REQUEST["action"]) ? 0 : $_REQUEST["action"];

	switch ($step) {

		case 0: /* When user clicks login from any page this case will occur */

			include 'header.inc.php'; /* Include header.inc.php */

			// Login form
			echo '<div class="form_block">';
			echo '<h3 class="page_name">Login</h3>';
			echo '<form name="input" action="login.php" method="post">';
			echo '<table><tr><td class="label">Username:</td><td><input type="text" name="user" size="32"></td></tr>';
			echo '<tr><td class="label">Password:</td><td><input type="password" name="pass" size="32"></td></tr>';
			echo '<tr><td class="label"></td><td><input type="submit" value="Submit">';
			echo '<input type="hidden" name="action" value="1">';
			echo '</td></tr></table></form>';
			echo '</div>';

			include 'footer.inc.php'; /* Include footer.inc.php */

			break;

		case 1: /* When user sumbits credentials to be authenticated this case occurs */

			$user = mysqli_real_escape_string($link, $_POST["user"]); /* Escape special chars in user input */
			$pass = md5($_POST["pass"]); /* Hash password supplied by user */

			$result = mysqli_query($link, 'SELECT user_name FROM users WHERE user_name=\''.$user.'\' AND user_pass=\''.$pass.'\'') or die('Query failed: ' . mysqli_error($link)); /* Check if credentials supplied match */

			if (mysqli_num_rows($result) > 0) { /* If credentials match enter this block */
				$_SESSION['authorised'] = true; /* Set user to authorised. */
				$_SESSION['user'] = $user;

				$location = "index";
				if(!empty($_SESSION['current_page'])){
					$location = $_SESSION['current_page'];
					if(!empty($_SESSION['current_id'])){
						$location .= '?movie_id='.$_SESSION['current_id'];
					}
				}

				header("Location: $location");
			}

			else { /* If credentials do not match allow user to retry */

				include 'header.inc.php'; /* Include header.inc.php */

				// Login form
				echo '<div class="form_block">';
				echo '<h3 class="page_name">Login</h3>';
				echo '<p class="warning">Username or password was incorrect.</p>'; /* Message advising user that credentials supplied incorrect */
				echo '<form name="input" action="login.php" method="post">';
				echo '<table><tr><td class="label">Username:</td><td><input type="text" name="user" size="32"></td></tr>';
				echo '<tr><td class="label">Password:</td><td><input type="password" name="pass" size="32"></td></tr>';
				echo '<tr><td class="label"></td><td><input type="submit" value="Submit">';
				echo '<input type="hidden" name="action" value="1">';
				if (isset($_REQUEST['movie_id'])) { /* Pass movie_id along */
					echo '<input type="hidden" name="movie_id" value="'.$_REQUEST['movie_id'].'">';
				}
				echo '</td></tr></table></form>';
				echo '</div>';

				include 'footer.inc.php'; /* Include footer.inc.php */
			}

			break;

		case 2: /* When a user clicks logout from any page this case will occur */

			session_destroy();

			header('Location: index.php'); /* No matter where user clicks logout they will always return to index.php*/

			break;
	}
?>
