<?php
/**
 * Cli (command line interface) module (application) bootstrap
 *
 * @author    George Tutuianu
 */
class CliBootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Bootstrap autoloader for application resources
	 *
	 * @return Zend_Application_Module_Autoloader
	 */
	protected function _initAutoload()
	{
		$webAutoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath'  => realpath(dirname(__FILE__) . '/../default')));
		$cliAutoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Cli',
			'basePath'  => dirname(__FILE__)));
		
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->setFallbackAutoloader(true);
		$autoloader->pushAutoloader($webAutoloader);
		$autoloader->pushAutoloader($cliAutoloader);

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
	 * Initialize router
	 *
	 */
	protected function _initRoutes()
	{
		if (APPLICATION_ENV == 'cli') {
			$front = Zend_Controller_Front::getInstance();
			$front->setRouter(new Utility_Controller_Router());
		}
	}

	/**
	 * Bootstrap plugins
	 *
	 */
	protected function _initPlugins()
	{
		// Ensure the front controller is initialized
		$this->bootstrap('FrontController');

		$front = $this->getResource('FrontController');
		$front->throwExceptions(false);

		$front->registerPlugin(new Utility_Controller_Plugin_CliInitializer());
		$front->registerPlugin(new Utility_Controller_Plugin_ErrorHandler(array('module' => 'cli')));
	}

	/**
     * Error handler for PHP-generated errors
     */
//    protected function _initErrorHandler()
//    {
//        require_once realpath(
//            sprintf("%s/../library/Utility/Controller/ErrorController.php", APPLICATION_PATH)
//        );
//
//        set_error_handler(
//            array('Utility_Controller_ErrorController', 'phpErrorsAction')
//        );
//    }
}
