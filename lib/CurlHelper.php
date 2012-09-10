<?php

class CurlHelper
{
	static function getUrl($url, $additionalConfig = array()) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt_array($ch, $additionalConfig);
		$data = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if (in_array($http_status, array(400, 401, 403, 404, 500)))
			throw new Exception("url $url return $http_status response code");

		return $data;
	}

	static function postUrl($url, $postQuery, $additionalConfig = array()) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postQuery);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt_array($ch, $additionalConfig);
		$data = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if (in_array($http_status, array(400, 401, 403, 404, 500)))
			throw new Exception("url $url return $http_status response code. postFields: ".print_r($postQuery, true));

		return $data;
	}

	static function downloadToFile($url, $toFile, $additionalConfig = array()) {
		$fp = fopen($toFile, 'w');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt_array($ch, $additionalConfig);
		curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		fclose($fp);

		if (in_array($http_status, array(400, 401, 403, 404, 500)))
			throw new Exception("url $url return $http_status response code");
	}
}
