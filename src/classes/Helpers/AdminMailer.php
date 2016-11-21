<?php
/**
 * Created by jonas.
 * Project: recipe-api
 * Date: 2016-11-21
 */

namespace App\Helpers;


class AdminMailer {
	/**
	 * @var \PHPMailer
	 */
	public $mail;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->mail = new \PHPMailer();
		$this->mail->addAddress('', 'Jonas');
		$this->mail->setFrom('', 'Recipe API');
		//$this->mail->IsMAIL();
		$this->mail->isSMTP();                                      // Set mailer to use SMTP
		$this->mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
		$this->mail->SMTPAuth = true;                               // Enable SMTP authentication
		$this->mail->Username = '';                 // SMTP username
		$this->mail->Password = '';                           // SMTP password
		$this->mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$this->mail->Port = 587;                                    // TCP port to connect to
	}

	public function mailAdmin() {


		$this->mail->Subject = 'Here is the subject';
		$this->mail->Body    = 'This is the HTML message body <b>in bold!</b>';
		$this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if ( ! $this->mail->send() ) {
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $this->mail->ErrorInfo;
		} else {
			echo 'Message has been sent';
		}
	}
}