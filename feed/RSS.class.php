<?php

class RSS
{
  public function RSS()
  {
    DEFINE('DB_USER', 'mm');
    DEFINE('DB_PASSWORD', '%PASSWORD%');
    DEFINE('DB_HOST', 'localhost');
    DEFINE('DB_NAME', 'mm');
  }

  public function GetFeed()
  {
    return $this->getDetails().$this->getItems();
  }

  private function xml_character_encode($string, $trans='')
  {
    $trans = (is_array($trans)) ? $trans : get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
    foreach ($trans as $k=>$v)
    {
      $trans[$k]= "&#".ord($k).";";
    }
    return strtr($string, $trans);
  }

  private function getDetails()
  {
    $detailsTable = "movies";
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
    mysqli_select_db($link, 'mm') or die('Could not select database');
    $query = "SELECT * FROM $detailsTable";
    $result = mysqli_query($link, $query);
    $count = mysqli_num_rows($result);

    $details = "<?xml version='1.0' encoding='ISO-8859-1' ?>
<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom'>
  <channel>
    <title>Movie Men</title>
    <link>http://moviemen.co/</link>
    <description>$count movies and counting...</description>
    <language>en-us</language>
    <atom:link href='http://moviemen.co/feed/' rel='self' type='application/rss+xml' />
  ";

    return $details;
  }

  private function getItems()
  {
    $itemsTable = "movies";
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
    mysqli_select_db($link, 'mm') or die('Could not select database');
    $query = "SELECT * FROM  $itemsTable ORDER BY movie_date_watched DESC LIMIT 10";
    $result = mysqli_query($link, $query);
    $items = '';
    while($row = mysqli_fetch_array($result))
    {
      $row = str_replace(" & ", " &amp; ", $row);

      // Title
      if(empty($row['movie_aka'])){
        $title = sprintf('%s (%s)', $row['movie_name'], $row['movie_year']);
      }
      else{
        $title = sprintf('%s (%s) aka %s', $row['movie_name'], $row['movie_year'], $row['movie_aka']);
      }

      // ID
      $id = $row['movie_id'];

      // Date Watched
      $date_watched = date('r', strtotime($row['movie_date_watched'].' 19:30:00'));

      // Description
      $description = $row['movie_description'];

      $items .= "  <item>
      <title>$title</title>
      <link>http://moviemen.co/index#$id</link>
      <pubDate>$date_watched</pubDate>
      <description><![CDATA[$description]]></description>
      <guid>http://moviemen.co/index#$id</guid>
    </item>
  ";
    }
    $items .= '</channel>
</rss>';

    return $items;
  }
}
?>
