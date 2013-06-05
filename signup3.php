
<iframe src="https://www.facebook.com/plugins/registration.php?
             client_id=167535613301542&
             redirect_uri=<?=urlencode("http://www.eventgrabber.com/signup4.php")?>&
             fields=[
 {'name':'name'},
 {'name':'first_name'},
 {'name':'last_name'},
 {'name':'email'},
 {'name':'location'},
 {'name':'gender'},
 {'name':'birthday'},
 {'name':'password'},
 {'name':'zip',      'description':'Zip Code',             'type':'text'},
 {'name':'captcha'},
 {'name':'ref_member',       'description':'', 'type':'hidden',  'default':'12'},
 {'name':'usertype',       'description':'Event Host or Promoter', 'type':'checkbox',  'default':'checked'},
 {'name':'agree',       'description':'Agree to Terms of Services and Privacy Policy?', 'type':'checkbox',  'default':'checked'}
]
"
        scrolling="auto"
        frameborder="no"
        style="border:none"
        allowTransparency="true"
        width="504"
        height="800">
</iframe>