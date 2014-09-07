<?php

/**
 * The error controller manages all exceptions thrown by the application that were not caught
 * at the controller level or below.
 *
 * @author    Lucian Daia
 * @copyright 2010, Zitec Com
 * @package   HotelTrends
 */
class Cli_ErrorController extends Utility_Controller_CliGeneric
{
    /**
     * errorAction() is the action that will be called by the "ErrorHandler"
     * plugin. 
     *
     * @return void
     */
	public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

		try {
			throw $errors->exception;
		} catch(Exception $e) {
			
		}
    }

	/**
	 * This action will only be called when a PHP error is triggered (even notices / warnings / etc.).
	 * Its main responsability is to log all non-exception errors in the application log file.
	 */
	public static function phpErrorsAction($errno, $errstr, $errfile, $errline, $errcontext)
	{
		

		// skip the execution of the default php error handler
		return true;
	}
}
