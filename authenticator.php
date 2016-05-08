<?php
/*
 * Copyright (c) Andr3as
 * as-is and without warranty under the MIT License. 
 * See http://opensource.org/licenses/MIT for more information.
 * This information must remain intact.
 */

	$folder = str_replace(dirname(dirname(AUTH_PATH)),"",dirname(AUTH_PATH));
	$path = $_SERVER['REQUEST_URI'];

	if (!isset($_SESSION['user']) && strpos($path,"plugins" . $folder) === false) {
		
		
		$pos = strpos($path,'index.php');
		if ($pos !== false) {
			$path = substr($path,0,$pos);
		}
		
		if ($path[strlen($path) - 1] != "/") {
			$path .= "/";
		}
		
		header("Location: " . $path . "plugins" . $folder . "/index.php");
	}

?>