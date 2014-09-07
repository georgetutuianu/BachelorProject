<?php
/**
 * The CliInitializer turns a default Zend Framework environment into a
 * command-line-ready (CLI) one. This is ideal for cron jobs or processor-intensive
 * tasks because the entry point is outside the application's public folder.
 *
 * NOTE: Please make sure that all actions/controllers that need to be called
 * only from the command line check that the request is of type
 * Zend_Controller_Request_Simple.
 */
class Utility_Controller_Plugin_CliInitializer extends Zend_Controller_Plugin_Abstract
{
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		try {
			$opts = new Zend_Console_Getopt(
				array(
					'controller|c=s' => 'Name of the controller to open',
					'action|a=s' => 'The command line action to execute',
					'cityId|ci=i' => 'City id to get trend data for',
					'startDate|sd=s' => 'Start date for the price trends',
					'endDate|ed=s' => 'End date for the price trends',
					'hours|h=i' => 'How many hours to simulate for'
				)
			);

			$opts->parse();
			$args = $opts->getRemainingArgs();

			if (!isset($opts->action)) {
				throw new Zend_Console_Getopt_Exception('Action parameter missing');
			}
			$cliAction     = $opts->action;
			$cliController = $opts->controller;
			$paramters     = array();
			$optArray      = $opts->toArray();

			for ($i = 0; $i < count($optArray); $i += 2) {
				$paramters[$optArray[$i]] = $optArray[$i + 1];
			}
		} catch (Zend_Console_Getopt_Exception $e) {
			echo $opts->getUsageMessage();
			exit;
		}
        
		// set the request as a CLI request
		$request = new Zend_Controller_Request_Simple($cliAction, $cliController, 'cli');
        
		foreach ($paramters as $key => $paramVal) {
			$request->setParam($key, $paramVal);
		}

		foreach ($args as $argument) {
			$request->setParam($argument, true);
		}

		$response = new Zend_Controller_Response_Cli();
		$front = Zend_Controller_Front::getInstance();
		$front->setRequest($request)->setResponse($response);
	}
}