<?

class RSS
{
	public function RSS()
	{
		require_once ('mysql_connect.php');
	}

	public function GetFeed()
	{
		return $this->getDetails() . $this->getItems();
	}

	private function dbConnect()
	{
		DEFINE ('LINK', mysql_connect (DB_HOST, DB_USER, DB_PASSWORD));
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
		$this->dbConnect($detailsTable);
		$query = "SELECT * FROM ". $detailsTable;
		$result = mysql_query ($query, LINK);
		$count = mysql_num_rows($result);

		$details = '<?xml version="1.0" encoding="ISO-8859-1" ?>
			<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
				<channel>
					<title>Movie Men</title>
					<link>http://jasper.ods.org/~jamnew/moviemen/</link>
					<description>' .$count. ' movies and counting ... </description>
					<language>en-us</language>
					<atom:link href="http://jasper.ods.org/~jamnew/moviemen/feed/" rel="self" type="application/rss+xml" />';

		return $details;
	}

	private function getItems()
	{
		$itemsTable = "movies";
		$this->dbConnect($itemsTable);
		$query = "SELECT * FROM ". $itemsTable . " ORDER BY movie_date_watched DESC";
		$result = mysql_query ($query, LINK);
		$items = '';
		while($row = mysql_fetch_array($result))
		{
			$row = str_replace(" & ", " &amp; ", $row);
			$items .= '<item>
				<title>'. $row["movie_name"] .'</title>
				<link>http://jasper.ods.org/~jamnew/moviemen/index.php#'. $row['movie_id'] .'</link>
				<pubDate>'. date("r", strtotime($row['movie_date_watched'] ." 19:30:00")) .'</pubDate>
				<description><![CDATA['. $row["movie_description"] .']]></description>
				<guid>http://jasper.ods.org/~jamnew/moviemen/index.php#'. $row['movie_id'] .'</guid>
			</item>';
		}
		$items .= '</channel>
				</rss>';
		return $items;
	}

}

?>
