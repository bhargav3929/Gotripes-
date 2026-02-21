<?php
// DEPRECATED: CCAvenue encryption helpers — no longer used. Replaced by Nomod Hosted Checkout.
// Kept for historical reference and potential decryption of old CCAvenue responses.

error_reporting(0);
ini_set('display_errors', '0');

function ccavenue_encrypt($plainText, $key)
{
	$blockSize = 16;
	$plainPad = pkcs5_pad($plainText, $blockSize);
	$secretKey = hex2bin(md5($key));
	$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
	$openMode = openssl_encrypt($plainPad, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
	$encryptedText = bin2hex($openMode);
	return $encryptedText;
}

function ccavenue_decrypt($encryptedText, $key)
{
	$secretKey = hex2bin(md5($key));
	$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
	$encryptedText = hex2bin($encryptedText);
	$decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
	return rtrim($decryptedText, "\0");
}
//*********** Padding Function *********************

function pkcs5_pad($plainText, $blockSize)
{
	$pad = $blockSize - (strlen($plainText) % $blockSize);
	return $plainText . str_repeat(chr($pad), $pad);
}

//********** Hexadecimal to Binary function for php 4.0 version ********

function hextobin($hexString)
{
	$length = strlen($hexString);
	$binString = "";
	$count = 0;
	while ($count < $length) {
		$subString = substr($hexString, $count, 2);
		$packedString = pack("H*", $subString);
		if ($count == 0) {
			$binString = $packedString;
		} else {
			$binString .= $packedString;
		}

		$count += 2;
	}
	return $binString;
}