<?php
	require_once('admin/database.php');
	include_once('site_functions.php');

	if($_SESSION['LOGGEDIN_MEMBER_ID'] > 0 && basename($_SERVER['PHP_SELF']) == 'index.php' &&  basename($_SERVER['PHP_SELF'])!='login.php'){
		if(basename($_SERVER['PHP_SELF'])!='dashboard.php')
			echo "<script>window.location.href='dashboard.php'</script>";
		}
	else{
		if(basename($_SERVER['PHP_SELF'])!='index.php' && basename($_SERVER['PHP_SELF'])!='login.php' && basename($_SERVER['PHP_SELF'])!='user_login.php' && basename($_SERVER['PHP_SELF'])!='signup.php' && basename($_SERVER['PHP_SELF'])!='forgot_password.php' && $_SESSION['LOGGEDIN_MEMBER_ID']=='' &&  basename($_SERVER['PHP_SELF'])!='subscription.php' &&  basename($_SERVER['PHP_SELF'])!='create_patient.php' && basename($_SERVER['PHP_SELF'])!='dr.subscription.php' && basename($_SERVER['PHP_SELF'])!='patient.subscription.php' )
			echo "<script>window.location.href='".ABSOLUTE_PATH."index.php'</script>";
	}

	if(isset($_POST["isSubmitted"])){

		$errors = 0;
		$err_display = 'none';
		$email 		=	DBin($_POST['email']);
		$password 	=	DBin($_POST['password']);
		$login_type	=	DBin($_POST['login_type']);


		$load_err = '<style>';

		if($email == ''){
			$load_err .= ' #auth-popup .auth-form .fields .email span.error-text{display:block;}';
			$errors++;
		}

		if($password == ''){
			$load_err .= ' #auth-popup .auth-form .fields .password span.error-text{display:block;}';
			$errors++;
		}
		$load_err .= '</style>';

		if($errors == 0){

			if($login_type == 'doctor')
				$sql = "SELECT `id` , `first_name` , `last_name` , `email` , `password` FROM `doctors` WHERE `email` = '". $email ."' AND `password` = '". $password ."' ";
			else
				$sql = "SELECT `id` , `firstname` , `lastname` , `email` , `password` FROM `patients` WHERE `email` = '". $email ."' AND `password` = '". $password ."' ";

			$res = mysql_query($sql);

			if($row = mysql_fetch_assoc($res)){

				$_SESSION['logedin'] = '1';
				$_SESSION['LOGGEDIN_MEMBER_ID'] = $row['id'];
				$_SESSION['usertype']= $login_type;

				if ( $_SESSION['REDIRECT_URL'] != '' )
					echo "<script>window.location.href='". urldecode($_SESSION['REDIRECT_URL']) ."';</script>";
				else
					echo "<script>window.location.href='". ABSOLUTE_PATH ."dashboard.php';</script>";

			}else{

				$err_display = 'block';

			}

		}

	}

?>

<!doctype html>
<!--[if !IE]><!--><html lang="en"><!--<![endif]-->
<!--[if gt IE 9]><html lang="en" class="ie gt-ie9"><![endif]-->
<!--[if IE 9]><html lang="en" class="ie lt-ie10 ie9"><![endif]-->
<!--[if lt IE 9]><html lang="en" class="ie lt-ie10 lt-ie9"><![endif]-->
<head>
<script type="text/javascript">var NREUMQ=NREUMQ||[];NREUMQ.push(["mark","firstbyte",new Date().getTime()]);</script>
<link rel="stylesheet" media="all" type="text/css" href="<?php echo ABSOLUTE_PATH; ?>udemy.css"/>
<meta name="apple-itunes-app" content="app-id=562413829">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>Manage, Track and Monitor | PT/INR or Diabetic Results</title>
<meta name="title" content="Manage, Track and Monitor | PT/INR or Diabetic Results"/>
<meta name="keywords" content=""/>
<meta name="description" content=""/>
<link rel="icon" href="" type="<?php echo ABSOLUTE_PATH; ?>image/x-icon"/>
<link rel="image_src" href=<?php echo ABSOLUTE_PATH; ?>images/eg-logo.png/>
<meta name="medium" content="mult"/>
<meta property="og:type" content="video_lecture"/>
<meta property="og:url" content="https://genesis.yourhealthsupport.com"/>
<meta property="og:title" content="Total Patient Management"/>
<meta property="og:description" content="."/>
<meta property="og:image" content=""/>
<meta property="og:site_name" content="Genesis Total Patient Management"/>
<meta property="og:locale" content="en_US"/>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="canonical" href="https://www.eventgrabber.com/"/>
<link href='https://fonts.googleapis.com/css?family=Short+Stack|Arvo:400,700,400italic,700italic|Montserrat:400,700' rel='stylesheet' type='text/css'>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body id="udemy" class="udemy controller-home action-index view-index home">
<?php echo $load_err; ?>
<div id="old-browser" style="display: none">
    <div class="copy">
        <img src="<?php echo ABSOLUTE_PATH; ?>images/chrome-logo.png">
        We highly recommend using the free
        <a class="download" href="https://www.google.com/chrome">Google Chrome Browser</a>
        on our site!
        <a id="hide-browser-message" href="#">Hide</a>
    </div>
    <div class="overlay"></div>
