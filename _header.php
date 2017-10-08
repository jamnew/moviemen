<html>
<head>
  <title>Movie Men (MMVII)</title>
  <link href="screen.css" media="screen" rel="stylesheet" title="Default" type="text/css">
  <link href="handheld.css" media="handheld" rel="stylesheet" title="Handheld" type="text/css">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=2017100701">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=2017100701">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=2017100701">
  <link rel="manifest" href="/manifest.json?v=2017100701">
  <link rel="mask-icon" href="/safari-pinned-tab.svg?v=2017100701" color="#378dce">
  <link rel="shortcut icon" href="/favicon.ico?v=2017100701">
  <meta name="theme-color" content="#378dce">
  <script src="https://moviemen.co/jquery-3.1.0.min.js" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $("#movie_name").change(function(){
        $.ajax({
          url: '/images.php',
          data: {
            name: $('#movie_name').val(),
            year: $('#movie_year').val()
          }
        }).done(function(data){
          $("#images").html(data);
        });
      });
      $("#movie_year").change(function(){
        $.ajax({
          url: '/images.php',
          data: {
            name: $('#movie_name').val(),
            year: $('#movie_year').val()
          }
        }).done(function(data){
          $("#images").html(data);
        });
      });
      $("#refresh").mousedown(function(){
        $.ajax({
          url: '/images.php',
          data: {
            name: $('#movie_name').val(),
            year: $('#movie_year').val()
          }
        }).done(function(data){
          $("#images").html(data);
        });
      });
    });
    function lightbox_toggle(img_src){
      var lightbox_background = document.getElementById('lightbox_background');
      var lightbox_image_container = document.getElementById('lightbox_image_container');
      var lightbox_image = document.getElementById('lightbox_image');

      // Toggle lightbox
      if(img_src){
        // Set img src
        lightbox_image.src = img_src;

        // Show lightbox
        lightbox_background.style.display = "flex";
        lightbox_image_container.style.display = "block";
        lightbox_image.style.display = "inline";
      }
      else{
        // Hide lightbox
        lightbox_background.style.display = "none";
        lightbox_image_container.style.display = "none";
        lightbox_image.style.display = "none";
      }
    }
  </script>
</head>
<body>

<!--Lightbox background-->
<div id="lightbox_background" class="lightbox_background" onclick="lightbox_toggle()">
  <div id="lightbox_image_container" class="lightbox_image_container">
    <img id="lightbox_image" class="lightbox_image" src="" />
  </div>
</div>

<!--Login link-->
<?php
  echo '<div class="mm_login">';

  if ($_SESSION['authorised'] == true) { /* Check if user has been recently authorised */
    echo 'Logged in as '.$_SESSION['user'];
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

    $poster_thumbnail_file = sprintf('thum_%s.%s', ($movie_id), $config['posters_image_format']);
    $poster_thumbnail_path = sprintf('%s/%s', $config['posters_path'], $poster_thumbnail_file);

    // Select movie poster
    $movies[] = $result[$random_movie_index];

    // Remove the element at random index to prevent duplicates
    array_splice($result, $random_movie_index, 1);
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
  if(empty($quote_movie['movie_aka'])){
    echo '<div class="mm_quote" title="'.$quote_movie["movie_name"].' ('.$quote_movie["movie_year"].')">'.$quote.'</div>';
  }
  else{
    echo '<div class="mm_quote" title="'.$quote_movie["movie_name"].' ('.$quote_movie["movie_year"].') aka '.$quote_movie["movie_aka"].'">'.$quote.'</div>';
  }

  //Display random poster thumbnails
  echo '<div class="movie_posters">';
  echo '<table width=800><tr>';
  foreach($movies as $movie){
    if(empty($movie['movie_aka'])){
      echo '<td align=center><img src="poster.php?movie_id='.$movie["movie_id"].'&thumbnail" height="75" title="'.$movie["movie_name"].' ('.$movie["movie_year"].')" onclick="lightbox_toggle(\'poster.php?movie_id='.$movie["movie_id"].'\')"></td>';
    }
    else{
      echo '<td align=center><img src="poster.php?movie_id='.$movie["movie_id"].'&thumbnail" height="75" title="'.$movie["movie_name"].' ('.$movie["movie_year"].') aka '.$movie['movie_aka'].'" onclick="lightbox_toggle(\'poster.php?movie_id='.$movie["movie_id"].'\')"></td>';
    }
  }
  echo '</tr></table>';
  echo '</div>';
?>
</div>
