<?php
$_GET_lower = array_change_key_case($_GET, CASE_LOWER);

// Read in parameters from URL
 $clinicid = $_GET_lower['clinicid']; 

// now try it
//$ua=getBrowser();
//$yourbrowser= "Your browser: " . $ua['name'] . " " . $ua['version'] . " on " .$ua['platform'] . " reports: <br >" . $ua['userAgent'];
//print_r($yourbrowser);

?>

<html>
	<head>
		<style type="text/css">
		body {
			text-align:center;
			background-color: black;
			overflow:hidden;
		}
		body, object, embed, a {
			padding:0;
			margin:0;
			outline-width:0;
			color: white;
		}
		#missing {
			padding-top: 250px;
			height:400px;
			background-position: center top;
			background-repeat: no-repeat;
			background-image: url(http://dat.marsxplr.com/player/bg.jpg);
		}
		</style>
		<title>Pangea Version 2.0</title>
		<script type="text/javascript" src="https://restorationhealth.yourhealthsupport.com/UnityObject.js"></script>
		<script type="text/javascript">
		<!--
		function GetUnity() {
			if (typeof unityObject != "undefined") {
				return unityObject.getObjectById("unityPlayer");
			}
			return null;
		}
		if (typeof unityObject != "undefined") {
			var params = {
				backgroundcolor: "000000",
				bordercolor: "000000",
				textcolor: "FFFFFF",
				disableContextMenu: true,
				/* logoimage: "https://pangeaifa.com/IFA/Styles/images/Pangea-logo-LR.png", */
				logoimage: "./images/loading.png",
				progressbarimage: "http://dat.marsxplr.com/player/ldp.png",
				progressframeimage: "http://dat.marsxplr.com/player/ldb.png"
			};
			
			<?php
				include 'GetBrowserGamiGen.php';
				
				$ua=getBrowser();
				//print $ua['name'];
				if ($ua['name'] == 'Internet Explorer')
	//					echo 'YEP';
					print 'unityObject.embedUnity("unityPlayer", "WebPlayer.unity3d", "100%", 768, params);';
				else
					print 'unityObject.embedUnity("unityPlayer", "WebPlayer.unity3d", "100%", "100%", params);'; 
			?>
		}
		-->
		</script>
	</head>
	<body>
		<?php
		print("<input type=\"hidden\" name=\"clinicID\" id=\"clinicID\" value=\"" . $clinicid . "\" />");
		?>
		<form id="form1" runat="server">
		<div id="unityPlayer">
			<div class="missing">
				<a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now!">
					<img alt="Unity Web Player. Install now!" src="http://webplayer.unity3d.com/installation/getunity.png" width="193" height="63" />
				</a>
			</div>
		</div>
	</body>
</html>