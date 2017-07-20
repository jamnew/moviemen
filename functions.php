<?php
  function db_connect() {
    // Define connection as a static variable, to avoid connecting more than once
    static $connection;

    // Try and connect to the database, if a connection has not been established yet
    if(!isset($connection)) {
      // Load configuration as an array. Use the actual location of your configuration file
      $config = parse_ini_file('../config.ini');
      $connection = mysqli_connect('localhost', $config['username'], $config['password'], $config['dbname']);

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
    $result = mysqli_query($connection,$query);

    return $result;
  }

  function db_error() {
    $connection = db_connect();

    return mysqli_error($connection);
  }
?>
