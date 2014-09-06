<?php
/**
 * Main controller
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @copyright (c) 2013, George Tutuianu
 */
class IndexController extends Utility_Controller_AbstractController
{
    /**
     * Index action
     */
    public function indexAction()
    {
        
    }
    
    public function downloadRequestAction()
    {
        $this->_checkLoggedUser();
        
        $downloadData = $this->getAllParams();
        
        $downloadForm = new Form_DownloadForm();
        if ($this->getRequest()->isPost() && $downloadForm->isValid($downloadData)) {
            $multimediaService = new Service_Multimedia();
            
            $downloadLink = $downloadData['DownloadLink'];
            $multimediaService->addDownloadRequest($downloadLink);
            $this->_addFlashMessage(
                'The download request have been successfully saved!',
                BundlePhu_View_Helper_DisplayFlashMessages::SUCCESS_MESSAGE
            );
            $this->_redirect('/default/index/download-list');
        }
        
        $this->view->downloadForm = $downloadForm;
    }
    
    public function downloadListAction()
    {
        $this->_checkLoggedUser();
        
        $multimediaService = new Service_Multimedia();
        $downloadList = $multimediaService->getDownloadListDetails();
        
        $this->view->downloadLinkList = $downloadList;
    }
}