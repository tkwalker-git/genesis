<?php

	$codeFormat		= 'code39extended'; // codabar, code11, code39, code39extended, code93, code128, ean8, ean13, gs1128, isbn, i25, s25, msi, upca, upce, upcext2, upcext5, postnet, othercode
	$output			= 1; // 1=PNG, 2=JPEG, 3=GIF
	$dpi			= 72;
	$thickness		= 30;
	$resolution		= 1; // Resolution => 1, 2, 3
	$rotation		= 0.0; // 0, 90, 180, 270 
	$font_family	= 'Arial.ttf';
	$font_size		= 8;
	$text			= $ticket_number;
	$a1				= '';
	$a2				= '';
	$a3				= '';


	$class_dir = 'class';
	require_once($class_dir . '/BCGColor.php');
	require_once($class_dir . '/BCGBarcode.php');
	require_once($class_dir . '/BCGDrawing.php');
	require_once($class_dir . '/BCGFontFile.php');

	if(include_once($class_dir . '/BCG' . $codeFormat . '.barcode.php')) {
		if($font_family !== '0' && $font_family !== '-1' && intval($font_size) >= 1) {
			$font = new BCGFontFile($class_dir . '/font/' . $font_family, intval($font_size));
		} else {
			$font = 0;
		}

		$color_black = new BCGColor(0, 0, 0);
		$color_white = new BCGColor(255, 255, 255);
		$codebar = 'BCG' . $codeFormat;

		$drawException = null;
		try {
			$code_generated = new $codebar();
			if(isset($a1) && intval($a1) === 1) {
				$code_generated->setChecksum(true);
			}
			if(isset($a2) && !empty($a2)) {
				$code_generated->setStart($a2 === 'NULL' ? null : $a2);
			}
			if(isset($a3) && !empty($a3)) {
				$code_generated->setLabel($a3 === 'NULL' ? null : $a3);
			}
			$code_generated->setThickness($thickness);
			$code_generated->setScale($resolution);
			$code_generated->setBackgroundColor($color_white);
			$code_generated->setForegroundColor($color_black);
			$code_generated->setFont($font);

			$code_generated->parse($text);
		} catch(Exception $exception) {
			$drawException = $exception;
		}

		$drawing = new BCGDrawing('', $color_white);
		if($drawException) {
			$drawing->drawException($drawException);
		} else {
			$drawing->setBarcode($code_generated);
			$drawing->setRotationAngle($rotation);
			$drawing->setDPI($dpi == 'null' ? null : (int)$dpi);
			$drawing->draw();
		}

		if(intval($output) === 1) {
			header('Content-Type: image/png');
		} elseif(intval($output) === 2) {
			header('Content-Type: image/jpeg');
		} elseif(intval($output) === 3) {
			header('Content-Type: image/gif');
		}

echo $drawing->finish(intval($output));

		
	}
	else{
		header('Content-Type: image/png');
		readfile('error.png');
	}
	

?>