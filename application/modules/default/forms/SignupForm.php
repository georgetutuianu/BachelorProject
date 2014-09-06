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
        
        $this->setDecorators(
            array(
                array(
                    'ViewScript',
                    array(
                        'viewScript' => '/forms/Decorators/signupForm.phtml',
                        'class' => 'form'
                    )
                )
            )
        );
    }
    
    private function _getConfirmPasswordElement()
    {
        $passwordMatchValidator = new Form_Validate_PasswordMatch();
        
        $confirmPasswordElement = new Zend_Form_Element_Password('Password Confirm');
        $confirmPasswordElement->setRequired()
                               ->setLabel('Confirm password')
                               ->addValidator($passwordMatchValidator);
        
        return $confirmPasswordElement;
    }
    
    private function _getEmailElement()
    {
        $emailValidator = new Zend_Validate_EmailAddress();
        $emailNotExistsValidator = new Form_Validate_EmailNotTaken();
        
        $emailElement = new Zend_Form_Element_Text('Email');
        $emailElement->setRequired()
                     ->setLabel('Email')
                     ->addValidator($emailValidator)
                     ->addValidator($emailNotExistsValidator);
        
        return $emailElement;
    }
    
    private function _getPasswordElement()
    {
        $passwordLengthValidator = new Zend_Validate_StringLength();
        $passwordLengthValidator->setMin(4)->setMax(20);
        
        $passwordElement = new Zend_Form_Element_Password('Password');
        $passwordElement->setRequired()
                        ->setLabel('Password')
                        ->addValidator($passwordLengthValidator);
        
        return $passwordElement;
    }
    
    private function _getSubmitElement()
    {
        $submitElement = new Zend_Form_Element_Submit('Sign up');
        $submitElement->setValue('Sign up');
        
        return $submitElement;
    }
}
