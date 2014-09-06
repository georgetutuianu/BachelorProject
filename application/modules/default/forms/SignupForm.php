<?php
/**
 * Description of SignupForm
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class Form_SignupForm extends Zend_Form
{
    public function init()
    {
        $this->setAction('/default/authentication/signup');
        $this->setMethod(Zend_Http_Client::POST);
        
        $emailElement           = $this->_getEmailElement();
        $passwordElement        = $this->_getPasswordElement();
        $confirmPasswordElement = $this->_getConfirmPasswordElement();
        $submitElement          = $this->_getSubmitElement();
        
        $this->addElements(
            array($emailElement, $passwordElement, $confirmPasswordElement, $submitElement)
        );
    }
    
    private function _getConfirmPasswordElement()
    {
        $confirmPasswordElement = new Zend_Form_Element_Password('Confirm Password');
        $confirmPasswordElement->setRequired()
                        ->setLabel('Confirm password');
        
        return $confirmPasswordElement;
    }
    
    private function _getEmailElement()
    {
        $emailElement = new Zend_Form_Element_Text('Email');
        $emailElement->setRequired()
                     ->setLabel('Email');
        
        return $emailElement;
    }
    
    private function _getPasswordElement()
    {
        $passwordElement = new Zend_Form_Element_Password('Password');
        $passwordElement->setRequired()
                        ->setLabel('Password');
        
        return $passwordElement;
    }
    
    private function _getSubmitElement()
    {
        $submitElement = new Zend_Form_Element_Submit('Sign up');
        $submitElement->setValue('Sign up');
        
        return $submitElement;
    }
}
