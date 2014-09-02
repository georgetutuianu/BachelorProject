<?php
/**
 * Model that implements main functionality of db table connections
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
abstract class Utility_Model_TableModel 
{
    /**
     * Adapter for using db
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_dbAdapter = null;
    
    /**
     * Initialization of the model
     */
    public function __construct() 
    {
        // store the adapter for db connection
        $this->_dbAdapter = Zend_Db_Table::getDefaultAdapter();
    }
}
