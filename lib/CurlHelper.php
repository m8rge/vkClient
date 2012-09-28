<?php

class CurlHelper
{
	/**
	 * @param string $url
	 * @param array $additionalConfig
	 * @param int $retryCount
	 * @throws Exception
	 * @return mixed downloaded data
	 */
	static function getUrlFailSafe($url, $additionalConfig = array(), $retryCount = 5) {
		for ($i=0; $i<$retryCount; $i++) {
			try {
				return self::getUrl($url, $additionalConfig);
			} catch (Exception $e) {
				if ($i+1 == $retryCount)
					throw $e;
			}
		}

		return false;
	}

	/**
	 * @param string $url
	 * @param array $additionalConfig
	 * @return mixed downloaded data
	 * @throws Exception
	 */
	static function getUrl($url, $additionalConfig = array()) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt_array($ch, $additionalConfig);
		$data = curl_exec($ch);
		if ($data === false)
			throw new Exception("retrieving url $url failed with error: ".curl_error($ch));
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if (in_array($http_status, array(400, 401, 403, 404, 500)))
			throw new Exception("url $url return $http_status response code");

		return $data;
	}

	/**
	 * @param string $url
	 * @param array $postQuery
	 * @param array $additionalConfig
	 * @return mixed returned data
	 * @throws Exception
	 */
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
		if ($data === false)
			throw new Exception("posting to $url failed with error: ".curl_error($ch).". postFields: ".print_r($postQuery, true));
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if (in_array($http_status, array(400, 401, 403, 404, 500)))
			throw new Exception("url $url return $http_status response code. postFields: ".print_r($postQuery, true));

		return $data;
	}

	/**
	 * @param string $url
	 * @param string $toFile file name
	 * @param array $additionalConfig
	 * @throws Exception
	 */
	static function downloadToFile($url, $toFile, $additionalConfig = array()) {
		$fp = fopen($toFile, 'w');
		$timeout = 5;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt_array($ch, $additionalConfig);
		$res = curl_exec($ch);
		if ($res === false)
			throw new Exception("retrieving url $url failed with error: ".curl_error($ch));
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		fclose($fp);

		if (in_array($http_status, array(400, 401, 403, 404, 500)))
			throw new Exception("url $url return $http_status response code");
	}
}
