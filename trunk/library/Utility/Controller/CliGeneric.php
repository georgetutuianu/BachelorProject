<?php
/**
 * This is not a controller per-se, it is just a base class that provides
 * basic functionality and performs initialization routins for CLI module's controllers.
 *
 * @author     George Tutuianu
 */
class Utility_Controller_CliGeneric extends Zend_Controller_Action
{
	/**
	 * @var stdClass
	 */
	protected $_config = null;

	/**
	 * Controller init
	 */
	public function init()
	{
		parent::init();

		if (!$this->_request instanceof Zend_Controller_Request_Simple) {
			throw new Exception('CLI controlles can be called only from the command line');
		}

		$this->_config   = Zend_Registry::get('configs');
		$this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
	}
}