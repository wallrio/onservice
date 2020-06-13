<?php 

namespace onservice\services\router\format; 

class Xml{

	static public function transform($arr, $i=1,$flag=false){
		$xml = '';
		if($i===1) $xml = '<?xml version=\'1.0\' encoding=\'utf-8\'?>'."\n";

	    $sp = "";
	    for($j=0;$j<=$i;$j++){
	    	if($i>1)
	        $sp.="";
	     }
	    foreach($arr as $key=>$val){
	        $xml .= "$sp<".$key.">";
	        if(is_array($val)){
	            $xml .= self::transform($val,$i+5);
	            $xml .= "$sp</".$key.">";
	        }else{
	        	if(is_object($val)){
	        		$xml .= self::transform($val,$i+5);
	        		$xml .= "$sp</".$key.">";
	        	}
	        	else{
	            	$xml .= "$val"."</".$key.">";
	        	}
	        }
	    }
	    return $xml;	
	}

}