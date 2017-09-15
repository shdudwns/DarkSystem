<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\utils;

class JWT{

	public static function base64UrlEncode($data){
		return rtrim(str_replace(array('+', '/'), array('-', '_'), base64_encode($data)), '=');
	}

	public static function base64UrlDecode($data){
		return base64_decode(str_replace(array('-', '_'), array('+', '/'), $data), true);
	}

	public static function createJwt($header, $payload, $privateKey){
		$body = self::base64UrlEncode(json_encode($header)) . '.' . self::base64UrlEncode(json_encode($payload));
		$sign = \McpeEncrypter::getES384Signature($body, $privateKey);
		return $body . '.' . self::base64UrlEncode($sign);
	}

	public static function parseJwt($jwt, $needVerify = true){
		$data = explode('.', $jwt);
		if(count($data) == 3){
			$result = [];
			$result['header'] = json_decode(self::base64UrlDecode($data[0]), true);
			$result['payload'] = json_decode(self::base64UrlDecode($data[1]), true);
			if($needVerify){
				$result['isVerified'] = \McpeEncrypter::verifyES384Signature($data[0] . '.' . $data[1], self::base64UrlDecode($data[2]), $result['header']['x5u']);
			}
			
			return $result;
		}
		
		return false;
	}

}
