<?php

class SimpleMailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $from;
    private $socket;
    private $timeout = 30;
    private $debug = false;

    public function __construct() {
        $this->host = getenv('SMTP_HOST');
        $this->port = getenv('SMTP_PORT');
        $this->username = getenv('SMTP_USERNAME');
        $this->password = getenv('SMTP_PASSWORD');
        $this->from = getenv('SMTP_USERNAME');
    }

    private function connect() {
        $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        if (!$this->socket) {
            throw new Exception("Could not connect to SMTP host: $errstr ($errno)");
        }
        $this->getResponse();
        return true;
    }

    private function getResponse() {
        $response = '';
        while ($str = fgets($this->socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == ' ') break;
        }
        if ($this->debug) {
            error_log($response);
        }
        return $response;
    }

    private function sendCommand($command) {
        if ($this->debug) {
            error_log($command);
        }
        fwrite($this->socket, $command . "\r\n");
        return $this->getResponse();
    }

    private function authenticate() {
        // Send EHLO command
        $this->sendCommand("EHLO " . $_SERVER['SERVER_NAME']);
        
        // Start TLS
        $this->sendCommand("STARTTLS");
        stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        
        // Send EHLO again after TLS
        $this->sendCommand("EHLO " . $_SERVER['SERVER_NAME']);
        
        // Authentication
        $this->sendCommand("AUTH LOGIN");
        $this->sendCommand(base64_encode($this->username));
        $this->sendCommand(base64_encode($this->password));
    }

    public function send($to, $subject, $body) {
        try {
            $this->connect();
            $this->authenticate();

            // Set sender and recipient
            $this->sendCommand("MAIL FROM: <{$this->from}>");
            $this->sendCommand("RCPT TO: <$to>");

            // Send email content
            $this->sendCommand("DATA");
            
            // Construct headers and message
            $headers = [
                "From: SnackHaven <{$this->from}>",
                "To: <$to>",
                "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
                "MIME-Version: 1.0",
                "Content-Type: text/html; charset=UTF-8",
                "Content-Transfer-Encoding: base64",
                ""
            ];

            $message = implode("\r\n", $headers) . "\r\n" . chunk_split(base64_encode($body));
            $this->sendCommand($message . "\r\n.");

            // Close connection
            $this->sendCommand("QUIT");
            fclose($this->socket);

            return true;
        } catch (Exception $e) {
            error_log("Mail Error: " . $e->getMessage());
            if ($this->socket) {
                fclose($this->socket);
            }
            return false;
        }
    }
}

function sendMail($to, $subject, $body) {
    $mailer = new SimpleMailer();
    return $mailer->send($to, $subject, $body);
} 