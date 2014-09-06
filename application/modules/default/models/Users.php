<?php
/**
 * Description of Users
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class Model_Users 
{
    const TABLE_NAME = 'users';
    
    private $_dbAdapter = null;
    
    public function __construct()
    {
        $this->_dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
    }
    
    public function addUser($email, $password)
    {
        return $this->_dbAdapter->insert(
            self::TABLE_NAME, array(
                'email' => $email,
                'password' => md5($password)
            )
        );
    }

    public function login($userEmail, $userPassword)
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable();
        $authAdapter->setTableName(self::TABLE_NAME)
                    ->setIdentityColumn('email')
                    ->setIdentity($userEmail)
                    ->setCredentialColumn('password')
                    ->setCredential(md5($userPassword));
        
        $authController = Zend_Auth::getInstance();
        $auth = $authController->authenticate($authAdapter);
        
        if ($auth->isValid()) {
            $authController->getStorage()->write(array('email' => $userEmail));
            Zend_Session::regenerateId();
            return true;
        }
        
        return false;
    }
    
    public function signoutUser()
    {
        $authController = Zend_Auth::getInstance();
        $authController->clearIdentity();
    }
}
