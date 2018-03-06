<?php
namespace myapp;

require_once 'vendor/swiftmailer/swiftmailer/lib/swift_required.php';
require_once 'lib/info.php';

class Mail
{
    private $config = null;
    private $info = null;
 

    public function __construct($config, $info)
    {
        $this->config = $config;
        $this->info = $info;
    }


    private function sendMessage($message)
    {
        // Create the Transport
        $transport = \Swift_SmtpTransport::newInstance(
            $this->config->email_account->SMTP,
            $this->config->email_account->port,
            $this->config->email_account->encryption
        )
        ->setUsername($this->config->email_account->username)
        ->setPassword($this->config->email_account->password);

        // Create the Mailer using your created Transport
        $mailer = \Swift_Mailer::newInstance($transport);

        return $mailer->send($message);
    }

    public function sendAutomaticResponse($sendToAddress, $msg)
    {
        $message = \Swift_Message::newInstance()
            // Give the message a subject
            ->setSubject('Auto response from ' . $this->info->name)

            // Set the From address with an associative array
            ->setFrom(array($this->config->email_account->email => $this->config->email_account->emailName))

            // Set the To addresses with an associative array
            ->setTo(array($sendToAddress))

            // And optionally an alternative body
            ->addPart(
                '<p>Hello<br>Your message was sent to the e-mail of ' . $this->info->name .'</p>'.
                '<p>Your original message was:<br>"' . $msg . '"</p>',
                'text/html'
            );
        $this->sendMessage($message);
    }

    public function sendSelfNotification($sendFromAddress, $msg)
    {
        $message = \Swift_Message::newInstance()
            // Give the message a subject
            ->setSubject('Message from ' . $sendFromAddress)

            // Set the From address with an associative array
            ->setFrom(array($this->config->email_account->email => $this->config->email_account->emailName))

            // Set the To addresses with an associative array
            ->setTo(array($this->config->email_account->email))

            // And optionally an alternative body
            ->addPart(
                '<p>Hello<br>You have just received a message from someone with the email "' . $sendFromAddress .'"</p>'.
                '<p>His message was:<br>"' . $msg . '"</p>',
                'text/html'
            );
        $this->sendMessage($message);
    }
}
