<?php

namespace onservice\essentials;

class Http{
    

    public static function request(array $parameters, &$header = null,$context = null){
        
        $url = isset($parameters['url'])?$parameters['url']:null;
        $method = isset($parameters['method'])?$parameters['method']:'get';
        $data = isset($parameters['data'])?$parameters['data']:null;
        $autenticate = isset($parameters['autenticate'])?$parameters['autenticate']:null;
        $follow = isset($parameters['follow'])?$parameters['follow']:false;
        $fallback = isset($parameters['fallback'])?$parameters['fallback']:null;
        $timeout = isset($parameters['timeout'])?$parameters['timeout']:7;
        $onlyheader = isset($parameters['onlyheader'])?$parameters['onlyheader']:false;

    
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method) );
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $follow);        

        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3); 
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); 


        

        if($data !== null){
            if( gettype($data) !== 'array' ){
                $data = array($method=>$data);
            }
        }


        if(is_array($data) && count($data)>0){
            $fields_string = http_build_query($data);            
            curl_setopt($curl,CURLOPT_POST, 1);
            curl_setopt($curl,CURLOPT_POSTFIELDS, $fields_string);                    
        }
        

        if($autenticate !== null)
        curl_setopt($curl, CURLOPT_USERPWD, $autenticate);

        $response = curl_exec($curl);
        
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        $err = curl_error($curl);
        
        $headerAll = curl_getinfo($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $pre_http_code =(int) $headerAll['http_code'];
        

        $headers = self::headerToArray($headers);

        $http_code =(int) $headers['Request']['code'];



        if($http_code === 300 || $http_code === 301 || $http_code === 302){            
            


            if($follow === true){
                $redirect_url = $headers['Location'];
                $parameters['url'] = $redirect_url;                        
                $return = self::request($parameters,$header,$context);            
                return $return;
            }
        }
        

        if($header_size === 0){            
            if($fallback !== null){
                
        // print_r($parameters);
                sleep(1);
                return $fallback($parameters,$header,$context);
            }
            return false;
        }

        
       
        
        $headers['Request']['url'] = $url;

        $header = array(
          'code' => $headers['Request']['code'],
          'message' => $headers['Request']['message'],
          'type' => isset($headers['Content-Type'])?$headers['Content-Type']:null
        );

        $headersAfter = $headers;

        unset($headersAfter['Request']);
        unset($headersAfter['Content-Type']);

        $header = array_merge($header,$headersAfter);

        $header = $headers;




        if ($http_status==404)return false;
        
        if($err) return false;
        
        if($onlyheader === true)
            return $header;

        

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