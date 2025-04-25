<?php
class SMTP {
    private $host;
    private $port;
    private $username;
    private $password;
    private $secure = 'tls';
    private $from_email;
    private $from_name;
    private $to;
    public $subject;
    public $body;
    private $is_html = true;

    public function __construct() {
        $this->host = SMTP_HOST;
        $this->port = SMTP_PORT;
        $this->username = SMTP_USERNAME;
        $this->password = SMTP_PASSWORD;
    }

    public function isSMTP() {
        // Method to indicate SMTP usage
        return true;
    }

    public function setFrom($email, $name = '') {
        $this->from_email = $email;
        $this->from_name = $name;
    }

    public function addAddress($email) {
        $this->to = $email;
    }

    public function isHTML($bool) {
        $this->is_html = $bool;
    }

    public function send() {
        $headers = array();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: " . ($this->is_html ? "text/html" : "text/plain") . "; charset=UTF-8";
        $headers[] = "From: " . $this->from_name . " <" . $this->from_email . ">";
        $headers[] = "Reply-To: " . $this->from_email;
        
        // Additional required headers for SMTP
        $headers[] = "X-Mailer: PHP/" . phpversion();
        
        // Create socket connection to SMTP server
        $smtp = fsockopen(
            ($this->secure === 'ssl' ? 'ssl://' : '') . $this->host,
            $this->port,
            $errno,
            $errstr,
            30
        );

        if (!$smtp) {
            throw new Exception("Could not connect to SMTP server: $errstr ($errno)");
        }

        // Read server greeting
        $this->getResponse($smtp);

        // Send EHLO command
        $this->sendCommand($smtp, "EHLO " . $_SERVER['SERVER_NAME']);

        // Start TLS if required
        if ($this->secure === 'tls') {
            $this->sendCommand($smtp, "STARTTLS");
            stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->sendCommand($smtp, "EHLO " . $_SERVER['SERVER_NAME']);
        }

        // Authenticate
        $this->sendCommand($smtp, "AUTH LOGIN");
        $this->sendCommand($smtp, base64_encode($this->username));
        $this->sendCommand($smtp, base64_encode($this->password));

        // Send email
        $this->sendCommand($smtp, "MAIL FROM:<" . $this->from_email . ">");
        $this->sendCommand($smtp, "RCPT TO:<" . $this->to . ">");
        $this->sendCommand($smtp, "DATA");

        // Send headers and message
        $message = implode("\r\n", $headers) . "\r\n\r\n" . $this->body . "\r\n.";
        $this->sendCommand($smtp, $message);

        // Close connection
        $this->sendCommand($smtp, "QUIT");
        fclose($smtp);

        return true;
    }

    private function sendCommand($smtp, $command) {
        fwrite($smtp, $command . "\r\n");
        return $this->getResponse($smtp);
    }

    private function getResponse($smtp) {
        $response = '';
        while ($str = fgets($smtp, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == ' ') break;
        }
        if (substr($response, 0, 3) >= 400) {
            throw new Exception("SMTP Error: " . $response);
        }
        return $response;
    }
} 