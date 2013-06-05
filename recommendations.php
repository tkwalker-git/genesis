<?php 
require_once('admin/database.php');
require_once('site_functions.php');

if($_SESSION['userZip'])
	$userZip =	"and zipcode in (".$_SESSION['userZip'].")";
	
if (!$_SESSION['LOGGEDIN_MEMBER_ID']>0)
		echo "<script>window.location.href='login.php';</script>";

$member_full_name = attribValue('users', 'concat(firstname," ",lastname)', "where id='". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
$meta_title	= 'Recommendations';
$member_id = $_SESSION['LOGGEDIN_MEMBER_ID'];
include_once('includes/header.php');
?>
<link rel="stylesheet" href="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH;?>fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("a#profileImage").fancybox({
			'titleShow'		: false,
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic'
		});
	});
</script>
<style>
.dash_menu{
	position:relative;
	height:67px;
	}
	
.dash_menu table{
	}
	
.dash_menu td{
	padding:0 08px;
	height:64px;
	text-align:center;
	vertical-align:text-bottom;
	}
	
.dash_menu .bordr{
	border-right:#c1c1c1 solid 1px;
	}
	
.dash_menu td a{
	float:left;
	font-size:11px;
	font-weight:bold
	}
	
.dash_menu td a:hover{
	text-decoration:none;
	color:#0598fa
	}
	
.dash_menu td a img{
	margin-bottom:3px;
	}
	
.head_new{
	font-size:18px;
	background:url(images/dashB_bar.gif) repeat-x;
	padding:8px 12px;
	color:#ffffff;
	border-left:solid 1px #cbcbcb;
	border-right:solid 1px #cbcbcb;
	}
	
.recBox{
	border:#cecece solid 1px;
	border-top:none;
	background:#f6f6f6;
	}
	
.recBox .rBox{
	padding:17px 10px 10px 26px
	}

ul.recommend_ul li {
    float: left;
    list-style: none outside none;
    margin: 0 17px;
    width: 180px;
	}

.prev_btn {
    float: left;
    margin-right: 10px;
	}

.recBox .yellow_bar{
	background: url("images/yellow_bar.gif") repeat-x scroll 0 0 transparent;
    border-bottom: 1px solid #CBCBCB;
    color: #231F20;
    font-size: 16px;
    font-weight: bold;
    height: 28px;
    padding: 9px 12px 0;
	}
	
.yellow_bar a{
	font-size:12px;
	color:#006695;
	}
	
.categoryEventBlock_left{
	float: left;
	width:190px;
	border:0;
	padding:0;
	}
	
ul.category_ul li {
	margin:0 16px;
	}

ul.recommend_ul .heading_dark_14 {
	font-size: 13px;
    font-weight: bold;
	}

</style>
<div class="topContainer">
	<div class="welcomeBox"></div>
	<div id="middleContainer">
		<div class="clr"></div>
		<div class="gredBox">
			<?php include('dashboard_menu.php'); ?>
			<div class="whiteTop">
				<div class="whiteBottom">
					<div class="whiteMiddle" style="padding-top:7px;">
						<?php include_once("widget_recomemded2.php"); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include_once('includes/footer.php'); ?>