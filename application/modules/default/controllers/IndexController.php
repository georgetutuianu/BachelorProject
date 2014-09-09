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
        require 'WindowsAzure\WindowsAzure.php';
        $connectionString = 'DefaultEndpointsProtocol=https;AccountName=licenta;AccountKey=kIldSIWX1maxG1xj+yw+SgBk+9DN6/oexbu+PwiwINIX6eySp4GMVXPrYDSDWon2mAdluWEThF/rmvMwKKuA4g==';
        $blobRestProxy = WindowsAzure\Common\ServicesBuilder::getInstance()->createBlobService($connectionString);
        
        $createContainerOptions = new WindowsAzure\Blob\Models\CreateContainerOptions(); 
        $createContainerOptions->setPublicAccess(
            WindowsAzure\Blob\Models\PublicAccessType::CONTAINER_AND_BLOBS
        );
        
//        $createContainerOptions->addMetaData("id", "value");
//        $createContainerOptions->addMetaData("key2", "value2");
        
        try {
            // Create container.
//            $blobRestProxy->createContainer("audios", $createContainerOptions);
        
            
            $blobName = 'testblob';
            $filename = 'C:\Users\George\SkyDrive\Proiecte\Licenta\Aplicatie\data\downloads\new.txt';
            $fileContent = file_get_contents($filename);
            
            $blobRestProxy->createBlockBlob('audios', $blobName, $fileContent);
        }
        catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here: 
            // http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
        
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