<?php
/**
 * Abstract controller. This controller has been created to implement the
 * common functionality for all the controllers
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @copyright (c) 2014, George Tutuianu
 */
class Utility_Controller_AbstractController extends Zend_Controller_Action
{
    /**
     * Title of the current page
     *
     * @var string
     */
    private $_pageTitle = null;

    protected $_flashMessenger = null;
    
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }
    
    /**
     * Post dispatch routine. Executes after the action has been finished
     */
    public function postDispatch()
    {
        $this->_setPageTitleForView();
        $this->_setApplicationNameForView();
        
        $authController = Zend_Auth::getInstance();
        $user = $authController->getIdentity();
        $this->view->userEmail = $user['email'];
        
        $flashMessageData = $this->_flashMessenger->getMessages();
        if (!empty($flashMessageData)) {
            list($flashMessage, $flashMessageType) = $flashMessageData;
            $this->_helper->layout()->flashMessage = $flashMessage;
            $this->_helper->layout()->flashMessageType = $flashMessageType;
        }
        
        $this->view->bundleLink()
                   ->appendStylesheet('/css/libraries/bootstrap.min.css')
                   ->appendStylesheet('/css/custom/layout.css');
    }
    
    protected function _addFlashMessage($message, $type)
    {
        $this->_flashMessenger->addMessage($message);
        $this->_flashMessenger->addMessage($type);
    }
    
    protected function _checkLoggedUser()
    {
        $authController = Zend_Auth::getInstance();
        $userDetails = $authController->getIdentity();
        
        if (!array_key_exists('email', $userDetails)) {
            $this->_redirect('/');
        }
    }
    
    /**
     * Get the name of the application
     *
     * @return string | null
     */
    protected function _getApplicationName()
    {
        $configs = Zend_Registry::get('configs');

        $applicationName   = $configs->application->name;
        $translatedAppName = $this->view->translate($applicationName);

        return $translatedAppName;
    }

    /**
     * Get the name of the page if is set
     *
     * @return string | null
     */
    protected function _getPageTitle()
    {
        return $this->_pageTitle;
    }

    protected function _setApplicationNameForView()
    {
        $configs = Zend_Registry::get('configs');

        $applicationName = $configs->application->name;
        $this->view->applicationName = $applicationName;
    }
    
    /**
     * Set the name for the current page
     *
     * @param string $title Title for the page
     */
    protected function _setPageTitle($title)
    {
        $this->_pageTitle = $this->view->translate($title);
    }

    /**
     * Compose page title and assign it to view
     */
    private function _setPageTitleForView()
    {
        $pageTitle       = $this->_getPageTitle();
        $applicationName = $this->_getApplicationName();

        if ($pageTitle && $applicationName) {
            $title = sprintf("%s - %s", $pageTitle, $applicationName);
        } elseif ($pageTitle && !$applicationName) {
            $title = $pageTitle;
        } else {
            $title = $applicationName;
        }

        $this->view->pageTitle = $title;
    }
}
