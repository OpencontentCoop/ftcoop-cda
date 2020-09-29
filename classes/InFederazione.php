<?php

class InFederazione
{
	private static $token;

	public static function getToken()
	{
		if (self::$token === null){
			$ini = eZINI::instance('infederazione.ini');
			$settings = $ini->group('client_credentials');

			$url = $settings['url'];
		    $params = [
		        'client_id' => $settings['client_id'],
		        'client_secret' => $settings['client_secret'],
		        'grant_type' => 'client_credentials',
		        'resource' => $settings['resource'],
		    ];
		    $paramsString = http_build_query($params, '', '&');

		    $ch = curl_init($url);
		    $options = array(
		        CURLOPT_POST => true,
		        CURLOPT_POSTFIELDS => $paramsString,
		        CURLOPT_HTTPHEADER => array(
		            'Content-Type: application/x-www-form-urlencoded'
		        ),
		        CURLINFO_HEADER_OUT => false,
		        CURLOPT_HEADER => false,
		        CURLOPT_RETURNTRANSFER => true,
		        CURLOPT_SSL_VERIFYPEER => true
		    );
		    curl_setopt_array($ch, $options);
		    self::$token = curl_exec($ch);
		    curl_close($ch);		    
		}

		return json_decode(self::$token, true);
	}

	public static function userExists($username)
	{
		if (empty($username)){
			return false;
		}
		
		$url = "https://www.ftcoop.it/InFederazioneServices/api/v1/Riuniamoci/userExists/{$username}";
		$params = [
	        'token' => self::getToken()['access_token']
	    ];
	    $paramsString = http_build_query($params, '', '&');

		$ch = curl_init($url);
	    $options = array(
	        CURLOPT_POST => true,
	        CURLOPT_POSTFIELDS => $paramsString,
	        CURLOPT_HTTPHEADER => array(
	            "Authorization: Bearer " . self::getToken()['access_token']
	        ),
	        CURLINFO_HEADER_OUT => false,
	        CURLOPT_HEADER => false,
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_SSL_VERIFYPEER => true
	    );
	    curl_setopt_array($ch, $options);
	    $response = curl_exec($ch);
	    curl_close($ch);		 

	    $data = json_decode($response, true);
	    if (isset($data['userExists'])){
	    	return (bool)$data['userExists'];
	    }

	    return false;
	}

	public static function createUser(array $user)
	{
		$url = "https://www.ftcoop.it/InFederazioneServices/api/v1/Riuniamoci/createUser";
		
		$user['token'] = self::getToken()['access_token'];		
	    $paramsString = json_encode($user);

		$ch = curl_init($url);
	    $options = array(	        
	        CURLOPT_POST => true,
	        CURLOPT_POSTFIELDS => $paramsString,
	        CURLOPT_HTTPHEADER => array(
	            "content-type: application/json",
	            "Authorization: Bearer " . self::getToken()['access_token']
	        ),
	        CURLINFO_HEADER_OUT => false,
	        CURLOPT_HEADER => false,
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_SSL_VERIFYPEER => true
	    );
	    curl_setopt_array($ch, $options);
	    $response = curl_exec($ch);
	    curl_close($ch);		 

	    return json_decode($response, true);	    
	}
}