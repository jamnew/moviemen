<?
  DEFINE ('DB_USER', 'mm_rss');
  DEFINE ('DB_PASSWORD', '');
  DEFINE ('DB_HOST', 'localhost');
  DEFINE ('DB_NAME', 'mm');

  // Make the connnection and then select the database.
  $dbc = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) OR die ('Could not connect to MySQL: ' . mysql_error() );
  mysql_select_db (DB_NAME) OR die ('Could not select the database: ' . mysql_error() );
?>
