<?php

if (isset($_POST['name']) && isset($_POST['descr'])) {
	$name = $_POST['name'];
	$desc = $_POST['descr'];
	$redirect = urlencode('http://kokarn.com/kokathon/AutoDeploy/authed.php?name=' . $name . '&desc='. $desc);
	$get = "client_id=115d413f5ad3eb4c0a01&scope=repo&redirect_uri=" . $redirect;
	$url = 'https://github.com/login/oauth/authorize?' . $get;
}

header('Location: ' . $url) ;

?>