<?php
	if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed. */

	include 'functions.php';

	// Connecting to & selecting database
	$link = db_connect();

	include 'header.inc.php'; /* Include header.inc.php */

	switch ($_REQUEST["action"]) {

		case 0: /* When user clicks login from any page this case will occur */

			// Login form
      echo '<div class="form_block">';
			echo '<h3 class="page_name">Login</h3>';
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

			break;

		case 1: /* When user sumbits credentials to be authenticated this case occurs */

			$user = mysqli_real_escape_string($link, $_POST["user"]); /* Escape special chars in user input */
			$pass = md5($_POST["pass"]); /* Hash password supplied by user */

			$result = mysqli_query($link, 'SELECT user_name FROM users WHERE user_name=\''.$user.'\' AND user_pass=\''.$pass.'\'') or die('Query failed: ' . mysqli_error($link)); /* Check if credentials supplied match */

			if (mysqli_num_rows($result) > 0) { /* If credentials match enter this block */

				if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed. */
				$_SESSION['authorized'] = TRUE; /* Set user to authorized. */
				$_SESSION['USER'] = $user;
				$_SESSION['PASS'] = $pass;
				if (isset($_SESSION['current_page'])) {
					if ($_SESSION['current_page'] == 'edit.php') { /* Check if we came from edit.php */
						if (isset($_REQUEST['movie_id'])) { /* Check if we came from edit link */
							header('Location: '.$_SESSION['current_page'].'?movie_id='.$_REQUEST['movie_id']); /* Send user back to edit.php with movie_id */
						}
						else {
							header('Location: index.php'); /* User accessed edit.php directly without a movie_id so redirect to index.php */
						}
					}
					else if ($_SESSION['current_page'] == 'add.php') { /* Check if we came from add.php */
						header('Location: add.php'); /* Send user back to add.php */
					}
					else { /* Came from neither edit.php or add.php so send back to index.php */
						header('Location: index.php');
					}
				}
				else {
					header('Location: index.php'); /* Session variable is not set redirect to index.php */
				}
			}

			else { /* If credentials do not match allow user to retry */

				include 'header.inc.php'; /* Include header.inc.php */
				echo '<hr>';

				// Login form
				echo '<form name="input" action="login.php" method="post">';
				echo '<table><tr><td class="label">Username:</td><td><input type="text" name="user" size="32"></td></tr>';
				echo '<tr><td class="label">Password:</td><td><input type="password" name="pass" size="32"></td></tr>';
				echo '<tr><td class="label"></td><td><input type="submit" value="Submit">';
				echo '<input type="hidden" name="action" value="1">';
				if (isset($_REQUEST['movie_id'])) { /* Pass movie_id along */
					echo '<input type="hidden" name="movie_id" value="'.$_REQUEST['movie_id'].'">';
				}
				echo '</td></tr></table></form>';
				echo '<p class="warning">Username or password was incorrect.</p>'; /* Message advising user that credentials supplied incorrect */

				include 'footer.inc.php'; /* Include footer.inc.php */
			}

			break;

		case 2: /* When a user clicks logout from any page this case will occur */

			session_destroy();

			header('Location: index.php'); /* No matter where user clicks logout they will always return to index.php*/

			break;
	}
?>
