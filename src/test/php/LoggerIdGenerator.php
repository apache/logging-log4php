<?php
/**
 * That's a singletone class
 */

class LoggerIdGenerator
{
	private static $instance;
	private $requestId;
	private $sequence = 1;

	protected function __construct() {/* you can't create me */
	}

	public function getId() {
		if($this->requestId == null) {
			$dateTime = new DateTime();
			$this->requestId = $dateTime->getTimestamp() . rand(1000, 9999);
		}
		return $this->requestId;
	}

	public function getSeq() {
		return $this->sequence++;
	}

	public static function me() {
		return self::getInstance();
	}

	final public static function getInstance() {

		if(!isset(self::$instance)) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	final private function __clone() {/* do not clone me */
	}
}
