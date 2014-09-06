<?php
/**
 * Description of DownloadForm
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class Form_DownloadForm extends Zend_Form 
{
    public function init()
    {
        $this->setAction('/default/index/download-request');
        $this->setMethod(Zend_Http_Client::POST);
        
        $downloadLinkElement = $this->_getDownloadLinkElement();
        $submitFormElement = $this->_getSubmitFormElement();
        
        $this->addElements(array($downloadLinkElement, $submitFormElement));
    }
    
    private function _getDownloadLinkElement()
    {
        $downloadLinkElement = new Zend_Form_Element_Text('DownloadLink');
        $downloadLinkElement->setRequired()
                            ->setLabel('Download link');
        
        return $downloadLinkElement;
    }
    
    private function _getSubmitFormElement()
    {
        $submitElement = new Zend_Form_Element_Submit('Download');
        
        return $submitElement;
    }
}
