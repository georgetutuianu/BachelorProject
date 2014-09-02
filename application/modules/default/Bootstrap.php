<?php
/**
 * Default bootstrap
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @copyright (c) 2014, George Tutuianu
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Initialize autoloader
     *
     * @return \Zend_Loader_Autoloader
     */
    protected function _initAutoloader()
    {
        $webAutoloader = new Zend_Application_Module_Autoloader(
            array('namespace' => '', 'basePath'  => dirname(__FILE__))
        );

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);
        $autoloader->pushAutoloader($webAutoloader);

        return $autoloader;
    }

    /**
     * Initialize router
     *
     * @return \Zend_Controller_Router_Rewrite
     */
    protected function _initRoutes()
    {
        $config = new Zend_Config_Json(
            APPLICATION_PATH . '/configs/routes/en.json',
            'routes',
            array('ignore_constants' => true, 'skip_extends' => true)
        );

        $router = new Zend_Controller_Router_Rewrite();
        $router->addConfig($config);

        $front = Zend_Controller_Front::getInstance();
        $front->setRouter($router);

        return $router;
    }

    /**
     * Error logger
     *
     * @return \Zend_Log
     */
    protected function _initErrorLogger()
    {
        $errorLogPath = realpath(
            sprintf("%s/../data/logs/application-errors.log", APPLICATION_PATH)
        );

        $logger = $this->_registerLogger($errorLogPath);
        Zend_Registry::set('errorLog', $logger);

        return $logger;
    }

    /**
     * Log errors that happens when the cron is running (ex: if the structure of a page has 
     * modified, the cron will fail, and will have to log the error)
     * 
     * @return \Zend_Log
     */
    protected function _initCronsErrorLogger()
    {
        $errorLogPath = realpath(
            sprintf("%s/../data/logs/cron-errors.log", APPLICATION_PATH)
        );
        
        $logger = $this->_registerLogger($errorLogPath);
        Zend_Registry::set('cronsErrorLog', $logger);

        return $logger;
    }
    
    /**
     * Error handler for PHP-generated errors
     */
    protected function _initErrorHandler()
    {
        require_once realpath(
            sprintf("%s/../library/Utility/Controller/ErrorController.php", APPLICATION_PATH)
        );

        set_error_handler(
            array('Utility_Controller_ErrorController', 'phpErrorsAction')
        );
    }

    /**
     * Set internal encoding for application use
     */
    protected function _initInternalEncoding()
    {
        mb_internal_encoding('UTF-8');
    }

    /**
     * Initialize the view and the bundle script
     *
     * @return \Zend_View
     */
    protected function _initBundle()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $view->getHelper('BundleScript')
             ->setCacheDir(APPLICATION_PATH . '/../public/cache/js')
             ->setDocRoot(APPLICATION_PATH . '/../public')
             ->setUrlPrefix('cache/js');

        $view->getHelper('BundleLink')
             ->setCacheDir(APPLICATION_PATH . '/../public/cache/css')
             ->setDocRoot(APPLICATION_PATH . '/../public')
             ->setUrlPrefix('cache/css');

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->setView($view);
        return $view;
    }

    /**
     * Initialize project configs and store them in registry
     *
     * @throws Zend_Application_Exception
     * @return \Zend_Config_Ini The config element
     */
    protected function _initConfigs()
    {
        $configFilePath = sprintf(
            "%s/configs/config.ini",
            APPLICATION_PATH
        );

        $projectConfigs = new Zend_Config_Ini(
            $configFilePath,
            APPLICATION_ENV
        );

        Zend_Registry::set('configs', $projectConfigs);

        return $projectConfigs;
    }
    
    /**
     * Register a logger for the given filepath
     * 
     * @param string $filePath Path to the file that logs the request
     * @return \Zend_Log
     */
    private function _registerLogger($filePath)
    {
        $writer = new Zend_Log_Writer_Stream($filePath);

        $logger = new Zend_Log();
        $logger->addWriter($writer);

        return $logger;
    }
}

