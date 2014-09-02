<?php
/**
 * Contoller that handles errors
 * 
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @copyright (c) 2013, George Tutuianu
 */
class ErrorController extends Utility_Controller_AbstractController
{
    /**
     * Error action
     * 
     * @return void
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = $this->view->translate('You have reached the error page');
            return;
        }
        
        $this->_handleErrors($errors);
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }
    
    private function _handleErrors($errors)
    {
        $notFoundStatus    = Utility_Object_HttpStatusCode::STATUS_CODE_NOT_FOUND;
        $serverErrorStatus = Utility_Object_HttpStatusCode::STATUS_CODE_INTERNAL_SERVER_ERROR;
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode($notFoundStatus);
                $priority = Zend_Log::NOTICE;
                $this->view->message = $this->view->translate('Page not found');
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode($serverErrorStatus);
                $priority = Zend_Log::CRIT;
                $this->view->message = $this->view->translate('Application error');
                break;
        }
        
        // Log exception, if logger available
//        if ($log = $this->getLog()) {
//            $log->log($this->view->message, $priority, $errors->exception);
//            $log->log('Request Parameters', $priority, $errors->request->getParams());
//        }
    }
}

