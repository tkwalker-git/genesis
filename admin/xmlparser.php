<?php

class xmlparser
{

    function GetChildren($vals, &$i) 
    {
        $children = array(); 
    
    
        if (isset($vals[$i]['value'])) 
            $children['VALUE'] = $vals[$i]['value']; 
    
    
        while (++$i < count($vals))
        { 
            switch ($vals[$i]['type']) 
            { 
                case 'cdata': 
                    if (isset($children['VALUE']))
                        $children['VALUE'] .= $vals[$i]['value']; 
                    else
                        $children['VALUE'] = $vals[$i]['value']; 
                    break;
    
                case 'complete': 
                    if (isset($vals[$i]['attributes'])) {
                        $children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
                        $index = count($children[$vals[$i]['tag']])-1;
    
                        if (isset($vals[$i]['value'])) 
                            $children[$vals[$i]['tag']][$index]['VALUE'] = $vals[$i]['value']; 
                        else
                            $children[$vals[$i]['tag']][$index]['VALUE'] = ''; 
                    } else {
                        if (isset($vals[$i]['value'])) 
                            $children[$vals[$i]['tag']][]['VALUE'] = $vals[$i]['value']; 
                        else
                            $children[$vals[$i]['tag']][]['VALUE'] = ''; 
    		}
                    break; 
    
                case 'open': 
                    if (isset($vals[$i]['attributes'])) {
                        $children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
                        $index = count($children[$vals[$i]['tag']])-1;
                        $children[$vals[$i]['tag']][$index] = array_merge($children[$vals[$i]['tag']][$index],$this->GetChildren($vals, $i));
                    } else {
                        $children[$vals[$i]['tag']][] = $this->GetChildren($vals, $i);
                    }
                    break; 
    
                case 'close': 
                    return $children; 
            } 
        } 
    } 




    function GetXMLTree($xml) 
    { 
        $data = $xml;
       
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
        xml_parse_into_struct($parser, $data, $vals, $index); 
        xml_parser_free($parser);
        
        //print_r($index);
    
        $data = array(); 
        $i = 0; 
        
        if (isset($vals[$i]['attributes'])) {
    	$data[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes']; 
    	$index = count($data[$vals[$i]['tag']])-1;
    	$data[$vals[$i]['tag']][$index] =    array_merge($data[$vals[$i]['tag']][$index], $this->GetChildren($vals, $i));
        }
        else
            $data[$vals[$i]['tag']][] = $this->GetChildren($vals, $i); 
        
        return $data; 
    } 
    
	function printa($obj) {
		global $__level_deep;
		if (!isset($__level_deep)) $__level_deep = array();
	
		if (is_object($obj))
			print '[obj]';
		elseif (is_array($obj)) {
			foreach(array_keys($obj) as $keys) {
				array_push($__level_deep, "[".$keys."]");
				$this->printa($obj[$keys]);
				array_pop($__level_deep);
			}
		}
		else print implode(" ",$__level_deep)." = $obj\n";
    }
} // end class


function get_url_contents($url){
	$crl = curl_init();
	$timeout = 5;
	curl_setopt ($crl, CURLOPT_URL,$url);
	curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
	$ret = curl_exec($crl);
	curl_close($crl);
	return $ret;
}

?>