<style>
strong
{
	text-decoration:underline;
	font-size:14px;
}
</style>
<?php
//  includes

include('facebook.php');

// end includes

//  defines
//define('FACEBOOK_APP_ID','129834663751946');
//define('FACEBOOK_SECRET','58e36a5c672c0aa978ad8cf1786b89e9');

define('FACEBOOK_APP_ID','175448372466498');
define('FACEBOOK_SECRET','2796fe4808043bb6c401ffc236bc1b2c');


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
                'q'=>'uid=1183299645',
                'type'=>'event',
                'limit'=>'50',
                'fields'=>'id,owner,name,description,start_time,end_time,location,venue,privacy',
                'locale'    => 'en_US'
            )
    );
    //  end facebook search
	
	$events = $res['data'];
	
	echo '<h1>Total Events: ' . count($events) . '</h1>';
	
	foreach ($events as $key => $value ) {
		
		echo '<strong>Owner Name: </strong>' . $value['owner']['name'] . '<br>' ;
		echo '<strong>Event Name: </strong>' . $value['name'] . '<br>' ;
		echo '<strong>Event description: </strong>' . $value['description'] . '<br>' ;
		echo '<strong>Event Start Time: </strong>' . $value['start_time'] . '<br>' ;
		echo '<strong>Event End Time: </strong>' . $value['end_time'] . '<br>' ;
		echo '<strong>Event Location: </strong>' . $value['location'] . '<br>' ;
		echo '<strong>Venu: </strong>'. $value['venue']['street'] . ' ' . $value['venue']['city'] . ' ' . $value['venue']['state']. ' ' . $value['venue']['country'] .' <br>' ;

		echo '<br><br><hr>' ;
		
	}
	 
}
catch(FacebookApiException $e)
{
    echo 'FacebookApiException<pre>';
    var_dump($e);
    echo '</pre>';
}
die('die');

?>