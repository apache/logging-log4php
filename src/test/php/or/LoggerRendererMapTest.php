<?php

require_once dirname(__FILE__).'/../phpunit.php';

require_once LOG4PHP_DIR.'/or/LoggerRendererMap.php';
require_once LOG4PHP_DIR.'/LoggerHierarchy.php';

class LoggerRendererMapTest extends PHPUnit_Framework_TestCase {
        
        protected function setUp() {
        }
        
        protected function tearDown() {
        }
        
        public function testAddRenderer() {
                
                $hierarchy = LoggerHierarchy::singleton();
                
                //print_r($hierarchy);
                
                LoggerRendererMap::addRenderer($hierarchy, 'string', 'LoggerDefaultRenderer');
                
                //print_r($hierarchy);
                
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testFindAndRender() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testGetByObject() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testGetByClassName() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testGetDefaultRenderer() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testClear() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testPut() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testRendererExists() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }

}
?>
