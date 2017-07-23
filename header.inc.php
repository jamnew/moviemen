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

  if ($_SESSION['authorized'] == TRUE) { /* Check if user has been recently authorized */
    echo 'Logged in as '.$_SESSION['USER'];
    echo ' | <a href="feed/" class="menu"><b>Feed</b></a>';
    echo '| <a href="add.php" class="menu"><b>Add Movie</b></a>';
    echo '| <a href="login.php?action=2" class="menu"><b>Logout</b></a>';
  }
  else { /* No session so display login link */
    echo '<a href="feed/" class="menu"><b>Feed</b></a>';
    echo '| <a href="login.php?action=0" class="menu"><b>Login</b></a>';
  }

  echo '</div>';
?>

<!--Title block-->
<div id="mm_title">
<?php
  // Parse config file
  $config = parse_ini_file('../config.ini');

  // Select all movies
  $query = "SELECT * from `movies`";
  $result = db_select($query);
  if($result === false) {
    die('Query Error: '.db_error());
  }

  // Count of all movies
  $count_movies = count($result);

  // Randomly choose a set of movies for display of poster thumbnails
  $movies = array();

  while(count($movies) < 14){
    $random_movie_index = mt_rand(1, count($result)) - 1;
    $movie_id = $result[$random_movie_index]['movie_id'];

    $thumbnail_file = sprintf('%s.%s', ($movie_id), $config['posters_image_format']);
    $thumbnail_path = sprintf('%s/%s', $config['posters_path'], $thumbnail_file);

    if(file_exists($thumbnail_path)){
      $movies[] = $result[$random_movie_index];
      array_splice($result, $random_movie_index, 1); // Remove the element at random index to prevent duplicates
    }
  }

  // Randomly choose a movie quote from thumbnail set
  $random_movie_index = mt_rand(1, count($movies)) - 1;
  $quote_movie = $movies[$random_movie_index];
  $quote = $quote_movie['movie_quote'];
  if(empty($quote)){
    $quote = "Toto, I've a feeling we're not in Kansas anymore.";
  }

  //Display title with movie count tooltip
  echo '<h1 class="title"><a href="index.php" class="title" title="'.$count_movies.' movies and counting...">Movie Men</a></h1>';

  //Display random movie quote
  echo '<div class="mm_quote" title="'.$quote_movie["movie_name"]." (".$quote_movie["movie_year"].")".'">'.$quote.'</div>';

  //Display random poster thumbnails
  echo '<div class="movie_posters">';
  echo '<table width=800><tr>';
  foreach($movies as $movie){
    echo '<td align=center><a href="poster.php?movie_id='.$movie["movie_id"].'"><img src="poster.php?movie_id='.$movie["movie_id"].'&thumbnail" title="'.$movie["movie_name"]." (".$movie["movie_year"].")".'"></a></td>';
  }
  echo '</tr></table>';
  echo '</div>';
?>
</div>
