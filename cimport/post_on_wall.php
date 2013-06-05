<?php

require_once('../admin/database.php'); 
include_once('../admin/api/facebook.php');

//1387282969

$attachment = array('message' => 'EventGrabber is Live now',
                'name' => 'EventGrabber is Live now. Please join. This is wonderful Platform for fun loving people.',
                'caption' => "Join EventGrabber.com",
                'link' => 'http://www.eventgrabber.com/signup.php',
                'description' => 'EventGrabber.com Description',
                'picture' => 'http://www.eventgrabber.com/images/logo.jpg',
                'actions' => array(array('name' => 'EventGrabber.com',
                                  'link' => 'http://www.eventgrabber.com/signup.php'))
                );

$facebook = new Facebook(array(
                    'appId'      => FACEBOOK_APP_ID,
                    'secret'     => FACEBOOK_SECRET,
                    'cookie'     => true
));

$result = $facebook->api('/me/feed/','post',$attachment);

print_r($result);

?>