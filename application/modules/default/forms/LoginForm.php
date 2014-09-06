<?php
/**
 * Form that contains login elements
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class Form_LoginForm extends Zend_Form
{
    /**
     * Initialization of the form
     */
    public function init()
    {
        $this->setAction('/default/authentication/login');
        $this->setMethod(Zend_Http_Client::POST);
        
        $emailElement    = $this->_getEmailElement();
        $passwordElement = $this->_getPasswordElement();
        $submitElement   = $this->_getSubmitElement();
        
        $this->addElements(
            array(
                $emailElement, $passwordElement, $submitElement
            )
        );
        
        $this->setDecorators(
            array(
                array(
                    'ViewScript',
                    array(
                        'viewScript' => '/forms/Decorators/loginForm.phtml',
                        'class' => 'form'
                    )
                )
            )
        );
    }
    
    private function _getEmailElement()
    {
        $emailValidator = new Zend_Validate_EmailAddress();
        
        $emailElement = new Zend_Form_Element_Text('email');
        $emailElement->setLabel('Email')
                     ->setRequired(true)
                     ->addValidator($emailValidator);

        return $emailElement;
    }
    
    private function _getPasswordElement()
    {
        $credentialsVaidator = new Form_Validate_Credentials();
        
        $passwordElement = new Zend_Form_Element_Password('password');
        $passwordElement->setRequired(true)
                        ->setLabel('Password')
                        ->addValidator($credentialsVaidator);
        
        return $passwordElement;
    }
    
    private function _getSubmitElement()
    {
        $submitElement = new Zend_Form_Element_Submit('Log in');
        $submitElement->setValue('Log in');
        
        return $submitElement;
    }
}
