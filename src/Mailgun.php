<?php

namespace CronixWeb\Mailgun;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class Mailgun
{

    private string $apiKey;
    private string $subject;
    private array $from;
    private array $to = [];
    private string $template;

    private array $variables = [];

    private array $attachments = [];
    private bool $debug = false;

    private string $domain = '';
    private string $html = '';

    public function html(string $html): static
    {
        $this->html = $html;
        return $this;
    }

    public function domain(string $domain): static
    {
        $this->domain = $domain;
        return $this;
    }

    private function __construct(string $apiKey = '', string $envKey = 'MAILGUN_API_KEY')
    {
        if (empty($apiKey)) {
            $apiKey = env($envKey, '');
        }
        $this->apiKey = $apiKey;
    }


    /**
     * @param string $apiKey Api key provided by mailgun
     * @param string $envKey
     * @return Mailgun
     */
    static function init(string $apiKey = '', string $envKey = 'MAILGUN_API_KEY'): Mailgun
    {
        return new Mailgun($apiKey, $envKey);
    }

    public function debug(bool $debug): static
    {
        $this->debug = $debug;
        return $this;
    }

    public function subject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function template(string $template): static
    {
        $this->template = $template;
        return $this;
    }

    public function from(string $email, string $name = ''): static
    {
        $this->from['email'] = $email;
        $this->from['name'] = $name;
        return $this;
    }

    public function to(array|string $to, string $name = ''): static
    {
        if (is_array($to)) {
            $this->to = array_merge($this->to, $to);
        } else {
            $this->to[] = [
                'email' => $to,
                'name' => $name
            ];
        }

        return $this;
    }

    public function variables(array $variables): static
    {
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }

    public function attachment(string $filepath, string $name): static
    {
        $this->attachments[] = [
            'path' => $filepath,
            'name' => $name
        ];
        return $this;
    }

    public function send()
    {
        //Validate Domain
        if(empty(trim($this->domain))){
            throw new MailgunException("Invalid Domain");
        }


        //Validate From email address
        if (empty(trim($this->from['email']))) {
            throw new MailgunException("Invalid From Address");
        }

        //Validate if there is any recipient
        if (sizeof($this->to) == 0) {
            throw new MailgunException("No Recipients available");
        }

        //Validate recipient email address
        foreach ($this->to as $to) {
            if (empty(trim($to['email']))) {
                throw new MailgunException("Invalid Recipient Address");
            }
        }

        //Validate subject
        if (empty(trim($this->subject))) {
            throw new MailgunException("Subject is Empty");
        }

        //Validate template
        if (empty(trim($this->template)) && empty(trim($this->html))) {
            throw new MailgunException("Template & HTML is Empty");
        }

        if (empty(trim($this->html))) {
            throw new MailgunException("Body is Empty");
        }

        //Validate template
        if (empty(trim($this->apiKey))) {
            throw new MailgunException("Invalid API Key");
        }

        $body = [
            'from' => $this->formatEmailAddress($this->from),
            'to' => $this->formatRecipients(),
            'subject' => $this->subject,
            'h:X-Mailgun-Variables' => json_encode($this->variables)
        ];

        if(!empty(trim($this->template))){
            $body['template'] = $this->template;
        }

        if(!empty(trim($this->html))){
            $body['html'] = $this->html;
        }

        $http = Http::withBasicAuth('api', $this->apiKey);

        if (count($this->attachments) > 0) {

            $http = $http->asMultipart();

            foreach ($this->attachments as $attachment) {
                $http = $http->attach(
                    'attachment',
                    file_get_contents($attachment['path']),
                    $attachment['name']
                );
            }
        } else {
            $http = $http->asForm();
        }

        $response = $http->post('https://api.mailgun.net/v3/'.$this->domain.'/messages', $body);


        if($this->debug){
            Log::debug("SENDING MAIL " . json_encode([
                'request' => $body, 'response' => $response->body(),
            ]));
        }


        if ($response->status() == 200) {
            return true;
        }

        if ($this->debug) {
            return $response;
        }

        return false;

    }

    private function formatEmailAddress($emailAddress): string
    {
        return $emailAddress['name'] . ' <' . $emailAddress['email'] . '>';
    }

    private function formatRecipients(): string
    {
        $recipients = [];
        foreach ($this->to as $to) {
            $recipients[] = $this->formatEmailAddress($to);
        }
        return implode(",", $recipients);
    }
}
