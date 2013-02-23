<?php

class GitHubAPI {

	private static $CLIENT_ID = '115d413f5ad3eb4c0a01';
	private static $CLIENT_SECRET = 'GITHUB_CLIENT_SECRET';
	private static $ORGANISATION = 'Kokathon';
	private static $REPO_BASE_URL = 'http://kokarn.com/kokathon/repos/';
	private static $HOOK_URL = 'http://kokarn.com/kokathon/AutoDeploy/buildhook.php';

	private static function fieldsToString($fields) {
		return utf8_encode(http_build_query($fields, '', '&'));
	}

	private static function fieldsToJson($fields) {
		return json_encode($fields);
	}

	public static function getAccessToken($code) {
		$url = 'https://github.com/login/oauth/access_token';
		$fields = array(
			'client_id' => self::$CLIENT_ID,
			'client_secret' => self::$CLIENT_SECRET,
			'code' => $code
			);

		$fieldsString = self::fieldsToString($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array (
	        "Accept: application/json"
	    ));
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);

		$result = curl_exec($ch);

		curl_close($ch);

		$json = json_decode($result);

		if (isset($json->access_token)) {
			return $json->access_token;
		}

		return false;
	}

	private static function formatName($name) {
		$search = array(' ', 'å', 'ä', 'ö', ',', '<', '>', ':', ';');
		$replace = '-';

		return str_replace($search, $replace, $name);
	}

	private static function formatDescription($desc) {
		$search = array('å', 'ä', 'ö');
		$replace = array('a', 'a', 'o');

		return str_replace($search, $replace, $desc);	
	}

	public static function createRepository($accessToken, $name, $description = '') {

		$url = 'https://api.github.com/orgs/' . self::$ORGANISATION . '/repos?access_token=' . $accessToken;
		$fields = array(
			'name' => $name,
			'description' => $description,
			'auto_init' => true,
			'homepage' => self::$REPO_BASE_URL . self::formatName($name)
			);
		$fieldsString = self::fieldsToJson($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array("Content-Type: application/x-www-form-urlencoded",
				'Content-Length: ' . strlen($fieldsString)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);

		$result = curl_exec($ch);

		curl_close($ch);

		$json = json_decode($result);

		return $json;
	}

	public static function addHook($accessToken, $repo) {
		$url = 'https://api.github.com/repos/' . $repo->owner->login . '/' . $repo->name . '/hooks?access_token=' . $accessToken;

		$fields = array(
			'name' => 'web',
			'active' => true,
			'config' => array('url' => self::$HOOK_URL)
			);

		$fieldsString = json_encode($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array("Content-Type: application/x-www-form-urlencoded",
				'Content-Length: ' . strlen($fieldsString)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);

		$result = curl_exec($ch);

		curl_close($ch);

		$json = json_decode($result);

		return $json;

	}

}

?>