</div>
<header class="udemy-header">
    <div class="container">
        <a id="logo" href="https://www.eventgrabber.com/">
            <img src=<?php echo ABSOLUTE_PATH; ?>images/eg-logo.png/>
        </a>
    </div>

</header>

<div id="top-section" class="v3-home-v2 ud-page" data-page-name="redirect-ipad-to-app">
    <div align="left"><img src=<?php echo ABSOLUTE_PATH; ?>images/eg-logo.png/></div>
	<hgroup id="headings">
		<h2>Manage, Track and Monitor Your</h2>
        <h1>PT/INR or Diabetic Results</h1>	</hgroup>

	<div id="auth-popup" class="signup-login-panel on">
		<div id="login">
			<form id="login-form" name="login-form" action="" method="post" class="ud-formajaxify">
            <input type="hidden" name="isSubmitted" value="1"/>
			<div class="auth-form">
				<div class="or">
					MEMBER LOGIN				</div>
				<h2 class="side-lined">
					<span>Login with your email :</span>
				</h2>
				<div class="fields">
					<div class="form-item email">
						<input id="email" name="email" type="text" class="text-input  " placeholder="E-mail"/><span class="error-text display">Please enter email.</span>
					</div>
					<div class="form-item password">
						<input id="password" name="password" type="password" class="text-input  " placeholder="Password"/><span class="error-text">Please enter password.</span>
					</div>

                    <div class="form-item">
						<input id="login_type" checked name="login_type" type="radio" class="text-input  " style="width:10px; margin:0;" value="doctor" /> Login as Doctor&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<input id="login_type" name="login_type" type="radio" class="text-input  " style="width:10px; margin:0;"  value="patient" /> Login as Patient
					</div>



					<div class="form-errors" style="display:<?php echo $err_display; ?>">Your given information is not matched!</div>
				</div>
			</div>
			<div class="form-bottom">
				<a class="login-btn btn btn-success btn-small" href="javascript:void(0);" onClick="document.forms['login-form'].submit(); return false;">
					login				</a>
				<a href="https://www.eventgrabber.com/user/forgot-password" class="forgot">
					Forgot Password?				</a>
			</div>
			<div class="form-bottom">
				<span>
					New Patient?&nbsp;
					<a href="<?php echo ABSOLUTE_PATH . 'patient.subscription.php'; ?>" class="goto-signup-btn">Click here for Patient Subscription</a>
				</span>
				<span>
					New Doctor?&nbsp;
                    <a href="<?php echo ABSOLUTE_PATH . 'dr.subscription.php'; ?>" class="goto-signup-btn">Click here for Doctor Subscription</a>
				</span>
			</div>
			</form>
			</div>
	</div>


</div>
<div id="discover-section">
	<div id="discover-top">
		<div class="container">

<!--
<form id="search" action="https://www.eventgrabber.com/courses/search">
				<div id="search-input-wrapper">
					<input type="text" placeholder="What type of event are you in the mood for?" name="q">
				</div>
				<input type="submit" class="btn btn-success btn-large" id="search-btn" value="SEARCH">
				</a>
			</form>
-->

		</div>
	</div>
	<div id="discover-main">
		<div class="container">
			<h1 class="side-lined thin">
				Select Plan			</h1>

            <ul id="courses" class="discover-courses-list">

          <li data-courseid="9287" class="course-item-grid">
    <a href="<?php echo ABSOLUTE_PATH; ?>patient.subscription.php?subscription_type=1" class="mask">
            <span class="course-thumb">
				<img src="<?php echo ABSOLUTE_PATH; ?>images/blood-glucose-304x171.png">
            </span>
            <span class="main-info">
                <h3><span class="cell m">Blood Glucose Tracking</span></h3>
                <h4>
                    <span class="thumb" style="background-image: url(https://udemy-images.s3.amazonaws.com/user/50x50/39032_bdc3_2.jpg);"></span>
                    <span class="title ellipsis">Visual Graphs</span>
                    <span class="job-title ellipsis">Historical Logs</span>
                </h4>
            </span>
            <span class="course-info ellipsis">
                Keep diabetes under control by monitoring your levels today!
            </span>
            <span class="bottom">
                <span class="rating">
                    <span class="review-count">Monthly Subscription</span>
                </span>
                <span class="price"><span>$9.95</span></span>
            </span>
    </a>
