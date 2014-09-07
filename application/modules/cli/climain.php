<?php
// Define path to application directory
$applicationDir = realpath(dirname(__FILE__) . '/../../');
defined('APPLICATION_PATH') || define('APPLICATION_PATH', $applicationDir);

// Ensure library/ is on include_path
$libraryDir = realpath(APPLICATION_PATH . '/../library');
$newIncludePath = implode(
    PATH_SEPARATOR, array($libraryDir, get_include_path())
);
set_include_path($newIncludePath);

define('PUBLIC_PATH', realpath(APPLICATION_PATH . '/../public/'));

// Define application environment
define('APPLICATION_ENV', 'cli');

/** Zend_Application */
require_once 'Zend/Application.php';

try {
    // Create application, bootstrap, and run
    $application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini');
    
    $application->bootstrap()->run();
} catch (Exception $e) {
    // this section is used only when something a critical error occurs in the
    // application startup process
    $logMessage = sprintf(
        "%s : %s %s%s %s",
        'CLI-CALL', $e->getMessage(), PHP_EOL, $e->getTraceAsString(), PHP_EOL
    );echo $logMessage;
    trigger_error($logMessage, E_USER_ERROR);
}