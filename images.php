<?php
  //error_reporting(E_ALL);
  //ini_set('display_errors', 1);

  if(!empty($_REQUEST['name'])){
    set_include_path(get_include_path() . PATH_SEPARATOR . '../lib/google-api-php-client/src');

    require_once 'Google/autoload.php';
    require_once 'Google/Client.php';

    $client = new Google_Client();
    $client->setApplicationName("Movie Men");
    $client->setDeveloperKey("AIzaSyDjdN4w7rXB0X3dsxglGUCs5YyRAhZy_1E");

    $customsearchService = new Google_Service_Customsearch($client);

    $name = $_REQUEST['name'];
    $start = '1';

    if(!empty($_REQUEST['start'])){
      $start = $_REQUEST['start'];
    }

    $q = "$name movie poster";

    $optParams = array(
      'cx' => '004093374540663055336:m2srg0atvps',
      'filter' => '1',
      'num' => '10',
      'searchType' => 'image',
      'siteSearch' => 'alamy.com',
      'siteSearchFilter' => 'e',
      'start' => $start
    );

    $cse = $customsearchService->cse; // Google_Service_Customsearch_Cse_Resource
    $search = $cse->listCse($q, $optParams); // Google_Service_Customsearch_Search
    $items = $search->getItems(); // Google_Service_Customsearch_Result

    echo "<table>";

    for($i = 0; $i < 3; $i++){
      echo "<tr>";
      foreach($items as $item){
        $image = $item->getImage(); // Google_Service_Customsearch_ResultImage
        $aspect = $image->getWidth() / $image->getHeight();

        if($aspect < 0.8){
          switch($i){
            case 0:
              $link = $item->getLink();
              $thumb = $image->getThumbnailLink();
              echo sprintf("<td style='text-align: center' onclick='document.getElementById(\"movie_poster_image\").value=\"%s\"'><img src='%s'></td>", $link, $thumb);
              break;
            case 1:
              $width = $image->getWidth();
              $height = $image->getHeight();
              echo sprintf("<td style='text-align: center'>%s x %s</td>", $width, $height);
              break;
            case 2:
              $bytesize = $image->getByteSize();
              $kilobytesize = intval(($bytesize/1000));
              echo sprintf("<td style='text-align: center'>%s KB</td>", $kilobytesize);
              break;
          }
        }
      }
      echo "</tr>";
    }

    echo "</table>";
  }
?>
