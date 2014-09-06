<?php
/**
 * Description of DisplayFlashMessages
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class BundlePhu_View_Helper_DisplayFlashMessages extends Zend_View_Helper_Abstract
{
    const SUCCESS_MESSAGE = 0;
    const ERROR_MESSAGE = 1;
    
    public function displayFlashMessages($message, $messageType = self::SUCCESS_MESSAGE)
    {
        if (is_null($message) || is_null($messageType)) {
            return null;
        }
        
        $returnHtml = '<div class="flash-message">';
        switch ($messageType) {
            case self::ERROR_MESSAGE : 
                $returnHtml .= sprintf('<div class="alert alert-danger" role="alert">%s</div>', $message);
                break;
            case self::SUCCESS_MESSAGE :
                $returnHtml .= sprintf('<div class="alert alert-success" role="alert">%s</div>', $message);
                break;
        }
        $returnHtml .= '</div>';
        
        return $returnHtml;
    }
}
