<?php

function getInviteEmailContents($name,$id)
	{
		$html = '<table align="center" width="600" style="border:#a6a6a6 1px solid; background-color:#f2f2f2; font-family:Arial, Helvetica, sans-serif;">
				  <tr>
					<td style="padding:15px;padding-left:22px;"><img alt="EventGrabber.com Logo" src="http://www.eventgrabber.com/images/logo_transparent.png"></td>
				  </tr>
					<tr>
					  <td align="center">
						 <table cellpadding="0" cellspacing="0" width="550" align="center" style="border:1px solid #a6a6a6; border-top:5px solid #4f81bd; border-bottom:3px solid #a6a6a6;  background-color:#FFFFFF; margin-bottom:60px;">
						  <tr>
							<td style="border-top:2px solid #a6a6a6;">
							  <p style="margin-left:45px; font-size: 17px;line-height:22px; margin-top:30px; margin-left:45px; ">You have been invited by '. $name .' to sign up on Eventgrabber.<br>
				EventGrabber grabs all events around town and recommends<br>things to do based on your personal preference. 
							  </p>
							
							<p style="margin-left:45px; font-size: 17px;line-height:20px; margin-left:45px; width:500px;">
							 Check out some of the features:
							 <ul style="margin-left:70px; line-height:22px;">
								<li>See your friend\'s Eventwall</li>
								  <li>Find out what Events are buzzing through CityPulse</li>
									<li>Create Hangout Groups and manage your own events</li>
							 </ul>
						  </p>
						   <p style="margin-left:44px;">To learn more, <a href="http://www.eventgrabber.com/demo.php">Watch the demo video</a></p></td>
					 </tr>
				   <tr> 
					  <td align="center" style="padding-bottom:25px;padding-top:20px;">
						<a href="http://www.eventgrabber.com/signup.php?ref='. base64_encode($id) .'" style="border:none; outline:none;"><img alt="Signup and Get Started" src="http://www.eventgrabber.com/images/invite_email_button.png" style="border:none;"></a>
					  </td>
				   </tr>
				 </table>
				</td>
				 </tr>
				</table>';
			return $html;	
	}

?>