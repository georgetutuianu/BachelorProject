<?php
/**
 * Description of ErrorController
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @copyright (c) 2013, George Tutuianu
 */
class Utility_Controller_ErrorController
{
    /**
     * Application error handler
     * 
     * @param  int     $errno
     * @param  string  $errstr
     * @param  mixed   $errfile
     * @param  int     $errline
     * @return boolean
     */
    public static function phpErrorsAction($errno, $errstr, $errfile, $errline)
    {
        Utility_Service_Log::log(new ErrorException($errstr, $errno, 0, $errfile, $errline));

        // skip the execution of the default php error handler
        return true;
    }
}
