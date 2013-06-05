<?
	if(!function_exists("d")){
		function d($var,$exit=false){
			$debug = debug_backtrace();
			print("<fieldset> Dumps a Variable at <br/>".$debug[0]['file']."<br/> Line No : ".$debug[0]['line']."<br/></fieldset>");
			echo "<pre><fieldset>";
			print_r($var);
			echo "</fieldset></pre>";
			if($exit) exit("<fieldset>Program Exited at<br/>".$debug[0]['file']."<br/> Line No : ".$debug[0]['line']."<br/></fieldset>");
		}
	}
?>