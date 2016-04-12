<?php
namespace NinjaWars\core\extensions;

use NinjaWars\core\extensions\NWTemplate;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class StreamedViewResponse extends StreamedResponse {
    public function __construct($title, $template, $data = [], $options = [], $headers = []) {
        parent::__construct();

        $this->setCallback(function() use ($title, $template, $data, $options) {
            $view = new NWTemplate();
            $view->displayPage($template, $title, $data, $options);
        });

        $this->headers = new ResponseHeaderBag($headers);
    }
}
