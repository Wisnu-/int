<?php

class R {

	public static function to($loc = 'maintenance')
	{

		$site = $_SERVER['HTTP_HOST'];
		header("Location: http://$site/$loc.php");
	}

	public static function fail($message = '')
	{
		header("Status: 501 Not Implemented");
			$data = [
				'message' => $message 
			];
			echo json_encode($data);
		exit;
	}

}