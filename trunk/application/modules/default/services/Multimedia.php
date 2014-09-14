<?php
/**
 * Description of Multimedia
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class Service_Multimedia 
{
    const DOWNLOAD_STATUS_WAITING = 0;
    const DOWNLOAD_STATUS_IN_PROGRESS = 1;
    const DOWNLOAD_STATUS_FAILED = 2;
    const DOWNLOAD_STATUS_SUCCESS = 3;
    
    const TABLE_NAME = 'download_requests';
    
    private $_dbAdapter = null;
    
    private $_youtubeAdapter = null;
    
    public function __construct()
    {
        $this->_dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $this->_youtubeAdapter = new Zend_Gdata_YouTube();
    }
    
    public function addDownloadRequest($downloadLink, $retryRequest = false)
    {
        $authController = Zend_Auth::getInstance();
        $userDetails = $authController->getIdentity();
        $videoName = $this->_getVideoName($downloadLink);
        $queueName = 'jobs';
        
        $connectionString = 'DefaultEndpointsProtocol=http;AccountName=licenta;AccountKey=kIldSIWX1maxG1xj+yw+SgBk+9DN6/oexbu+PwiwINIX6eySp4GMVXPrYDSDWon2mAdluWEThF/rmvMwKKuA4g==';
        $queueRestProxy = WindowsAzure\Common\ServicesBuilder::getInstance()->createQueueService($connectionString);
        
        if ($retryRequest) {
            // Create message.
            $queueRestProxy->createMessage($queueName, base64_encode($retryRequest));
            return $this->_dbAdapter->update(
                self::TABLE_NAME, 
                array(
                    'download_status' => '0'
                ), 
                $this->_dbAdapter->quoteInto('id = ?', $retryRequest)
            );
        }
        
        $this->_dbAdapter->insert(
            self::TABLE_NAME, array(
                'link' => $downloadLink,
                'video_name' => $videoName,
                'user_email' => $userDetails['email']
            )
        );
        $queueRestProxy->createMessage($queueName, base64_encode($this->_dbAdapter->lastInsertId()));
        
        return $this->_dbAdapter->lastInsertId();
    }
    
    public function collectGarbage($cronId)
    {
        $downloadPath = realpath(sprintf(
            '%s/../data/downloads/', APPLICATION_PATH
        ));
        
        $dirFiles = scandir($downloadPath);
        
        foreach ($dirFiles as $filePath) {
            if (strpos($filePath, $cronId) === 0) {
                unlink(realpath($downloadPath . '/' . $filePath));
            }
        }
    }
    
    public function downloadVideo($videoLink, $fileName)
    {
        require 'youtube-dl.class.php';
        
        $downloadDir = realpath(sprintf(
            '%s/../data/downloads/', APPLICATION_PATH
        ));
        $ffmpegLogsDir = realpath(sprintf(
            '%s/../data/logs/', APPLICATION_PATH
        ));
        
        $youtubeDownloader = new yt_downloader($videoLink['video_link']);
        $youtubeDownloader->set_downloads_dir($downloadDir . DIRECTORY_SEPARATOR);
        $youtubeDownloader->set_ffmpegLogs_dir($ffmpegLogsDir . DIRECTORY_SEPARATOR);
        $youtubeDownloader->set_download_thumbnail(false);
        $youtubeDownloader->set_video_title($fileName);
        
//        $youtubeDownloader->download_video(); exit;
        $youtubeDownloader->download_audio();
        $audioFileName = $youtubeDownloader->get_video_title();
       
        return sprintf('%s.mp3', $audioFileName);
    }
    
    public function getDownloadListDetails()
    {
        $authController = Zend_Auth::getInstance();
        $userDetails = $authController->getIdentity();
        
        $selectQuery = $this->_dbAdapter->select();
        $selectQuery->from(self::TABLE_NAME, array())
                    ->where('user_email = ?', $userDetails['email'])
                    ->order('added DESC')
                    ->columns(
                        array(
                            'id' => 'id',
                            'video_link' => 'link',
                            'video_name' => 'video_name',
                            'download_status' => 'download_status',
                            'download_link' => 'download_link'
                        )
                    );
        
        return $this->_dbAdapter->fetchAll($selectQuery);
    }
    
    public function getLatestDownloadedVideos()
    {
        $selectQuery = $this->_dbAdapter->select();
        $selectQuery->from(self::TABLE_NAME, array())
                    ->where("download_status = '?'", self::DOWNLOAD_STATUS_SUCCESS)
                    ->where('download_link is not null')
                    ->limit(10)
                    ->columns(
                        array(
                            'id' => 'id',
                            'video_link' => 'link',
                            'video_name' => 'video_name',
                            'download_status' => 'download_status',
                            'download_link' => 'download_link'
                        )
                    );
        
        return $this->_dbAdapter->fetchAll($selectQuery);
    }
    
    public function getVideoToConvert()
    {
        $queueName = 'jobs';
        
        $connectionString = 'DefaultEndpointsProtocol=http;AccountName=licenta;AccountKey=kIldSIWX1maxG1xj+yw+SgBk+9DN6/oexbu+PwiwINIX6eySp4GMVXPrYDSDWon2mAdluWEThF/rmvMwKKuA4g==';
        $queueRestProxy = WindowsAzure\Common\ServicesBuilder::getInstance()->createQueueService($connectionString);
        
        $listMessagesResult = $queueRestProxy->listMessages($queueName);
        $messages = $listMessagesResult->getQueueMessages();
        $message = $messages[0];
        
        $messageId = $message->getMessageId();
        $popReceipt = $message->getPopReceipt();
        
        try {
            $requestId = base64_decode($message->getMessageText());
            // Delete message.
            $queueRestProxy->deleteMessage($queueName, $messageId, $popReceipt);
        }
        catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here: 
            // http://msdn.microsoft.com/en-us/library/windowsazure/dd179446.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
        
        $selectQuery = $this->_dbAdapter->select();
        $selectQuery->from(self::TABLE_NAME, array())
                    //->where("download_status = '?'", self::DOWNLOAD_STATUS_WAITING)
                    ->columns(
                        array(
                            'id' => 'id',
                            'video_link' => 'link'
                        )
                    )
                    ->order('added DESC')
                    ->where('id = ?', $requestId)
                    ->limit(1);
        
        return $this->_dbAdapter->fetchRow($selectQuery);
    }
    
    public function markAsFailed($videoId)
    {
        return $this->_dbAdapter->update(
            self::TABLE_NAME,
            array(
                'download_status' => self::DOWNLOAD_STATUS_FAILED
            ),
            $this->_dbAdapter->quoteInto('id = ?', $videoId)
        );
    }

    public function markAsInProgress($videoId)
    {
        return $this->_dbAdapter->update(
            self::TABLE_NAME,
            array(
                'download_status' => self::DOWNLOAD_STATUS_IN_PROGRESS
            ),
            $this->_dbAdapter->quoteInto('id = ?', $videoId)
        );
    }

    public function setBlobName($fileName, $requestId)
    {
        return $this->_dbAdapter->update(
            self::TABLE_NAME, 
            array(
                'download_status' => self::DOWNLOAD_STATUS_SUCCESS,
                'download_link' => $fileName
            ),
            $this->_dbAdapter->quoteInto('id = ?', $requestId)
        );
    }
    
    public function storeBlobInAzure($filePath, $blobName)
    {
        $connectionString = 'DefaultEndpointsProtocol=http;AccountName=licenta;AccountKey=kIldSIWX1maxG1xj+yw+SgBk+9DN6/oexbu+PwiwINIX6eySp4GMVXPrYDSDWon2mAdluWEThF/rmvMwKKuA4g==';
        $blobRestProxy = WindowsAzure\Common\ServicesBuilder::getInstance()->createBlobService($connectionString);
        
        $createContainerOptions = new WindowsAzure\Blob\Models\CreateContainerOptions(); 
        $createContainerOptions->setPublicAccess(
            WindowsAzure\Blob\Models\PublicAccessType::CONTAINER_AND_BLOBS
        );
        
        try {
            $fileContent = fopen($filePath, 'r');
            
            $storingOptions = new \WindowsAzure\Blob\Models\CreateBlobOptions();
            $storingOptions->setContentType('audio/mpeg');
            
            $blobRestProxy->createBlockBlob('uploads', $blobName, $fileContent, $storingOptions);
        } catch (Exception $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
    }
    
    private function _getVideoCode($youtubeLink)
    {
        $youtubeRegex = '/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|'
                      . 'watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/';
        
        $matches = array();
        if (preg_match($youtubeRegex, $youtubeLink, $matches))
        {
            return $matches[1];
        }
    }
    
    private function _getVideoName($youtubeLink)
    {
        $videoId = $this->_getVideoCode($youtubeLink);
        $videoDetails = $this->_youtubeAdapter->getVideoEntry($videoId);
        $videoName = $videoDetails->getTitle()->__toString();
        
        return $videoName;
    }
}
