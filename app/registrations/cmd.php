<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2013
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists("registration_domain") || permission_exists("registration_all") || if_group("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the variables
	$cmd = check_str($_GET['cmd']);
	$rdr = check_str($_GET['rdr']);
	$domain = check_str($_GET['domain']);
	$user = check_str($_GET['user']);
	$agent = check_str($_GET['agent']);

//get the vendor
	if (substr($agent, 0, 6) == "Aastra") {
		$vendor = "aastra";
	}
	if (substr($agent, 0, 9) == "Cisco/SPA") {
		$vendor = "cisco";
	}
	if (substr($agent, 0,11) == "Grandstream") {
		$vendor = "grandstream";
	}
	if (substr($agent, 0, 10) == "PolycomVVX") {
		$vendor = "polycom";
	}
	if (substr($agent, 0, 7) == "Yealink") {
		$vendor = "yealink";
	}

//create the event socket connection
	$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
	if ($fp) {
		//app.lua event_notify
			if ($cmd == "reboot" || $cmd == "resync") {
				$cmd = "api luarun app.lua event_notify ".$cmd." ".$user." ".$domain." ".$vendor;
				$response = event_socket_request($fp, $cmd);
				unset($cmd);
			}

		//close the connection
			fclose($fp);
	}

//redirect the user
	if ($rdr == "false") {
		//redirect false
		echo $response;
	}
	else {
		header("Location: status_registrations.php?profile=internal&savemsg=".urlencode($response));
	}

?>