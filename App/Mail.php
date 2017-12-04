<?php
/**
 * User: idgu
 * Date: 23.11.2017
 * Time: 11:11
 */

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \App\Config;

class Mail
{
    public static function send($to, $subject, $text, $html)
    {
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
//            $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = Config::MAIL_HOST;  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = Config::MAIL_USERNAME;                 // SMTP username
            $mail->Password = Config::MAIL_PASSWORD;                           // SMTP password
            $mail->SMTPSecure = Config::MAIL_SMTP_SECURE;                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = Config::MAIL_PORT;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('from@example.com', 'Robciu');
            $mail->addAddress($to, 'User');

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $text;

            $mail->send();
        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
    }
}