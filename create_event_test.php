<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>
<style>
body{
	margin:0;
	padding:0;
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	}

.ev_new_box{
	width:978px;
	margin:auto;
	}
	
.ev_new_box_top{
	background:url(images/create_event_box_top.png) no-repeat;
	width:978px;
	height:17px;
	}
	
.ev_new_box_left{
	background:url(images/create_event_box_left.png) no-repeat;
	width:21px;
	float:left;
	height:420px;
	}
	
.ev_new_box_center{
	width:936px;
	float:left;
	}
	
.ev_new_box_right{
	background:url(images/create_event_box_right.png) no-repeat;
	width:21px;
	float:right;
	height:420px;
	}
	
.ev_new_box_center .basic_box, .ev_new_box_center .featured_box, .ev_new_box_center .premium_box, .ev_new_box_center .custom_box{
	width:234px;
	height:468px;
	float:left;
	position:absolute;
	overflow:hidden
	}

.ev_new_box_center .basic_box{
	background:url(images/basic_box.gif) no-repeat;
	}

.ev_new_box_center .featured_box{
	background:url(images/featured_box.gif) no-repeat;
	left:234px;
	}
	
.ev_new_box_center .premium_box{
	background:url(images/premium_box.gif) no-repeat;
	left:468px;
	}
	
.ev_new_box_center .custom_box{
	background:url(images/custom_box.gif) no-repeat;
	left:702px;
	}
	
	
.ev_new_box_center .basic_box .black, .ev_new_box_center .featured_box .black, .ev_new_box_center .premium_box .black, .ev_new_box_center .custom_box .black{	
	filter:alpha(opacity=50);
	-ms-filter:alpha(opacity=50);
	-moz-opacity:0.5;
	opacity:0.5;
	background:#333333;
	width:234px;
	height:468px;
	position:absolute;
	}
	
	
.ev_new_box_center .black:hover{
	display:none;
	}
	
.ev_new_box_center .basic_box:hover > .black, .ev_new_box_center .featured_box:hover > .black, .ev_new_box_center .premium_box:hover > .black, .ev_new_box_center .custom_box:hover > .black{
	display:none;
	}
	
	
.ev_new_box_center .basic_box:hover, .ev_new_box_center .featured_box:hover, .ev_new_box_center .premium_box:hover, .ev_new_box_center .custom_box:hover{
	z-index:9999;
	-moz-box-shadow:0px 0px 7px 2px #464646;
	-webkit-box-shadow:0px 0px 7px 2px #464646;
	-khtml-box-shadow:0px 0px 7px 2px #464646;
	box-shadow:0px 0px 7px 2px #464646;
	filter: progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=0),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=90),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=180),
           progid:DXImageTransform.Microsoft.Shadow(Color=#464646, Strength=5, Direction=270);
	}
	


.ssblack{
	filter:alpha(opacity=70);
	-ms-filter:alpha(opacity=70);
	-moz-opacity:0.7;
	opacity:0.7;
	background: #000;
	width: 100%;
	height: 100%;
	position: fixed;
	left: 0px;
	top: 0px;
	z-index: 5000;
/*	display: none; */
	}

.ev_new_box_center .detail{
	padding:146px 10px 0;
	height:245px
	}
</style>
<body>
<div class="ev_new_box">
  <div class="ev_new_box_top">&nbsp;</div>
  <div class="ev_new_box_left">&nbsp;</div>
  <div class="ev_new_box_center">
    <div style="position:relative">
      <div class="basic_box">
        <div class="black">&nbsp;</div>
        <div class="detail">Add a basic listing of your event to our database for free.</div>
        <div align="center"><a  href="javascript:voild(0)" onclick="window.location.href='create_event.php?type=simple';"><img src="images/ev_new_create_event.png" /></a></div>
         </div>
      <!-- end basic_box -->
      <div class="featured_box">
        <div class="black">&nbsp;</div>
        <div class="detail">Utilize our proprietary Showcase Creator<strong>&reg;</strong> to create an embedded event flyer that can be  integrated into your Facebook Page, Twitter Page, and Featured on  Eventgrabber.&nbsp; Featured Campaigns  includes the ability to sell and manage tickets and special offers to your  target audience.</div>
        <div align="center"><a  href="javascript:voild(0)" onclick="window.location.href='create_event.php?type=flyer';"><img src="images/ev_new_create_event.png" /></a></div>
         </div>
      <!-- end featured_box -->
      <div class="premium_box">
        <div class="black">&nbsp;</div>
        <div class="detail">Utilize our proprietary Showcase Creator<strong>&reg;</strong> to create an embedded event flyer that can be  integrated into your Website, Facebook Page, Twitter Page, and listed as a  Premium event on Eventgrabber.&nbsp; Premium  Showcases includes the ability to sell and manage tickets and special offers to  your target audience.</div>
        <div align="center"><a href="javascript:voild(0)" onclick="alert('This feature is coming soon');"><img src="images/ev_new_create_event.png" /></a></div>
         </div>
      <!-- end premium_box -->
      <div class="custom_box">
        <div class="black">&nbsp;</div>
        <div class="detail">Custom Campaigns are tailored made to fit your  specific need and includes White Label.&nbsp;  Contact us for more details on this product.</div>
        <div align="center"><a  href="javascript:voild(0)" onclick="alert('This feature is coming soon');"><img src="images/ev_new_create_event.png" /></a></div>
         </div>
      <!-- end custom_box -->
    </div>
    <!-- end position:relative -->
  </div>
  <div class="ev_new_box_right">&nbsp;</div>
</div>
</body>
</html>
