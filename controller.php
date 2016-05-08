<?php
/*
 * Copyright (c) Codiad & Andr3as, distributed
 * as-is and without warranty under the MIT License. 
 * See http://opensource.org/licenses/MIT for more information.
 * This information must remain intact.
 */
	//error_reporting(0);
	
	require_once('../../common.php');
	
	if ($_GET['action'] !== 'authenticate') {
		checkSession();
	}
	
	require_once('class.tfa.php');
	$Auth = new TFA();

	switch($_GET['action']) {

		case 'authenticate':
			if(!isset($_POST['username']) || !isset($_POST['password'])){
				die(json_encode(array('status' => "error", 'message' => 'Missing username or password')));
			}

			$Auth->username = $_POST['username'];
			$Auth->password = $_POST['password'];
			if (isset($_POST['token'])) {
				$Auth->token = $_POST['token'];
			}
			$Auth->Authenticate();
			break;

		case 'switchState':
			if(!isset($_POST['token']) || !isset($_POST['password'])){
				die(json_encode(array('status' => "error", 'message' => 'Missing token or password')));
			}

			$Auth->username = $_SESSION['user'];
			$Auth->password = $_POST['password'];
			$Auth->token = $_POST['token'];

			if ($Auth->IsTFAEnabled()) {
				$Auth->DisableTFA();
			} else {
				$Auth->EnableTFA();
			}
			break;

		default:
			echo '{"status":"error","message":"No Type"}';
			break;
	}
	
	if ($Auth->returnMessage !== '') {
		echo $Auth->returnMessage;
	}

	function getWorkspacePath($path) {
		//Security check
		if (!Common::checkPath($path)) {
			die('{"status":"error","message":"Invalid path"}');
		}
		if (strpos($path, "/") === 0) {
			//Unix absolute path
			return $path;
		}
		if (strpos($path, ":/") !== false) {
			//Windows absolute path
			return $path;
		}
		if (strpos($path, ":\\") !== false) {
			//Windows absolute path
			return $path;
		}
		return "../../workspace/".$path;
	}
?>