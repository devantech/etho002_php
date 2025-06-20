<?php

$ip_address = "192.168.0.200";
$port = 17494;
$password = "password";

$GET_OUTPUTS = 36;
$GET_UNLOCKTIME = 122;
$PASSWORDENTRY = 121;
$output_active = 32;
$output_inactive = 33;
$output_number = 1;
$output_pulsetime = 0;

$fp = fsockopen($ip_address,$port, $errno, $errstr, 30);
if (!$fp) {
    	echo "$errstr ($errno)<br />\n";
} 
else {
    	echo "connected\n";
	$msg = pack("C",$GET_UNLOCKTIME);
	fwrite($fp,$msg);
	$returned_data1 = ord(fread($fp,1));
	if($returned_data1 == 255) echo "no password required\n";
	else{
		echo"password required\n";
		$msg = pack("C",$PASSWORDENTRY);
		$msg = $msg . $password;
		fwrite($fp,$msg);
		echo"password sent\n";
		$returned_data1 = ord(fread($fp,1));
		if($returned_data1 == 1) echo "password accepted\n";
		else {
			echo "password failed\n";
			exit();
		}
	}

	$msg = pack ("C",$GET_OUTPUTS);
	fwrite($fp,$msg);
	$returned_data1 = ord(fread($fp,1));
	echo "relays = $returned_data1\n";
	
	if(($returned_data1 & (1 << ($output_number - 1)))) {
		$msg = pack("CCC",$output_inactive,$output_number,$output_pulsetime);
		echo"clear\n";
	}
	else{
		$msg = pack("CCC",$output_active,$output_number,$output_pulsetime);
		echo "set\n";
	}
	
	fwrite($fp,$msg);
	echo "string sent\n";
	$returned_data1 = ord(fread($fp,1));
	if($returned_data1 == 0) echo "success\n";
	else echo "failed\n";
	fclose($fp);
}

?>
