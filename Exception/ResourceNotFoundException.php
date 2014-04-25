<?php

namespace L10nBundle\Exception;


class ResourceNotFoundException extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct($message = null)
    {
        parent::__construct($message);
    }
}