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
    
    public function downloadAction()
    {
        $this->_checkLoggedUser();
        
        $downloadForm = new Form_DownloadForm();
        
        $this->view->downloadForm = $downloadForm;
    }
    
    public function downloadRequestAction()
    {
        $this->_checkLoggedUser();
        
        $downloadData = $this->getAllParams();
        
        $downloadForm = new Form_DownloadForm();
        if ($downloadForm->isValid($downloadData)) {
            $multimediaService = new Service_Multimedia();
            
            $downloadLink = $downloadData['DownloadLink'];
            $multimediaService->addDownloadRequest($downloadLink);
            $this->_redirect('/default/index/download-list');
        }
        
        $this->_redirect('/default/index/download');
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
    }
    
    public function downloadListAction()
    {
        $this->_checkLoggedUser();
        
        $multimediaService = new Service_Multimedia();
        $downloadList = $multimediaService->getDownloadListDetails();
        
        $this->view->downloadLinkList = $downloadList;
    }
}