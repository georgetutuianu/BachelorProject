<?php
/**
 * Description of Credentials
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class Form_Validate_Credentials extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';
    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Email and password does not match'
    );
    
    public function isValid($value, $context = null)
    {
        $usersModel = new Model_Users();
        
        if ($usersModel->userIsValid($context['email'], $value)) {
            return true;
        }
        
        $this->_error(self::NOT_MATCH);
        return false;
    }
}
