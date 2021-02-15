<?php

	// use a cron job service like https://cron-job.org/de/members/jobs/

	// variables
	$uriMoodleSwitch = 'https://www.lernraum-berlin.de/';
	$uriMoodleLogin = 'https://portal.lernraum-berlin.de/moodle/login/index.php';
	$userAgent = 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87 (tursics.de monitoring robot)';
	$cookiefile = '../../../cookie-lernraum/lernraum-berlin-cookie.txt';
	$credentialUsername = 'username';
	$credentialPassword = 'password';

	function parseHTML($html, $searchKey, $searchStart, $searchEnd) {
		$pos = 0;
		$value = '';

		do {
			$pos = stripos($html, $searchKey, $pos);
			if ($pos !== false) {
				$pos = stripos($html, $searchStart, $pos + strlen($searchKey)) + strlen($searchStart);
				$endpos = stripos($html, $searchEnd, $pos);

				if ($endpos !== false) {
					return substr($html, $pos, $endpos - $pos);
				}
			}
		} while($pos !== false);

		return '';
	}

	function removeCookie() {
		global $cookiefile;

		if (file_exists($cookiefile)) {
			unlink($cookiefile);
		}
	}

	function getLoginPage($cluster) {
		global $uriMoodleSwitch;
		global $userAgent;
		global $cookiefile;

		$url = $uriMoodleSwitch . $cluster;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $url);
//		curl_setopt($ch, CURLOPT_REFERER,        $url_ref);
		curl_setopt($ch, CURLOPT_USERAGENT,      $userAgent);
		curl_setopt($ch, CURLOPT_COOKIEFILE,     $cookiefile);
		curl_setopt($ch, CURLOPT_COOKIEJAR,      $cookiefile);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_NOBODY,         false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST,           false);

		$ret = curl_exec($ch);
		curl_close($ch);

		$error = parseHTML($ret, 'id="loginerrormessage"', '>', '</');

		return array(
			'error' => $error,
			'logintoken' => parseHTML($ret, 'name="logintoken"', 'value="', '"'),
			'body' => $ret,
		);
	}

	function loginMoodle($logintoken) {
		global $uriMoodleLogin;
		global $userAgent;
		global $cookiefile;
		global $credentialUsername;
		global $credentialPassword;

		$url = $uriMoodleLogin;
		$post = array(
			'anchor' => '',
			'logintoken' => $logintoken,
			'username' => $credentialUsername,
			'password' => $credentialPassword,
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $url);
//		curl_setopt($ch, CURLOPT_REFERER,        $url_ref);
		curl_setopt($ch, CURLOPT_USERAGENT,      $userAgent);
		curl_setopt($ch, CURLOPT_COOKIEFILE,     $cookiefile);
		curl_setopt($ch, CURLOPT_COOKIEJAR,      $cookiefile);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_NOBODY,         false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

		$coded = array();
		foreach( $post as $key => $value) {
			$coded[] = $key . '=' . urlencode($value);
		}
		$str = implode('&', $coded);
		curl_setopt($ch, CURLOPT_POST,       true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $str);

		$ret = curl_exec($ch);
		curl_close($ch);

		$error = parseHTML($ret, 'id="loginerrormessage"', '>', '</');
		// Ungültige Anmeldedaten. Versuchen Sie es noch einmal!

		return array(
			'error' => $error,
			'logininfo' => parseHTML($ret, 'class="logininfo"', '>', '</div'),
			'body' => $ret,
		);
	}

	function autologin($cluster) {
		removeCookie();

		$loginPage = getLoginPage($cluster);
		if ($loginPage['error'] == '') {
			if ($loginPage['logintoken'] != '') {
				$resultPage = loginMoodle($loginPage['logintoken']);

				if ($resultPage['error'] == '') {
					$userName = stripos($resultPage['logininfo'], 'Thomas Tursics', 0);
					if ($userName !== false) {
						header('HTTP/1.1 200 OK');
//						echo('Yeah');

						$test = parseHTML($resultPage['body'], '<nav', 'http://www.lernraum-berlin.de/', '/');
						var_dump($test);
					} else {
						header('HTTP/1.1 401 Unauthorized - ' . $resultPage['logininfo']);
						echo($resultPage['logininfo']);
					}
				} else {
					header('HTTP/1.1 403 Forbidden - ' . $resultPage['error']);
					echo($resultPage['error']);
				}
			} else {
				header('HTTP/1.1 401 Unauthorized - no login token');
				echo('no login token');
			}
		} else {
			header('HTTP/1.1 404 - ' . $loginPage['error']);
			echo($loginPage['error']);
		}
	}

	if (isset($_GET['cluster'])) {
		// v1und2
		// v3und4
		// osz
		autologin($_GET['cluster']);
	} else {
		header('HTTP/1.1 400 Bad Request - parameter cluster missing');
		echo('parameter cluster missing');
	}
?>