<?php 
require_once('../admin/database.php');

if(isset($_POST["action"]))
	$action = $_POST["action"];
else
	$action = $_GET["action"];
	
switch($action){

	case 'send_invitation_to_doctor' :
	
		$sql = "INSERT INTO `invitations` (`patient_id` , `doctor_id` , `status`) VALUES ('". $_SESSION['LOGGEDIN_MEMBER_ID'] ."' , '". $_POST["doctor_id"] ."' , 0)";
		$res = mysql_query($sql);
		
		if($res){
			
			/*$dr_email = attribValue("doctors" , "email" , "where id = '". $_POST["doctor_id"] ."'");
			$dr_name = attribValue("doctors" , "CONCAT(`first_name` , ' ' , `last_name`)" , "WHERE `id` = '". $_POST["doctor_id"] ."'");
			$pat_name = attribValue("patients" , "CONCAT(`firstname` , ' ' , `lastname`)" , "WHERE `id` = '". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
			$pat_name = attribValue("patients" , "CONCAT(`firstname` , ' ' , `lastname`)" , "WHERE `id` = '". $_SESSION['LOGGEDIN_MEMBER_ID'] ."'");
			$subject = "Invitation from a patient";
			$message = 'Dear '. $dr_name .' ,<br><br>';
			$message .= 'You have recieved a invitation as a caretaker from '. $pat_name .'<br>';
			$message .= 'Please login to your account by click the url below. <br><br> <a href="'. ABSOLUTE_PATH  .'">'. ABSOLUTE_PATH  .'</a><br><br>';
			$message .= 'Message footer';
			
			
			$semi_rand = md5(time()); 
    		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
			 
			$headers   = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/html; charset=iso-8859-1";
			$headers[] = "From: ". $pat_name ." <". $pat_name .">";
			$headers[] = "Reply-To: Recipient Name <no-reply@example.com>";
			$headers[] = "Subject: {$subject}";
			$headers[] = "X-Mailer: PHP/".phpversion();
			
			
			$ok = @mail($dr_email, $subject, $message, implode("\r\n", $headers));
			
			if($ok)*/
				echo 1;
		}else
			echo 0;
		
	break; //	end case send_invitation_to_doctor
	
	case 'unset_confirmation':
	
		unset($_SESSION["CONFIRMATION_MESSAGE"]);
		echo 1;
	
	break; // end case unset_confirmation
	
} //end switch

?>