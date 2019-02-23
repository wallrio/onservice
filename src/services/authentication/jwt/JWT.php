<?php

namespace onservice\services\authentication\jwt;

class JWT
{
    
    public static function decode($token, $key = null, $check = true){
        $tks = explode('.', $token);
        if (count($tks) != 3) {
            throw new \UnexpectedValueException('Wrong number of segments');
        }
        list($headb64, $payloadb64, $cryptob64) = $tks;
        if (null === ($header = json_decode(JWT::urlBase64Decode($headb64)))
        ) {
            throw new \UnexpectedValueException('Invalid segment encoding');
        }
        if (null === $payload = json_decode(JWT::urlBase64Decode($payloadb64))
        ) {
            throw new \UnexpectedValueException('Invalid segment encoding');
        }
        $sig = JWT::urlBase64Decode($cryptob64);
        if ($check) {
            if (empty($header->alg)) {
                throw new DomainException('Empty algorithm');
            }
            if ($sig != JWT::sign("$headb64.$payloadb64", $key, $header->alg)) {
                throw new \UnexpectedValueException('Signature verification failed');
            }
        }
        return $payload;
    }

    
    public static function encode($payload, $key, $algo = 'HS256'){
        $header = array('typ' => 'JWT', 'alg' => $algo);

        $segments = array();
        $segments[] = JWT::urlBase64Encode(json_encode($header));
        $segments[] = JWT::urlBase64Encode(json_encode($payload));
        $signing_input = implode('.', $segments);

        $signature = JWT::sign($signing_input, $key, $algo);
        $segments[] = JWT::urlBase64Encode($signature);

        return implode('.', $segments);
    }

   
    public static function sign($msg, $key, $method = 'HS256'){
        $methods = array(
            'HS256' => 'sha256',
            'HS384' => 'sha384',
            'HS512' => 'sha512',
        );
        if (empty($methods[$method])) {
            throw new DomainException('Algorithm not supported');
        }
        return hash_hmac($methods[$method], $msg, $key, true);
    }

    public static function urlBase64Decode($input){
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function urlBase64Encode($input){
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    private static function handleJsonError($errno){
        $messages = array(
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
        );
        throw new DomainException(isset($messages[$errno])
            ? $messages[$errno]
            : 'Unknown JSON error: ' . $errno
        );
    }

}
