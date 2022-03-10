<?php

namespace App\Exception;

use Symfony\Component\Form\Form;

class InvalidFormException extends \RuntimeException
{
    const DEFAULT_ERROR_MESSAGE = 'The submitted data was invalid.';

    protected $form;

    /**
     * @param null   $form
     * @param string $message
     */
    public function __construct($form = null, $message = self::DEFAULT_ERROR_MESSAGE)
    {
        parent::__construct($message);

        $this->form = $form;
    }

    /**
     * @return Form|null
     */
    public function getForm()
    {
        return $this->form;
    }
}