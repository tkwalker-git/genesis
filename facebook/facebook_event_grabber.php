<?php
//  includes

include('facebook.php');

// end includes

//  defines

define('FACEBOOK_APP_ID','129834663751946');
define('FACEBOOK_SECRET','58e36a5c672c0aa978ad8cf1786b89e9');

//  initializing
$facebook = new Facebook(array(
                    'appId'      => FACEBOOK_APP_ID,
                    'secret'     => FACEBOOK_SECRET,
                    'cookie'     => true
));

try
{
    // facebook search
    $res = $facebook->api(
            'search',
            'get',
            array(
                'q'=>'Orlando, FL',
                'type'=>'event',
                'limit'=>'5000',
                'fields'=>'id,owner,name,description,start_time,end_time,location,venue,privacy',
                'locale'    => 'en_US'
            )
    );
    //  end facebook search
	
	print_r($res);
	 
}
catch(FacebookApiException $e)
{
    echo 'FacebookApiException<pre>';
    var_dump($e);
    echo '</pre>';
}
die('die');

?>