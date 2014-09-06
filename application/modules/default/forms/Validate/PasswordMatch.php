<?php
/**
 * Description of PasswordMatch
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class Form_Validate_PasswordMatch extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';
    
    protected $_messageTemplates = array(
        self::NOT_MATCH => 'The passwords does not match'
    );
    
    public function isValid($value, $context = null) 
    {
        if ($value == $context['Password']) {
            return true;
        }
        
        $this->_error(self::NOT_MATCH);
        return false;
    }
}
