<?php

/**
 * Email API Sender
 * Supports multiple email APIs: Resend, SendGrid, Mailgun
 */
class EmailAPI {
    private $provider;
    private $apiKey;
    private $fromEmail;
    private $fromName;

    public function __construct() {
        // Load configuration
        $config = require __DIR__ . '/email_api_config.php';
        
        $this->provider = $config['provider'] ?? 'resend';
        $this->apiKey = $config['api_key'] ?? '';
        $this->fromEmail = $config['from_email'] ?? '';
        $this->fromName = $config['from_name'] ?? 'Nutrimind';
    }

    /**
     * Send email using configured API
     */
    public function sendEmail($toEmail, $toName, $subject, $htmlBody) {
        if (empty($this->apiKey)) {
            return false;
        }

        switch ($this->provider) {
            case 'resend':
                return $this->sendViaResend($toEmail, $toName, $subject, $htmlBody);
            
            case 'sendgrid':
                return $this->sendViaSendGrid($toEmail, $toName, $subject, $htmlBody);
            
            case 'mailgun':
                return $this->sendViaMailgun($toEmail, $toName, $subject, $htmlBody);
            
            default:
                return false;
        }
    }

    /**
     * Send via Resend API (Recommended - Easiest)
     */
    private function sendViaResend($toEmail, $toName, $subject, $htmlBody) {
        $url = 'https://api.resend.com/emails';
        
        $data = [
            'from' => $this->fromName . ' <' . $this->fromEmail . '>',
            'to' => [$toEmail],
            'subject' => $subject,
            'html' => $htmlBody
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    /**
     * Send via SendGrid API
     */
    private function sendViaSendGrid($toEmail, $toName, $subject, $htmlBody) {
        $url = 'https://api.sendgrid.com/v3/mail/send';
        
        $data = [
            'personalizations' => [
                [
                    'to' => [
                        ['email' => $toEmail, 'name' => $toName]
                    ]
                ]
            ],
            'from' => [
                'email' => $this->fromEmail,
                'name' => $this->fromName
            ],
            'subject' => $subject,
            'content' => [
                [
                    'type' => 'text/html',
                    'value' => $htmlBody
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 202;
    }

    /**
     * Send via Mailgun API
     */
    private function sendViaMailgun($toEmail, $toName, $subject, $htmlBody) {
        // Mailgun requires domain in config
        $config = require __DIR__ . '/email_api_config.php';
        $domain = $config['mailgun_domain'] ?? '';
        
        if (empty($domain)) {
            return false;
        }

        $url = "https://api.mailgun.net/v3/{$domain}/messages";
        
        $data = [
            'from' => $this->fromName . ' <' . $this->fromEmail . '>',
            'to' => $toEmail,
            'subject' => $subject,
            'html' => $htmlBody
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $this->apiKey);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    /**
     * Test email configuration
     */
    public function testConnection() {
        $testEmail = $this->fromEmail;
        $testSubject = 'Test Email from Nutrimind';
        $testBody = '<p>This is a test email to verify your email API configuration is working correctly.</p>';
        
        return $this->sendEmail($testEmail, 'Test User', $testSubject, $testBody);
    }
}
?>
