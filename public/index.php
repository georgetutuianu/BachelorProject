<?php
// Define path to application directory
$applicationDir = realpath(dirname(__FILE__) . '/../application');
defined('APPLICATION_PATH') || define('APPLICATION_PATH', $applicationDir);

// Define application environment
$applicationEnv = (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', $applicationEnv);

// Ensure library/ is on include_path
$libraryDir = realpath(APPLICATION_PATH . '/../library');
$newIncludePath = implode(
    PATH_SEPARATOR, array($libraryDir, get_include_path())
);
set_include_path($newIncludePath);

/** Zend_Application */
require_once 'Zend/Application.php';

// path to application initializer (application.ini or application.json)
$applicationIniPath = sprintf("%s/configs/application.ini", APPLICATION_PATH);
// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, $applicationIniPath);

try {
    $application->bootstrap()->run();
} catch (Exception $exception) {
    $logMessage = sprintf(
        "%s : %s %s %s %s",
        filter_input(INPUT_SERVER, 'REMOTE_ADDR'),
        $exception->getMessage(),
        PHP_EOL,
        $exception->getTraceAsString(),
        PHP_EOL
    );

    trigger_error($logMessage, E_USER_ERROR);
}
