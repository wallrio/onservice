<?php

namespace onservice\essentials;

class Http{
    

    public static function request(array $parameters, &$headers = null,$context = null){

        $url = isset($parameters['url'])?$parameters['url']:null;
        $method = isset($parameters['method'])?$parameters['method']:'get';
        $data = isset($parameters['data'])?$parameters['data']:null;
        $header = isset($parameters['header'])?$parameters['header']:null;
        $autenticate = isset($parameters['autenticate'])?$parameters['autenticate']:null;
        $follow = isset($parameters['follow'])?$parameters['follow']:true;
        $fallback = isset($parameters['fallback'])?$parameters['fallback']:null;
        $timeout = isset($parameters['timeout'])?$parameters['timeout']:7;
        $onlyheader = isset($parameters['onlyheader'])?$parameters['onlyheader']:false;
        $includeheader = isset($parameters['includeheader'])?$parameters['includeheader']:false;



         $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => true,    // don't return headers
            CURLOPT_FOLLOWLOCATION => false,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "spider", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CUSTOMREQUEST => strtoupper($method) 
        );

         if( strtolower($method) === 'get'){
            if(is_array($data) || is_object($data) ){
                $fields_string = http_build_query($data); 

                $urlArray = explode('?', $url);
                $queryAjust = '';
                if(count($urlArray)>1){
                    $url = $urlArray[0];
                    $queryAjust = $urlArray[1].'&';
                }

                $url = $url . '?'.$queryAjust.$fields_string;
            }
         }

         $url = str_replace(' ', '%20', $url);

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );

        if($header !== null){       
            $headerSend = array();
            foreach ($header as $key => $value) {
                $headerSend[] = $key.':'.$value;
            }     
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerSend);
        }

        if( strtolower($method) !== 'get')
        if(is_array($data) || is_object($data) ){
            

            $fields_string = http_build_query($data); 
            
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);                    
        }

        $response = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerAll = curl_getinfo($ch);
        
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        $err = curl_error($ch);        
        curl_close( $ch );


        $pre_http_code = (int) $headerAll['http_code'];        
        $headers = self::headerToArray($headers);
        $http_code =(int) $headers['Request']['code'];
        $headers['Request']['url'] = $url;
        $headers['Request']['method'] = $method;

        $location = isset($headers['Location'])?$headers['Location']:null;
        $code = $headers['Request']['code'];

        if($follow === true)
        if($code === '301' || $code === '302' || $code === '307'){
            $parameters['url'] = $location;
            
            $body = self::request($parameters,$headers_redirect);
            $headers = $headers_redirect;
            return $body;
        }

        if($onlyheader === true)
            return $headers;

        if($includeheader)
            return ['header'=>$headers,'body'=>$body];

        return $body;
    }


    public static function headerToArray($message){
 
        $messageArray = explode("\n", $message);
        $method = $messageArray[0];
        $newArray = array();
        $newArray['Request'] = trim($method);

        

        $RequestArray = explode(' ', $newArray['Request']);

        $newArray['Request'] = array(
          'protocol'=>$RequestArray[0],
          'code'=>isset($RequestArray[1])?$RequestArray[1]:null,
          'message'=>isset($RequestArray[2])?$RequestArray[2]:null,
          
        );
        

        unset($messageArray[0]);
        array_values($messageArray);
     
        foreach ($messageArray as $key => $value) {
            $array = explode(':', $value,2);
            $val = isset($array[1])?$array[1]:'';
            $newArray[$array[0]] = trim($val);
        }

      
        $newArray = array_filter($newArray);
        return $newArray;
       
    }

}