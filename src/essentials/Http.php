<?php

namespace onservice\essentials;

class Http{
	

	public static function request(array $parameters){
     	
     		$url = isset($parameters['url'])?$parameters['url']:null;
     		$autenticate = isset($parameters['autenticate'])?$parameters['autenticate']:null;

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_ENCODING => "",
              CURLOPT_TIMEOUT => 30,
             
            ));
   	
            if($autenticate !== null)
   			    curl_setopt($curl, CURLOPT_USERPWD, $autenticate);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

        if ($http_status==404)return false;
        
        if($err) return false;
        
        return $response;
   
    }

}