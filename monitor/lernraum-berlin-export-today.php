<?php
	// https://stackoverflow.com/questions/2467945/how-to-generate-json-file-with-php/2467974

	include 'lernraum-berlin/config.php';

	function getSQLi() {
		return new mysqli(PSM_DB_HOST, PSM_DB_USER, PSM_DB_PASS, PSM_DB_NAME, PSM_DB_PORT);
	}

	function saveJSON($filename, $data) {
		$fp = fopen('../../../../www.ist-der-lernraum-noch-da.de/public/data/' . $filename, 'w');
		fwrite($fp, json_encode($data/*, JSON_PRETTY_PRINT*/));
		fclose($fp);
	}

	function getServersQuery()
	{
		return "SELECT *
			FROM `" . PSM_DB_PREFIX . "servers`
			ORDER BY `active` ASC, `status` DESC, `label` ASC";
	}

	function getServersUptimeQuery($beginDate, $endDate)
	{
		return "SELECT *
			FROM `" . PSM_DB_PREFIX . "servers_uptime`
			WHERE `date` >= '" . $beginDate . "' AND `date` <= '" . $endDate . "'";
	}

	function getDetailedHistoryForDay($day, $server_id, $day_records, $allowedServers)
	{
		$fiveMinutes = array_fill(0, 24 * 60 / 5, NULL);
		$history = null;

		foreach ($day_records as $day_record) {
			$secondsOfDay = strtotime($day_record['date'] . ' UTC') % 86400;
			$secondsSlot = intdiv($secondsOfDay, 5 * 60);

			$fiveMinutes[$secondsSlot] = round(floatval($day_record['latency']), 2);

			if ($day_record['status'] == 0) {
				$checks_failed++;
				$fiveMinutes[$secondsSlot] = 'off';
			}
		}

		$url = null;
		foreach($allowedServers['servers'] as $server) {
			if ($server_id == $server['server_id']) {
				$url = $server['url'];
				break;
			}
		}

		if ($url) {
			$history = array(
				'date' => $day,
				'url' => $url,
				'latency' => $fiveMinutes,
				'latency_max' => in_array('off', $fiveMinutes) ? 'off' : max($fiveMinutes),
			);
		}

		return $history;
	}

	function getSummaryHistoryForDay($day, $server_id, $day_records, $allowedServers)
	{
		$detailedHistory = getDetailedHistoryForDay($day, $server_id, $day_records, $allowedServers);
		$history = null;

		if ($detailedHistory) {			
			$history = array(
				'date' => $detailedHistory['date'],
				'url' => $detailedHistory['url'],
				'latency_max' => $detailedHistory['latency_max'],
			);
		}

		return $history;
	}

	function getAllowedServers() {
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
				$server_id = $row['server_id'];

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

				$servers[] = array('server_id' => $server_id, 'url' => $url);
			}
			$data['servers'] = $servers;

			$result->close();
		}

		$mysqli->close();
		return $data;
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

	function exportToday() {
		$mysqli = getSQLi();
		$queryDate = new \DateTime('-0 day 0:0:0');
		$beginDate = $queryDate->format('Y-m-d 00:00:00');
		$endDate = $queryDate->format('Y-m-d 23:59:59');

		if ($mysqli->connect_errno) {
			printf("Connect failed: %s\n", $mysqli->connect_error);
			exit();
		}

		if ($records = $mysqli->query(getServersUptimeQuery($beginDate, $endDate))) {
			$allowedServers = getAllowedServers();

			// first group all records by day and server_id
			$data_by_day = array();
			foreach ($records as $record) {
				$server_id = (int) $record['server_id'];
				$day = date('Y-m-d', strtotime($record['date']));
				if (!isset($data_by_day[$day][$server_id])) {
					$data_by_day[$day][$server_id] = array();
				}
				$data_by_day[$day][$server_id][] = $record;
			}

            // now get history data day by day
            $histories = array();
            foreach ($data_by_day as $day => $day_records) {
                foreach ($day_records as $server_id => $server_day_records) {
                    $history = getDetailedHistoryForDay($day, $server_id, $server_day_records, $allowedServers);
//                    $history = getSummaryHistoryForDay($day, $server_id, $server_day_records, $allowedServers);
					if ($history) {
						$histories[] = $history;
					}
                }
            }

			if (count($histories) > 0) {
				$date = $histories[0]['date'];
				$data['today'] = $histories;
				saveJSON('today-' . $date . '.json', $data);
				echo('exported today-' . $date . '.json');
			}

			$records->close();
		} else {
			echo('no records');
		}

		$mysqli->close();
	}

//	exportServers();
	exportToday();
?>