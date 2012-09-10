<?php

require_once(__DIR__.'/lib/phpQuery/phpQuery/phpQuery.php');
require_once(__DIR__.'/lib/CurlHelper.php');

class VkClient
{
	public $accessToken = null;

	/**
	 * @param integer $appId VK Application ID
	 * @param string $email Email used to login
	 * @param string $password Password used to login
	 * @param integer $scope Access rights bit mask
	 * @param string $devNull URL to dummy file. File must exists and be visible to script.
	 * @throws Exception
	 */
	function __construct($appId, $email, $password, $scope, $devNull)
	{
		$query = array(
			'client_id' => $appId,
			'scope' => $scope,
			'redirect_uri' => $devNull,
			'display' => 'popup',
			'response_type' => 'token',
			'_hash' => 0,
		);
		$queryString = http_build_query($query);
		$oauthPage = CurlHelper::getUrl("http://oauth.vk.com/oauth/authorize?$queryString", array(
			CURLOPT_COOKIESESSION => true, // create new session
		));
		$phpQueryObject = phpQuery::newDocumentHTML($oauthPage);
		if ($phpQueryObject->find("form input[name=email]")->count() == 1) {
			$formData = array();
			foreach ($phpQueryObject->find("form input") as $node) {
				$jNode = pq($node);
				$formData[ $jNode->attr('name') ] = $jNode->attr('value');
			}
			$formData['email'] = $email;
			$formData['pass'] = $password;
			$formAction = $phpQueryObject->find("form")->attr('action');

			$cookieFile = tempnam(sys_get_temp_dir(), 'vkCookie');
			$loginData = CurlHelper::postUrl($formAction, $formData, array(
				CURLOPT_HEADER => 1,
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_COOKIEJAR => $cookieFile,
				CURLOPT_COOKIEFILE => $cookieFile,
			));
			if (preg_match('#access_token=(\w+)#', $loginData, $matches)) {
				$this->accessToken = $matches[1];
				unlink($cookieFile);
				return;
			} elseif (preg_match('#onclick\s*=\s*"\s*return\s+allow\s*\(\s*\);?\s*"#', $loginData, $matches)) {
				// we need approve application
				if (!preg_match('#function\s+allow\s*\(\s*\)\s*{.*?"(https://[^"]+)"#s', $loginData, $matches))
					throw new Exception('can\'t get access token');
				$loginData = CurlHelper::getUrl($matches[1], array(
					CURLOPT_HEADER => 1,
					CURLOPT_FOLLOWLOCATION => 1,
					CURLOPT_COOKIEJAR => $cookieFile,
					CURLOPT_COOKIEFILE => $cookieFile,
				));
				if (preg_match('#access_token=(\w+)#', $loginData, $matches)) {
					$this->accessToken = $matches[1];
					unlink($cookieFile);
					return;
				}
			} elseif ($phpQueryObject->find("form input[name=email]")->count() == 1) {
				throw new Exception('wrong login/password');
			}
		}

		throw new Exception('can\'t get access token');
	}

	/**
	 * @param string $apiName
	 * @param array $params
	 * @return mixed Contents of field response
	 * @throws Exception
	 */
	public function makeApiCall($apiName, $params) {
		$query = array_merge(array(
			'access_token' => $this->accessToken
		), $params);
		$queryString = http_build_query($query);
		$answer = CurlHelper::getUrl("https://api.vk.com/method/$apiName?$queryString");
		$answer = json_decode($answer, true);
		if (isset($answer['error']) || !isset($answer['response'])) {
			throw new Exception(print_r($answer, true));
		}
		return $answer['response'];
	}
}
