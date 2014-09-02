<?php
/**
 * Form that contains login elements
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class LoginForm extends Zend_Form
{
    /**
     * Initialization of the form
     */
    public function init()
    {
        $emailElement    = $this->_getEmailElement();
        $passwordElement = $this->_getPasswordElement();
        $submitElement   = $this->_getSubmitElement();
        
        $this->addElements(
            array(
                $emailElement, $passwordElement, $submitElement
            )
        );
    }
    
    private function _getEmailElement()
    {
        
    }
    
    private function _getPasswordElement()
    {
        
    }
    
    private function _getSubmitElement()
    {
        
    }
}
