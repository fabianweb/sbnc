<?php
namespace Fw\Sbnc\Modules;

class validate implements module {

    private $options = [
        'email' => ['email', 'mail'],
        'url'   => ['url', 'link', 'web']
    ];

    public function __construct(&$master) {

    }

    public function check($master) {

    }

}