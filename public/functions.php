<?php
  function env_flag($name, $default = false) {
    $value = getenv($name);
    if($value === false || $value === '') {
      return $default;
    }

    $value = strtolower(trim($value));
    return in_array($value, array('1', 'true', 'yes', 'on'), true);
  }

  function app_config() {
    static $config;

    if(!isset($config)) {
      $project_root = dirname(__DIR__);
      $posters_path = getenv('POSTERS_PATH');
      $temp_path = getenv('TEMP_PATH');
      $posters_image_format = getenv('POSTERS_IMAGE_FORMAT');
      $read_only_mode = env_flag('READ_ONLY_MODE', true);

      if(!$posters_path) {
        $posters_path = $project_root.'/storage/posters';
      }
      if(!$temp_path) {
        $temp_path = $project_root.'/storage/tmp';
      }
      if(!$posters_image_format) {
        $posters_image_format = 'jpg';
      }

      $config = array(
        'posters_path' => $posters_path,
        'temp_path' => $temp_path,
        'posters_image_format' => $posters_image_format,
        'read_only_mode' => $read_only_mode
      );
    }

    return $config;
  }

  function db_connect() {
    // Define connection as a static variable, to avoid connecting more than once
    static $connection;

    // Try and connect to the database, if a connection has not been established yet
    if(!isset($connection)) {
      $db_host = getenv('DB_HOST');
      $db_user = getenv('DB_USER');
      $db_pass = getenv('DB_PASSWORD');
      $db_name = getenv('DB_NAME');

      if(!$db_host || !$db_user || !$db_name) {
        die('Missing required DB environment variables: DB_HOST, DB_USER, DB_NAME');
      }

      $connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

      // Set charset to utf8 if connection established
      if($connection !== false){
        mysqli_set_charset($connection, 'utf8');
      }
    }

    // If connection was not successful, handle the error
    if($connection === false) {
      die('Connect Error: '.mysqli_connect_error());
    }

    return $connection;
  }

  function db_query($query) {
    // Connect to the database
    $connection = db_connect();

    // Query the database
    $result = mysqli_query($connection, $query);

    return $result;
  }

  function db_error() {
    $connection = db_connect();

    return mysqli_error($connection);
  }

  function db_select($query) {
    $rows = array();
    $result = db_query($query);

    // If query failed, return `false`
    if($result === false) {
      return false;
    }

    // If query was successful, retrieve all the rows into an array
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }

    return $rows;
  }

  function db_quote($value) {
    $connection = db_connect();

    return mysqli_real_escape_string($connection, $value);
  }

  function manage_session($session_items = NULL) {
    // Start session
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
    if(empty($session_items) || !is_array($session_items)) {
      return;
    }
    // Update session items
    foreach($session_items as $k => $v){
      if(is_null($v)){
        if(isset($_SESSION[$k])){
          unset($_SESSION[$k]);
        }
      }
      else{
        $_SESSION[$k] = $v;
      }
    }
  }
?>
