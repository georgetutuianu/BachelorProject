<?php
/**
 * Handles error logging
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @copyright (c) 2013, George Tutuianu
 */
class Utility_Service_Log
{
    const EMERG  = 0;  // Emergency: system is unusable
    const ALERT  = 1;  // Alert: action must be taken immediately
    const CRIT   = 2;  // Critical: critical conditions
    const ERR    = 3;  // Error: error conditions
    const WARN   = 4;  // Warning: warning conditions
    const NOTICE = 5;  // Notice: normal but significant condition
    const INFO   = 6;  // Informational: informational messages
    const DEBUG  = 7;  // Debug: debug messages

    /**
     * @var Zend_Log
     */
    private static $_logger = null;

    /**
     * Logs a message to the error log along with its trace.
     * The backtrace is automatically retrieved, even if the entry is a string, so NO backtrace
     * information should be provided in the message. However, if you need to save other context
     * data, as the URL, HTTP headers, or ENV variables, these should be part of the message.
     *
     * @param mixed $entry Can be of type string, ErrorException or Exception
     * @param int   $priority One of the Cli_Service_Log class constants.
     * @param bool  $addTrace Whether to add a trace to log message.
     */
    public static function log($entry, $priority = self::ERR, $addTrace = true)
    {
        if (!self::$_logger) {
            self::$_logger = Zend_Registry::get('errorLog');
        }

        $logData = array(
            'message' => '',
            'priority' => $priority
        );

        if ($entry instanceof ErrorException) {
            $severity = $entry->getSeverity();
            switch ($severity) {
                case E_NOTICE:
                    $logData['priority'] = self::NOTICE;
                    break;
                case E_WARNING:
                    $logData['priority'] = self::WARN;
                    break;
                case E_ERROR:
                default:
                    $logData['priority'] = self::ERR;
                    break;
            }

            if ($addTrace) {
                $logData['message'] = '';
                $traceString = str_replace("#", "\t#", $entry->getTraceAsString());
                $logData['message'] .= $entry->getMessage() . PHP_EOL . $traceString . PHP_EOL;
            } else {
                $logData['message'] = $entry->getMessage();
            }
        } elseif ($entry instanceof Exception) {
            // add a tab beofre each new line of the trace string
            $logData['priority'] = $entry->getCode();
            if ($addTrace) {
                $logData['message'] = '';
                $traceString = str_replace("#", "\t#", $entry->getTraceAsString());
                $logData['message'] .= $entry->getMessage() . PHP_EOL . $traceString . PHP_EOL;
            } else {
                $logData['message'] = $entry->getMessage();
            }
        } elseif (is_string($entry)) {
            if ($addTrace) {
                $rawBacktrace = debug_backtrace();
                $formattedBacktrace = self::_getFormattedBacktrace($rawBacktrace);
                $logData['message'] = $entry . PHP_EOL . $formattedBacktrace;
            } else {
                $logData['message'] = $entry;
            }
        } else {
            throw new BadMethodCallException(
                'Logging service called with unknown entry type: ' . gettype($entry)
            );
        }

        if (($logData['priority'] >= self::EMERG) && ($logData['priority'] <= self::DEBUG)) {
            self::$_logger->log($logData['message'], $logData['priority']);
        } else {
            self::$_logger->err($logData['message']);
        }

        if (ini_get('display_errors')) {
            echo $logData['message'] . PHP_EOL;
        }
    }

    /**
     * Turns a raw PHP backtrace into a multi-line string with this format for each line:
     * \t#<StackLevel> <filePath>(<lineNumber): <functionName>(<args>)\n
     *
     * @param array $rawBacktrace
     * @return string
     */
    private static function _getFormattedBacktrace($rawBacktraceStack)
    {
        $backtraceAsString = '';

        foreach ($rawBacktraceStack as $stackLevel => $backtraceEntry) {
            $backtraceLine = sprintf(
                "\t#%d %s(%d): %s%s%s(%s)%s",
                $stackLevel,
                $backtraceEntry['file'],
                $backtraceEntry['line'],
                $backtraceEntry['class'],
                $backtraceEntry['type'],
                $backtraceEntry['function'],
                self::_getFormattedBacktraceArgs($backtraceEntry['args']),
                PHP_EOL
            );
            $backtraceAsString .= $backtraceLine;
        }

        return $backtraceAsString;
    }

    /**
     * Given an array of arguments provided by a raw PHP backtrace, it returns their string
     * representation.
     *
     * @param array $args
     * @return string
     */
    private static function _getFormattedBacktraceArgs($args)
    {
        $calledFunctionArgs = array();

        foreach ($args as $arg) {
            if (is_object($arg)) {
                $calledFunctionArgs[] = get_class($arg);
            } else {
                $calledFunctionArgs[] = (string) $arg;
            }
        }

        return implode(', ', $calledFunctionArgs);
    }

}
