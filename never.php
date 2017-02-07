#!/usr/bin/php
<?php
# change above path to your php system path

# include vendor lib
require (__DIR__ . '/vendor/autoload.php');
//use \NeverBounce\API\NB_Bulk;
# EDIT ME
$apikey = '';
$apisecret = '';

\NeverBounce\API\NB_Auth::auth($apisecret, $apikey);

echo "=========== NEVERBOUNCE EMAIL VALIDATOR ==========\n";
echo "Pick your source?\n";
echo "1. File (.csv)\n";
echo "2. Email Query\n" . PHP_EOL;

$source = getInput();

switch ($source) {
	case '1':
		echo "You Choose File input\n";
		echo "Please write your csv filename\n" . PHP_EOL;
		$fileSource = getInput();
		$file = explode('.', $fileSource);

		$validateExtension = in_array($file[1], [csv]);

		if ($validateExtension)
		{
			$emails = array_map('str_getcsv', file($fileSource));

			foreach ($emails as $key => $email) {
				$resp = \NeverBounce\API\NB_Single::app()->verify($email[0]);
				if($resp->is([0,3,4])) {
				    fwrite(STDOUT, sprintf("\n ".$email[0]." Accepted (%s)", $resp->definition()));
				} else {
				    fwrite(STDOUT, sprintf("\n ".$email[0]." Rejected (%s)", $resp->definition()));
				}

				sleep(2);
			}
		}

		break;
	case '2' :
		echo "email address:\n" . PHP_EOL;
		$email = getInput();
		$resp = \NeverBounce\API\NB_Single::app()->verify($email);
		if($resp->is([0,3,4])) {
		    fwrite(STDOUT, sprintf("\n ".$email." Accepted (%s)", $resp->definition()));
		} else {
		    fwrite(STDOUT, sprintf("\n ".$email." Rejected (%s)", $resp->definition()));
		}

		break;
	default:
		echo "gak ada opsi itu mas!";
		break;
}


function getInput()
{
	$fr = fopen("php://stdin","r");   
	$input = fgets($fr,128);        
	$input = rtrim($input);         
	fclose ($fr);                   
	return $input;               
}