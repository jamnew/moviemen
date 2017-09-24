<?php
  include 'functions.php';

  // Connecting to & selecting database
  $link = db_connect();

  // Clean up user input
  $movie_id = db_quote($_REQUEST['movie_id']);

  // Select single movie
  $query = "SELECT * from `movies` WHERE `movie_id` = $movie_id";
  $result = db_select($query);

  // Read poster image file
  if($result !== false) {
    $row = $result[0];

    // Parse config file
    $config = parse_ini_file('../config.ini');

    // Determine whether image or thumbnail
    if(isset($_REQUEST['thumbnail'])){
      $file = sprintf('%s.%s', $row['movie_id'], $config['posters_image_format']);
    }
    else{
      $file = sprintf('b%s.%s', $row['movie_id'], $config['posters_image_format']);
    }

    // Set content type
    $content_type = sprintf("image/%s", $config['posters_image_format']);

    // Set path for poster image file
    $poster_path = sprintf('%s/%s', $config['posters_path'], $file);

    // Load poster image file
    header("Content-Type: $content_type");
    readfile($poster_path);
  }

  mysqli_close($link); /* Closing connection */

  /* TODO */
  // Create a dummy poster and thumbnail, store in ROOT/assets/images
  // Use dummy poster when real poster does not exist or param not passed
?>
