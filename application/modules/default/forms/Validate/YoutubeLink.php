<?php
/**
 * Description of YoutubeLink
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class Form_Validate_YoutubeLink extends Zend_Validate_Abstract 
{
    CONST NOT_YOUTUBE = 'invalidLink';
    
    protected $_messageTemplates = array(
        self::NOT_YOUTUBE => 'This is not a valid youtube link'
    );
    
    public function isValid($value)
    {
        $youtubeRegex = '/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|'
                      . 'watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/';
        
        if (preg_match($youtubeRegex, $value)) {
            return true;
        }
        
        $this->_error(self::NOT_YOUTUBE);
        return false;
    }
}
