<?php

namespace Zend\Session\Validator;

use Zend\Session\Validator\ValidatorInterface as SessionValidator;

class OwnSession implements SessionValidator
{
    /**
     * Internal data.
     *
     * @var string
     */
    protected $data;

    /**
     * Constructor
     * get the current user session ID and store it in the session as 'valid data'
     *
     * @param null|string $data
     */
    public function __construct($data = null)
    {
        if (empty($data)) {
            $data = session_id();
        }
        $this->data = $data;
    }

    /**
     * isValid() - this method will determine if the current user IP matches the
     * IP we stored when we initialized this variable.
     *
     * @return bool
     */
    public function isValid()
    {
        return (session_id() === $this->getData());
    }

    /**
     * Retrieve token for validating call
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return validator name
     *
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }
}
