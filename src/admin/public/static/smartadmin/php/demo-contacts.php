<?php
if( isset($_POST['name']) )
{
	$to = 'fake@example.com'; // Replace with your email
	
	$subject = $_POST['subject'];
	$message = $_POST['message'] . "\n\n" . 'Regards, ' . $_POST['name'] . '.';
	$headers = 'From: ' . $_POST['name'] . "\r\n" . 'Reply-To: ' . $_POST['email'] . "\r\n" . 'X-Mailer: PHP/' . phpversion();
	
	mail($to, $subject, $message, $headers);
}
?>