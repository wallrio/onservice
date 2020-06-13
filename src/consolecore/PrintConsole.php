<?php

namespace onservice\consolecore;

class PrintConsole{

	public static function write($text,$options = null){		

		$colors = self::setColor($options);		
		$foreColor = $colors['forecolor'];
		$backColor = $colors['backcolor'];		
		
		$width = isset($options['width'])?$options['width']:null;		

		$laststring = isset($options['laststring'])?$options['laststring']:false;		
		

		if($width!=null)
	    	$text = self::fixedStringSize($text,$width,$laststring);

	    
	    
		return $foreColor.$backColor."".$text."\033[0m";
	}


	public static function fixedStringSize($string,$size = 30,$laststring = false){
		$countString = strlen($string);		
		$restString = $size-strlen($string);		
		$space = '';
		for($i=0;$i<$restString;$i++) $space .= ' ';

		
			if(strlen($string)>$size){
				if($laststring === false){
					$string = substr($string, 0,$size-4).'... ';
				}else{
					$string = '...'.substr($string, strlen($string)-($size-4)).' ';
				}
			}


		return $string.$space;
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

			case 'black':
				$forecolor = $bold . ";30";
				break;	
			case 'red':
				$forecolor =  $bold . ";31";
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
			case 'white':
				$forecolor =  $bold.";37";
				break;
	

			
			
			default:
				$forecolor = '';
				break;
		}

		switch ($bc) {
			case 'black':
				$backcolor = "40";
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
			case 'white':
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


}