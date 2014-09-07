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
        
        if ($retryRequest) {
            return $this->_dbAdapter->update(
                self::TABLE_NAME, 
                array(
                    'download_status' => '0'
                ), 
                $this->_dbAdapter->quoteInto('id = ?', $retryRequest)
            );
        }
        
        return $this->_dbAdapter->insert(
            self::TABLE_NAME, array(
                'link' => $downloadLink,
                'video_name' => $videoName,
                'user_email' => $userDetails['email']
            )
        );
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
