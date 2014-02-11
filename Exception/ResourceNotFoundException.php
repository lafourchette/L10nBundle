<?php

namespace L10nBundle\Exception;


class ResourceNotFoundException extends \Exception
{
    public function __construct($message = null)
    {
        parent::__construct($message);
    }
}