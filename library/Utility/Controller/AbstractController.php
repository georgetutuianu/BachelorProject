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

    /**
     * Post dispatch routine. Executes after the action has been finished
     */
    public function postDispatch()
    {
        $this->_setPageTitleForView();
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
