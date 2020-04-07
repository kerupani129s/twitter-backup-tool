<?php

class Twitter {

	private $apiKey;
	private $apiSecretKey;

	private $accessToken;
	private $accessTokenSecret;

	function __construct($user) {

		$config = json_decode(@file_get_contents(__DIR__ . '/config.json'));

		$this->apiKey       = $config->api_key;
		$this->apiSecretKey = $config->api_secret_key;

		$this->accessToken       = $config->profiles->$user->access_token;
		$this->accessTokenSecret = $config->profiles->$user->access_token_secret;

	}

	public function get($path, $params) {

		$url = $this->getRestUrl($path);

		return $this->getByFullUrl($url, $params);

	}

	public function getByFullUrl($url, $params) {

		$method = 'GET';

		$context = [
			'http' => [
				'method' => $method,
				'header' => [
					'Authorization: ' . $this->getAuthorizationHeader($method, $url, $params)
				]
			]
		];

		$requestUrl = (! $params ? $url : $url . '?' . http_build_query($params));

		$json = @file_get_contents($requestUrl, false, stream_context_create($context));

		return json_decode($json);

	}

	private function getAuthorizationHeader($method, $url, $params) {

		// 
		$oauthParams = [
			'oauth_consumer_key'     => $this->apiKey,
			'oauth_nonce'            => $this->getNonce(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp'        => time(),
			'oauth_token'            => $this->accessToken,
			'oauth_version'          => '1.0'
		];

		$allParams = $this->percentEncodeParams(array_merge($params, $oauthParams));

		ksort($allParams);

		// 
		$signature = $this->getSignature($method, $url, $allParams);

		// 
		$oauthHeaderParams = $this->percentEncodeParams(array_merge($oauthParams, ['oauth_signature' => $signature]));

		$oauthHeaderPairs = array_map(function ($value, $key) {
			return $key . '="' . $value . '"';
		}, array_values($oauthHeaderParams), array_keys($oauthHeaderParams));

		return 'OAuth ' . implode(', ', $oauthHeaderPairs);

	}

	private function getSignature($method, $url, $allParams) {

		$allQuery = http_build_query($allParams, '', '&', PHP_QUERY_RFC3986);

		// 
		$signatureBase = strtoupper($method) . '&' . rawurlencode($url) . '&' . rawurlencode($allQuery);

		$signatureKey = rawurlencode($this->apiSecretKey) . '&' . rawurlencode($this->accessTokenSecret);

		// 
		$hash = hash_hmac('sha1', $signatureBase, $signatureKey, TRUE);

		return base64_encode( $hash );

	}

	private function getRestUrl($path) {
		return 'https://api.twitter.com/1.1/' . $path . '.json'; // TODO: 
	}

	private function percentEncodeParams($params) {

		$encodedParams = [];

		foreach ($params as $key => $value) {
			$encodedParams[rawurlencode($key)] = rawurlencode($value);
		}

		return $encodedParams;

	}

	private function getNonce() {
		return bin2hex(random_bytes(32));
	}

}
