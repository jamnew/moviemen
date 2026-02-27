<?php
  include 'functions.php';

  $config = app_config();

  // Set content type
  $content_type = sprintf("image/%s", $config['posters_image_format']);

  // Determine whether display or thumbnail request
  if(isset($_REQUEST['thumbnail'])){
    $type = "thum";
  }
  else{
    $type = "disp";
  }

  // Connecting to & selecting database
  $link = db_connect();

  // Clean up user input
  $movie_id = db_quote($_REQUEST['movie_id']);

  // Select single movie
  $query = "SELECT * from `movies` WHERE `movie_id` = $movie_id";
  $result = db_select($query);

  // Close connection
  mysqli_close($link);

  // Read poster image file
  if($result !== false) {
    $row = $result[0];

    // Determine poster image path
    $file = sprintf('%s_%s.%s', $type, $row['movie_id'], $config['posters_image_format']);

    // Set path for poster image file
    $poster_path = sprintf('%s/%s', $config['posters_path'], $file);

    // Use placeholder if poster doesn't exist
    if(!file_exists($poster_path)){
      $poster_path = sprintf('assets/images/%s_default.%s', $type, $config['posters_image_format']);
    }
  }
  else{
    // Use placeholder if no movie found via requested id
    $poster_path = sprintf('assets/images/%s_default.%s', $type, $config['posters_image_format']);
  }

  // Load poster image file
  header("Content-Type: $content_type");
  readfile($poster_path);
?>
