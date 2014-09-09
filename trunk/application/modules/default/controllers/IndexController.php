<?php
/**
 * Main controller
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @copyright (c) 2013, George Tutuianu
 */
class IndexController extends Utility_Controller_AbstractController
{
    public function azureAction()
    {
        set_time_limit(0);
        
        $multimediaService = new Service_Multimedia();
        
        $cronId = md5(time());
        $cronId = 'ee54e0dad89dfa5d8816a48e9e0d9cca';
        $multimediaService->collectGarbage($cronId);
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    /**
     * Index action
     */
    public function indexAction()
    {
        $multimediaService = new Service_Multimedia();
        $downloadList = $multimediaService->getLatestDownloadedVideos();
        
        $this->view->downloadLinkList = $downloadList;
    }
    
    public function downloadRequestAction()
    {
        $this->_checkLoggedUser();
        
        $downloadData = $this->getAllParams();
        $retryRequest = $this->_getParam('entryId');
        
        $downloadForm = new Form_DownloadForm();
        if ($this->getRequest()->isPost() && $downloadForm->isValid($downloadData)) {
            $multimediaService = new Service_Multimedia();
            
            $downloadLink = $downloadData['DownloadLink'];
            try {
                $multimediaService->addDownloadRequest($downloadLink, $retryRequest);
                
                $this->_addFlashMessage(
                    'The download request have been successfully saved!',
                    BundlePhu_View_Helper_DisplayFlashMessages::SUCCESS_MESSAGE
                );
                $this->_redirect('/default/index/download-list');
            } catch (Exception $exception) {
                $this->_addFlashMessage(
                    'The download request has failed. Please try again!',
                    BundlePhu_View_Helper_DisplayFlashMessages::ERROR_MESSAGE
                );
            }
        }
        if ($retryRequest) {
            $this->_addFlashMessage(
                'The download request has failed. Please try again!',
                BundlePhu_View_Helper_DisplayFlashMessages::ERROR_MESSAGE
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