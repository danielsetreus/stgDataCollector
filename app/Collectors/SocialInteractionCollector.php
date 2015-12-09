<?php
	namespace StG\Collectors;
	use \StG\DataCollector;
	use \StG\CollectorService;

	class SocialInteractionCollector implements CollectorService {

		/* Fetch URL:s for the 10 latest nodes of type stg_news
		Use 
			https://api.facebook.com/method/links.getStats?urls=--LINK--&format=json
		and
			http://urls.api.twitter.com/1/urls/count.json?url=--LINK--
		to fetch a total value of social media interactions. 

		Store in file per url (md5 or url), array with todays date and the value

		In some other file store the found 10 latest stg_news
		*/

		public function run() {

			$db = DataCollector::getDatabaseConnection("stg_m");
			// Query to get 10 latest and the URL
			$sql = "SELECT n.title, n.nid, u.source, u.alias
			FROM stg6_node AS n
			LEFT JOIN stg6_url_alias AS u ON u.source = CONCAT(  'node/', n.nid ) 
			WHERE n.status =  '1'
			AND (
			n.type =  'stg_news'
			OR n.type =  'stg_press_release'
			)
			ORDER BY n.created DESC 
			LIMIT 10";
			$res = $db->query($sql);
			$pages = array();
			foreach($db->resultToArray($res) as $page) {
				$fb = $this->getFbCount("https://shiptogaza.se/sv/" . $page['alias']);
				$tw = $this->getTwCount("https://shiptogaza.se/sv/" . $page['alias']);
				$total = $fb + $tw;
				$pages[] = array(
					'title' => $page['title'],
					'url' => "https://shiptogaza.se/sv/" . $page['alias'],
					'fb' => $fb,
					'tw' => $tw,
					'total' => $total
				);
			}

			DataCollector::addData("SocialInteraction:URLs", $pages);
			
		}

		private function getFbCount($url) {
			$cont = file_get_contents("https://api.facebook.com/method/links.getStats?urls=" . $url . "&format=json");
			$stats = json_decode($cont);
			return $stats[0]->total_count;
		}

		private function getTwCount($url) {
			$cont = file_get_contents("http://urls.api.twitter.com/1/urls/count.json?url=" . $url);
			$stats = json_decode($cont);
			return $stats->count;
		}
		




	}