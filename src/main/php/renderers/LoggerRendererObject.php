<?php

/**
 * 
 * @category   tests   
 * @package    log4php
 * @subpackage renderers
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 *
 */
class LoggerRendererObject implements LoggerRenderer {
	/* (non-PHPdoc)
	 * @see LoggerRenderer::render()
	 */
	public function render($o) {
		return print_r($o, true);
	}
}

?>