</li>
<li data-courseid="9385" class="course-item-grid">

    <a href="<?php echo ABSOLUTE_PATH; ?>patient.subscription.php?subscription_type=2" class="mask">
            <span class="course-thumb">
				<img src="<?php echo ABSOLUTE_PATH; ?>images/blood-glucose-304x171.png">
            </span>
            <span class="main-info">
                <h3><span class="cell m">PT/INR Tracking</span></h3>
                <h4>
                    <span class="thumb" style="background-image: url(https://udemy-images.s3.amazonaws.com/user/50x50/39032_bdc3_2.jpg);"></span>
                    <span class="title ellipsis">Visual Graphs</span>
                    <span class="job-title ellipsis">Historical Logs</span>
                </h4>
            </span>
            <span class="course-info ellipsis">
                Keep diabetes under control by monitoring your levels today!
            </span>
            <span class="bottom">
                <span class="rating">
                    <span class="review-count">Monthly Subscription</span>
                </span>
                <span class="price"><span>$9.95</span></span>
            </span>
    </a>
</li>
<li data-courseid="11174" class="course-item-grid">

    <a href="<?php echo ABSOLUTE_PATH; ?>patient.subscription.php?subscription_type=3" class="mask">
            <span class="course-thumb">
				<img src="<?php echo ABSOLUTE_PATH; ?>images/blood-glucose-304x171.png">
            </span>
            <span class="main-info">
                <h3><span class="cell m">Blood Glucose + PT/INR Tracking</span></h3>
                <h4>
                    <span class="thumb" style="background-image: url(https://udemy-images.s3.amazonaws.com/user/50x50/39032_bdc3_2.jpg);"></span>
                    <span class="title ellipsis">Visual Graphs</span>
                    <span class="job-title ellipsis">Historical Logs</span>
                </h4>
            </span>
            <span class="course-info ellipsis">
                Keep diabetes under control by monitoring your levels today!
            </span>
            <span class="bottom">
                <span class="rating">
                    <span class="review-count">Monthly Subscription</span>
                </span>
                <span class="price"><span>$14.95</span></span>
            </span>
    </a>
</li>
<li data-courseid="6706" class="course-item-grid">

            <div class="add-to-wishlist btn btn-small ud-wishlist ud-popup"
             data-requireLogin="true"
             href="/wishlist/add?courseId=6706"
             data-courseid="6706">
            <span class="ajax-loader-stick wishlist-loader none"></span>
            <i class="icon-plus"></i>
            <span class="in-wishlist none">Wishlisted</span>
            <span class="not-in-wishlist">Wishlist</span>
        </div>
</li>

            </ul>

			<div id="discover-btn-row1">
                <a href="<?php echo ABSOLUTE_PATH; ?>patient.subscription.php?subscription_type=1" class="btn btn-success">
					Enroll Now | $9.95/mo
				</a>

                <a href="<?php echo ABSOLUTE_PATH; ?>patient.subscription.php?subscription_type=2" class="btn btn-success">
					Enroll Now | $9.95/mo
				</a>

                <a href="<?php echo ABSOLUTE_PATH; ?>patient.subscription.php?subscription_type=3" class="btn btn-success">
					Enroll Now | $14.95/mo
				</a>

			</div>

		</div>
	</div>
</div>

<div id="v3-footer">
    <div class="top" style="display:none;">
        <div class=container>
            <div class="row">
                <div class="span4">
                        <h3>As Seen On</h3>
                    <img src="<?php echo ABSOLUTE_PATH; ?>images/footer-logos.png"/>
                </div>
                <div class="span4">
                    <h3>Advertising Programs</h3>
                    <img src="<?php echo ABSOLUTE_PATH; ?>images/tree.png" class="span2 pull-left" style="margin-left: 0;margin-right: 10px;"/>
                    <p>
                        Join thousands of passionate advertisers who are building their brand, and making money on Eventgrabber.                        <br/>
                        <a href="https://www.eventgrabber.com/teach" class="btn btn-small clear " >Learn More</a>
                    </p>
                </div>
                <div class="span4">
                    <h3>Eventgrabber for Advocates</h3>
                    <img src="<?php echo ABSOLUTE_PATH; ?>images/footer-instructor.jpg" class="img-polaroid span2 pull-left" style="margin:0 10px 20px 0;"/>
                    <p >
                        Make money simply by referring cool events to your friends and family!                        <br/>
                        <a href="" class="btn btn-small">Learn More</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom">
        <div class="container" style="height:45px;">
            <ul class="clearfix" " rel="nofollow">Terms of Use</a></li>
	                <li><a href="" rel="nofollow">Privacy Policy</a></li>
            </ul>
            <div id="copyright" style="display:none;">&copy; 2013 Genesis</div>
        </div>
    </div>
</div>

</body>
</html>
