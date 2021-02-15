<?php
	// https://stackoverflow.com/questions/2467945/how-to-generate-json-file-with-php/2467974

	include 'lernraum-berlin/config.php';

	function getSQLi() {
		return new mysqli(PSM_DB_HOST, PSM_DB_USER, PSM_DB_PASS, PSM_DB_NAME, PSM_DB_PORT);
	}

	function saveJSON($filename, $data) {
		$fp = fopen('../../../../www.ist-der-lernraum-noch-da.de/public/data/' . $filename, 'w');
		fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
		fclose($fp);
	}

	function getServersQuery()
	{
		return "SELECT *
			FROM `" . PSM_DB_PREFIX . "servers`
			ORDER BY `active` ASC, `status` DESC, `label` ASC";
	}

	function exportServers() {
		$mysqli = getSQLi();
		$data = array();
		$servers = array();

		if ($mysqli->connect_errno) {
			printf("Connect failed: %s\n", $mysqli->connect_error);
			exit();
		}

		if ($result = $mysqli->query(getServersQuery())) {
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$url = $row['ip'];
				$active = $row['active'] == 'yes' ? 'true' : 'false';
				$status = $row['status'];

				$url = str_replace('https://www.lernraum-berlin.de', '', $url);
				$url = str_replace('https://portal.lernraum-berlin.de', '', $url);
				$url = str_replace('https://bbb.lernraum-berlin.de', '/bbb/', $url);
				$url = str_replace('http://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v1und2', '/v1und2/my/', $url);
				$url = str_replace('https://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v1und2', '/v1und2/my/', $url);
				$url = str_replace('http://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v3und4', '/v3und4/my/', $url);
				$url = str_replace('https://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v3und4', '/v3und4/my/', $url);
				$url = str_replace('http://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v1', '/v1/my/', $url);
				$url = str_replace('https://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v1', '/v1/my/', $url);
				$url = str_replace('http://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v2', '/v2/my/', $url);
				$url = str_replace('https://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v2', '/v2/my/', $url);
				$url = str_replace('http://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v3', '/v3/my/', $url);
				$url = str_replace('https://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v3', '/v3/my/', $url);
				$url = str_replace('http://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v4', '/v4/my/', $url);
				$url = str_replace('https://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=v4', '/v4/my/', $url);
				$url = str_replace('http://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=osz', '/osz/my/', $url);
				$url = str_replace('https://tursics.de/service/monitoring/lernraum-berlin-bot.php?cluster=osz', '/osz/my/', $url);

				if (stripos($url, 'lernraum-berlin-export')) {
					continue;
				}

				$responseTime = $row['rtime'];
				$lastOnline = $row['last_online'];
				$lastOffline = $row['last_offline'];
				$lastCheck = $row['last_check'];

				$servers[] = array('url' => $url, 'active' => $active, 'status' => $status,
					 'responseTime' => $responseTime, 'lastOnline' => $lastOnline, 'lastOffline' => $lastOffline, 'lastCheck' => $lastCheck,
				);
			}
			$data['servers'] = $servers;
			saveJSON('servers.json', $data);
			echo('exported servers.json');

			$result->close();
		}

		$mysqli->close();
	}

	exportServers();
?>