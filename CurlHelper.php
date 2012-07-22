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
		curl_close($ch);

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
		curl_close($ch);

		return $data;
	}
}
