<?php
namespace Behat\Test;
class Service3 {
    private $arg1;

    public function __construct($arg1)
    {
        $this->arg1 = $arg1;
    }

    public function test()
    {
        $this->arg1 + 1;
    }
} 