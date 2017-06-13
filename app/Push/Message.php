<?php

namespace App\Push;

class Message
{
    // Two fields most likely to be present
    public $title;
    public $message;

    // Optional fields
    public $icon = 'img/favicon/favicon-96x96.png';
    public $url = 'https://htch.us/';

    // Extra data if needed
    public $options = [];

    /**
     * Message constructor.
     *
     * @param string $title
     * @param string $message
     * @param array $options can include url, icon and maybe other data options
     */
    public function __construct($title, $message, $options = [])
    {
        foreach ($options as $option => $value) {
            $this->$option = $value;
        }

        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Prepare payload containing all the data in this object
     * @return array
     */
    public function getAllOptions()
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
        ] + $this->options;
    }

    public function __set($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function __get($name)
    {
        return $this->options[$name];
    }

    public function __isset($name)
    {
        return isset($this->options[$name]);
    }

    public function __unset($name)
    {
        unset($this->options[$name]);
    }
}
