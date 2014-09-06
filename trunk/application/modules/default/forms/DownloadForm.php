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
        
        $this->setDecorators(
            array(
                array(
                    'ViewScript',
                    array(
                        'viewScript' => '/forms/Decorators/downloadForm.phtml',
                        'class' => 'form'
                    )
                )
            )
        );
    }
    
    private function _getDownloadLinkElement()
    {
        $downloadLinkValidator = new Form_Validate_YoutubeLink();
        
        $downloadLinkElement = new Zend_Form_Element_Text('DownloadLink');
        $downloadLinkElement->setRequired()
                            ->setLabel('Youtube link')
                            ->addValidator($downloadLinkValidator);
        
        return $downloadLinkElement;
    }
    
    private function _getSubmitFormElement()
    {
        $submitElement = new Zend_Form_Element_Submit('Download');
        
        return $submitElement;
    }
}
