<?php
/**
 * Description of EmailNotTaken
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class Form_Validate_EmailNotTaken extends Zend_Validate_Abstract
{
    const MAIL_TAKEN = 'mailTaken';
    
    protected $_messageTemplates = array(
        self::MAIL_TAKEN => 'This email is already used by someone else'
    );
    
    public function isValid($value)
    {
        $usersModel = new Model_Users();
        
        if ($usersModel->emailExists($value)) {
            $this->_error(self::MAIL_TAKEN);
            return false;
        }
        
        return true;
    }
}
