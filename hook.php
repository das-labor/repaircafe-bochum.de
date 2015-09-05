<?php
if (isset($_POST['payload'])) {
	$payload = json_decode($_POST['payload']);
	$proc = proc_open("sudo /usr/local/bin/update-repaircafe.sh",
		array(
			array("pipe","r"),
			array("pipe","w"),
			array("pipe","w")
		),
		$pipes);
	print stream_get_contents($pipes[1]);
	print stream_get_contents($pipes[2]);
} else {
	echo 'Payload not available'; 
}
?>
