<?php

require_once dirname(__FILE__).'/../phpunit.php';

require_once LOG4PHP_DIR.'/or/LoggerDefaultRenderer.php';

class DefaultRendererMockObject {
        private $a;
        protected $b;
        public $c;
}

class LoggerDefaultRendererTest extends PHPUnit_Framework_TestCase {
        
        protected function setUp() {
        }
        
        protected function tearDown() {
        }
        
        public function testDoRender() {
                $class = new DefaultRendererMockObject();
                $renderer = new LoggerDefaultRenderer();
                self::assertEquals(var_export($class, true), $renderer->doRender($class));
        }

}
?>
