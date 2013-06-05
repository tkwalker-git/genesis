<?php

define('FACEBOOK_APP_ID','167535613301542');
define('FACEBOOK_SECRET','91350cc09f69e16007825383f54c0209');

header('Location: https://graph.facebook.com/oauth/access_token?' . http_build_query(array(
    'client_id'     => FACEBOOK_APP_ID,
    'type'          => 'client_cred',
    'client_secret' => FACEBOOK_SECRET,
    'code'          => $code)));



?>