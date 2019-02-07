<?php

namespace onservice\consolecore;

class PrintConsole{


	public static function formatWord($array = null){
		$out = '';
	    $html = '';
	    $spanaction = null;
	    $format = null;
	    $color = "white";
	    $bold = false;
	    		
	    $html = isset($array[0])?$array[0]:'';
	    $format = isset($array[1])?"{".$array[1]:null;	    
	    $html = str_replace('<br>', "\n", $html);
	    
	    if($format !== null){	    	
	        $format = json_decode($format,true);
	        $bold = isset($format['bold'])?$format['bold']:$bold;	        
	        $color = isset($format['color'])?$format['color']:$color;	            
	        $width = isset($format['width'])?$format['width']:null;
	    	
	    	if($width!=null)
	    	$html = self::fixedStringSize($html,$width);

	    	
	    	$spaceArray = explode('[:space:]', $html);	    		    	
	    	$outJoin = '';

	    	foreach ($spaceArray as $key => $value) {	    		
	    		$outJoin .= self::write($value,array('bold'=>$bold,'forecolor'=>$color));			
	    		if(count($spaceArray)>1)
	    		$outJoin .= ' ';
	    	}	    
	    	$spaceArray = explode('\s', $outJoin);	    		    	
	    	$outJoin = '';
	    	if(count($spaceArray)>0)
	    	foreach ($spaceArray as $key => $value) {	    		
	    		$outJoin .= self::write($value,array('bold'=>$bold,'forecolor'=>$color));			
	    		if(count($spaceArray)>1)
	    		$outJoin .= ' ';
	    	}
	    		       
	        $out = $outJoin;	        
	    }else{	    	
	    	$out = self::write($html,array('bold'=>$bold,'forecolor'=>$color));				        
	    }

	    return $out;
	}

	public static function formatStrings($string = null){
		$stringArray = explode(' ',$string);
            $html2 = '';
            foreach ($stringArray as $key2 => $value2) {            	
                $par = $value2;          
                $parArray = explode('~{',$par);               	
                $word = self::formatWord($parArray);
                
                $html2 .= ''.$word.' ';
            }

            

            return $html2;
	}

	public static function fixedStringSize($string,$size = 30){
		$countString = strlen($string);		
		$restString = $size-strlen($string);		
		$space = '';
		for($i=0;$i<$restString;$i++) $space .= ' ';
		return $string.$space;
	}

	/**
	 * [output description]
	 * @param  [type] $text    [description]
	 * @param  [type] $options [description]
	 * @return [type]          [description]
	 */
	public static function write($text,$options = null){		

		$colors = self::setColor($options);		
		$foreColor = $colors['forecolor'];
		$backColor = $colors['backcolor'];		
		return $foreColor.$backColor."".$text."\033[0m";
	}




	/**
	 * [setColor description]
	 * @param [type] $options [description]
	 */
	private static function setColor($options = null){

		$fc = isset($options['forecolor'])?$options['forecolor']:'white';
		$bc = isset($options['backcolor'])?$options['backcolor']:null;
		$bold = isset($options['bold'])?$options['bold']:null;

		

		if($bold === true) 
			$bold = '1'; 
		else 
			$bold = '0';

		switch ($fc) {
			case 'red':
				$forecolor =  $bold . ";31";
				break;
			case 'white':
				$forecolor =  $bold.";37";
				break;
			case 'green':
				$forecolor = $bold . ";32";
				break;	
			case 'blue':
				$forecolor = $bold . ";34";
				break;	
			case 'yellow':
				$forecolor = $bold . ";33";
				break;	
			case 'purple':
				$forecolor = $bold . ";35";
				break;	
			case 'cian':
				$forecolor = $bold . ";36";
				break;	
			case 'black':
				$forecolor = $bold . ";30";
				break;	
			
			default:
				$forecolor = '';
				break;
		}

		switch ($bc) {
			case 'black':
				$backcolor = "40";
				break;
			case 'white':
				$backcolor = "47";
				break;
			case 'red':
				$backcolor = "41";
				break;
			case 'green':
				$backcolor = "42";
				break;	
			case 'yellow':
				$backcolor = "43";
				break;	
			case 'blue':
				$backcolor = "44";
				break;	
			case 'magenta':
				$backcolor = "45";
				break;	
			case 'cian':
				$backcolor = "46";
				break;	
			case 'gray':
				$backcolor = "47";
				break;						
			default:
				$backcolor = '';
				break;
		}
	
		$forecolor = "\033[" . $forecolor . "m";
		if($bc != null)$backcolor = "\033[" . $backcolor . "m";
		return array('forecolor'=>$forecolor,'backcolor'=>$backcolor);
	}

}