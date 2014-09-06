<?php
/**
 * Description of Multimedia
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class Service_Multimedia 
{
    const TABLE_NAME = 'download_requests';
    
    private $_dbAdapter = null;
    
    public function __construct()
    {
        $this->_dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
    }
    
    public function addDownloadRequest($downloadLink)
    {
        $authController = Zend_Auth::getInstance();
        $userDetails = $authController->getIdentity();
        
        return $this->_dbAdapter->insert(
            self::TABLE_NAME, array(
                'link' => $downloadLink,
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
                            'video_link' => 'link'
                        )
                    );
        
        return $this->_dbAdapter->fetchAll($selectQuery);
    }
}
