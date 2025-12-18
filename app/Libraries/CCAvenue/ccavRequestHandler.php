<?php
// === DEBUG: Display all PHP errors in browser ===
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Optionally, you can add a marker to know the file executes:
echo "<!-- ccavRequestHandler.php loaded -->";

// Check for required files
if (!file_exists('config.php')) {
    echo "<b style='color:red;'>config.php not found!</b><br>";
}
if (!file_exists('Crypto.php')) {
    echo "<b style='color:red;'>Crypto.php not found!</b><br>";
}

// If this file is accessed without POST data, display a notice (for development only)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<b style='color:orange;'>Warning: This handler expects POST data from the payment form. Loading it directly in the browser will not process a payment.</b><br>";
}
?>


<html>
<head>
<title> Iframe</title>
</head>
<body>
<center>
<?php include('Crypto.php')?>
<?php 

	error_reporting(0);

	$working_key='53778';//Shared by CCAVENUES
	$access_code='AVEJ05MB28CE69JEEC';//Shared by CCAVENUES
	$merchant_data='0F2F4322154A045DEC537312307C387F';
	
	foreach ($_POST as $key => $value){
		$merchant_data.=$key.'='.$value.'&';
	}
	
	$encrypted_data=ccavenue_encrypt($merchant_data,$working_key); // Method for encrypting the data.

	$production_url=config('services.ccavenue.url').'?command=initiateTransaction&encRequest='.$encrypted_data.'&access_code='.$access_code;
?>
<iframe src="<?php echo $production_url?>" id="paymentFrame" width="482" height="450" frameborder="0" scrolling="No" ></iframe>

<script type="text/javascript" src="jquery-1.7.2.js"></script>
<script type="text/javascript">
    	$(document).ready(function(){
    		 window.addEventListener('message', function(e) {
		    	 $("#paymentFrame").css("height",e.data['newHeight']+'px'); 	 
		 	 }, false);
	 	 	
		});
</script>
</center>
</body>
</html>
