<?php
  if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed */
?>
<html>
<head>
  <title>Movie Men (MMVII)</title>
  <link href="screen.css" media="screen" rel="stylesheet" title="Default" type="text/css">
  <link href="handheld.css" media="handheld" rel="stylesheet" title="Handheld" type="text/css">
  <script src="https://moviemen.co/jquery-3.1.0.min.js" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $("#movie_name").change(function(){
        $.ajax({
          url: '/images.php',
          data: 'name='+$('#movie_name').val()
        }).done(function(data){
          $("#images").html(data);
        });
      });
      $("#refresh").mousedown(function(){
        $.ajax({
          url: '/images.php',
          data: 'name='+$('#movie_name').val()
        }).done(function(data){
          $("#images").html(data);
        });
      });
    });
  </script>
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
      echo '| <a href="login.php?action=2" class="menu"><b>Logout</b></a>';
    }
    else if (isset($_COOKIE['USER']) && isset($_COOKIE['PASS'])) { /* If user entered credentials less than one day ago automatically login */
      $user = mysqli_real_escape_string($link, $_COOKIE["USER"]); /* Add slashes to escape chars in case the user has hacked the cookie */
      $pass = mysqli_real_escape_string($link, $_COOKIE["PASS"]); /* Add slashes to escape chars in case the user has hacked the cookie */

      $result = mysqli_query($link, 'SELECT user_name FROM users WHERE user_name=\''.$user.'\' AND user_pass=\''.$pass.'\'') or die('Query failed: ' . mysqli_error($link)); /* Check if credentials supplied match */

      if (mysqli_num_rows($result) > 0) { /* If credentials match enter this block */
        if (session_id() == "") session_start(); /* Checks for active session and if not, one is started or resumed */
        $_SESSION['authorized'] = TRUE; /* Set user to authorized */
          echo '<a href="feed/"><img style = "vertical-align:text-top;" src = "feed/small_rss.png" /></a>';
          echo ' | Logged in as '.$_COOKIE['USER'];
          echo ' | <a href="add.php" class="menu"><b>Add Movie</b></a>';
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
  $result = mysqli_query($link, 'SELECT movie_id, movie_name, movie_aka, movie_year, movie_quote FROM movies ORDER BY movie_id ASC') or die('Query failed: ' . mysqli_error($link));

  $aom = array();
  $aon = array();
  while (count($aom) < 14) {
      $m_id = mt_rand(0,mysqli_num_rows($result)-1); // MySQL results internal pointer starts at 0 and ends at num_rows minus 1
      if (!in_array($m_id,$aon) && file_exists('posters/'.($m_id + 1).'.jpg')) {
        mysqli_data_seek($result, $m_id) or die ('Row index out of bounds');
        $aom[] = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $aon[] = $m_id;
        if ($aom[count($aom)-1]["movie_quote"] != '') {
          $rqi = count($aom)-1;
        }
      }
  }

  //Display title
  echo '<h1 class="title"><a href="index.php" class="title" title="'.mysqli_num_rows($result).' movies and counting...">Movie Men</a></h1>';

  //Display quote
  echo '<div class="mm_quote" title="'.$aom[$rqi]["movie_name"]." (".$aom[$rqi]["movie_year"].")".'">'.$aom[$rqi]["movie_quote"].'</div>';

  //Display posters
  echo '<div class="movie_posters">';
  echo '<table width=800><tr>';
  for ($i = 0; $i < count($aom); $i++){
    echo '<td align=center><a href="posters/b'.$aom[$i]["movie_id"].'.jpg" target="_blank"><img src="posters/'.$aom[$i]["movie_id"].'.jpg" title="'.$aom[$i]["movie_name"]." (".$aom[$i]["movie_year"].")".'"></a></td>';
  }
  echo '</tr></table>';
  echo '</div>';
?>
</div>
