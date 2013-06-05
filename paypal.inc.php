<?php
/***
* Paypal Payment Gateway.
*Author: Asmat ullah verak
*
*
*
*
***/
 
class paypal
{
	var $logfile='ipnlog.txt';
	var $form=array();
	var $form_action='https://www.sandbox.paypal.com/webscr';
	var $paypalurl='https://www.paypal.com';
	var $type='payment';
	var $posted_data=array();
	var $action='';
	var $error='';
	var $testmode;
	var $ipn='';
	var $price=0;
	var $payment_success=0;
	var $ignore_type=array();
	
	public function __construct() { 

		
		$this->liveurl 		= 'https://www.paypal.com/webscr';

		$this->testurl 		= 'https://www.sandbox.paypal.com/webscr';

		$this->testmode		= 'Test';		


    } 



	function validate_ipn(){
	
		if(!empty($_POST))
			{
			
			$postvars='';
			$this->price=0;

			foreach($_POST as $key=>$value):
				$postvars.=$key. '=' . urlencode($value) . '&';
				$this->posted_data[$key]=$value;
			endforeach;

			$postvars.="cmd=_notify-validate";
			
			$errstr=$errno='';
			
			$fp = @ fsockopen('ssl://www.sandbox.paypal.com',443,$errno,$errstr,30);
			
			if(!$fp):
				$this->error="fsockopen error no. $errno: $errstr";
				return $errstr;
			endif; 
			
			if ( $this->testmode == 'Test' ):

				$form_action = $this->testurl;		

			else :

				$form_action = $this->liveurl;		

			endif;
			
			$url_parsed=parse_url($form_action);
			
			fputs($fp, "POST ". $url_parsed[path] ." /cgi-bin/webscr HTTP/1.1\r\n");
			fputs($fp, "Host: ".$url_parsed[host]."\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ".strlen($postvars)."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $postvars . "\r\n\r\n");
			
			
			
			$str='';

			while(!feof($fp)):
				$str.=fgets($fp,1024);
			endwhile;
			
			fclose($fp);
			
			
			
			
			if(preg_match('/VERIFIED/i',$str))
			{
				if($this->log)
				$this->log_results(1);
					
				if(preg_match('/subscr/',$this->posted_data['txn_type']))
				{
					$this->type='subscription';

					if(in_array($this->posted_data['txn_type'],$this->ignore_type))
					return 0;
					
					if($this->posted_data['txn_type']=='subscr_payment')
					{
						if($this->posted_data['payment_status']=='Completed')
						{
							$this->price=$this->posted_data['mc_amount3'];
							$this->payment_success=1;
						}
					}

				}
				else
				{
					if($this->posted_data['payment_status']=='Completed')
					{
						$this->type='payment';
						$this->price=$this->posted_data['mc_gross'];
						$this->payment_success=1;
					}
				}
				$this->payment_success=1;
				return 1;
			}
			else
			{
				if($this->log)
				$this->log_results(0);

				$this->error='IPN verification failed.';
				return 0;
			}
		}
		else
		return 0;
		
	}

	function add($name,$value)
	{
		$this->form[$name]=$value;
	}

	function remove($name)
	{
		unset($this->form[$name]);
	}


	function enable_payment()
	{
		$this->type='payment';
		$this->remove('t3');
		$this->remove('p3');
		$this->remove('a3');
		$this->remove('src');
		$this->remove('sra');
		$this->add('amount',$this->price);
		$this->add('cmd','_xclick');
		$this->add('no_note',1);
		$this->add('no_shipping',1);
		$this->add('notify_url',$this->ipn);
	}
	function output_form()
	{
		if ( $this->testmode == 'Test' ):

			$form_action = $this->testurl;		

		else :

			$form_action = $this->liveurl;		

		endif;

		echo '<h3>Redirecting to PayPal...</h3>'
		. '<form name="f" action="'.$this->form_action.'" method="post">';

		foreach($this->form as $k=>$v)
		{
			echo '<input type="hidden" name="' . $k . '" value="' . $v . '" />';
		}

		echo '<input  type="submit" value="Click here if you are not redirected within 5 seconds" /></form>';
		
		echo '<script language="javascript">
				setTimeout("SubForm()", 0); 

				function SubForm() {
					document.f.submit();
				}
			</script>';
		
	}
	
/*	function log_results($var){
		$fp=@ fopen($this->logfile,'a');
		$data=date('m/d/Y g:i A');
		if($var==1)
		{
			$str="\nIPN PAYPAL TRANSACTION ID: " . $this->posted_data['txn_id'] ."\n";
			$str.="SUCCESS\n";
			$str.="DATE: ". $data . "\n";
			$str.="PAYER EMAIL: " . $this->posted_data['payer_email']. "\n";
			$str.="NAME: " . $this->posted_data['last_name']." ".$this->posted_data['first_name']. "\n";
			$str.="LINK ID: ". $this->posted_data['item_number']. "\n";
			$str.="QUANTITY: ". $this->posted_data['quantity']. "\n";
			$str.="TOTAL: "   . $this->posted_data['mc_gross']. "\n\n\n";
		}
		else
		{
			$str="\nIPN PAYPAL TRANSACTION ID:\n";
			$str.="INVALID\n";
			$str.="REMOTE IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
			$str.="ERROR: ". $this->posted_data['error'] . "\n";
			$str.="DATE: ". $data . "\n";
			$str.="PAYER EMAIL: " . $this->posted_data['payer_email']. "\n";
			$str.="NAME: " . $this->posted_data['last_name']." ".$this->posted_data['first_name']. "\n";
			$str.="LINK ID: ". $this->posted_data['item_number']. "\n";
			$str.="QUANTITY: ". $this->posted_data['quantity']. "\n";
			$str.="TOTAL: "   . $this->posted_data['mc_gross']. "\n\n\n";
		}
		if($fp)
		@ fputs($fp,$str);

		@ fclose($fp);
	}*/

	/*function headers_nocache()
	{
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		header('Pragma: no-cache');
	}
*/

}
