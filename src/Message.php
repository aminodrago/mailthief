<?php

namespace MailThief;

use Exception;

class Message
{
    public $body;
    public $view;
    public $data;
    public $subject;
    public $from;
    public $sender;
    public $to;
    public $cc;
    public $bcc;
    public $reply_to;
    public $priority;
    public $attachments;

    public function __construct($body, $view = null, $data = [])
    {
        $this->body = $body;
        $this->view = $view;
        $this->data = $data;
        $this->to = collect();
        $this->cc = collect();
        $this->bcc = collect();
        $this->reply_to = collect();
        $this->attachments = collect();
    }

    public static function fromView($view, $data, $views)
    {
        $body = $views->make($view, $data)->render();
        return new self($body, $view, $data);
    }

    public static function fromRaw($body)
    {
        return new self($body);
    }

    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function to($address, $name = null)
    {
        if (! is_array($address)) {
            $address = $name ? [$address => $name] : [$address];
        }

        $this->to = $this->to->merge($address);

        return $this;
    }

    public function cc($address, $name = null)
    {
        if (! is_array($address)) {
            $address = $name ? [$address => $name] : [$address];
        }

        $this->cc = $this->cc->merge($address);

        return $this;
    }

    public function bcc($address, $name = null)
    {
        if (! is_array($address)) {
            $address = $name ? [$address => $name] : [$address];
        }

        $this->bcc = $this->bcc->merge($address);

        return $this;
    }

    public function replyTo($address, $name = null)
    {
        if (! is_array($address)) {
            $address = $name ? [$address => $name] : [$address];
        }

        $this->reply_to = $this->reply_to->merge($address);

        return $this;
    }

    public function from($address, $name = null)
    {
        if (! is_array($address)) {
            $address = $name ? [$address => $name] : [$address];
        }

        $this->from = $address;

        return $this;
    }

    public function sender($address, $name = null)
    {
        if (! is_array($address)) {
            $address = $name ? [$address => $name] : [$address];
        }

        $this->sender = $address;

        return $this;
    }

    public function priority($level)
    {
        $this->priority = $level;
        return $this;
    }

    public function hasRecipient($email)
    {
        return $this->recipients()->has($email) || $this->recipients()->contains($email);
    }

    public function recipients()
    {
        return $this->to->merge($this->cc)->merge($this->bcc);
    }

    public function contains($text)
    {
        return str_contains($this->body, $text);
    }

    public function attach($pathToFile, array $options = [])
    {
        $this->attachments[] = ['path' => $pathToFile, 'options' => $options];
        return $this;
    }

    public function attachData($data, $name, array $options = [])
    {
        $this->attachments[] = ['data' => $data, 'name' => $name, 'options' => $options];
        return $this;
    }

    public function getSwiftMessage()
    {
        throw new Exception("Cannot get Swift message from MailThief message.");
    }
}
