<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.	See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *		http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   tests
 * @package    log4php
 * @subpackage filters
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link	   http://logging.apache.org/log4php
 */

/**
 * @group filters
 */
class LoggerFilterNameMatchTest extends PHPUnit_Framework_TestCase {

	public function testCaseSensitivity() {
		$filter = new LoggerFilterNameMatch();
		$filter->setAcceptOnMatch("true");
		$filter->setStringToMatch("AcCePtEd");

		$eventFromAccepted = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('AcCePtEd'), LoggerLevel::getLevelInfo(), "Irrelevant");
		$eventFromAccepted2 = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Accepted'), LoggerLevel::getLevelInfo(), "Irrelevant");
		$eventFromElsewhere = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Elsewhere'), LoggerLevel::getLevelInfo(), "Irrelevant");


		// Events are case-sensitive by default.
		$this->assertEquals($filter->decide($eventFromAccepted), LoggerFilter::ACCEPT);
		$this->assertEquals($filter->decide($eventFromAccepted2), LoggerFilter::NEUTRAL);
		$this->assertEquals($filter->decide($eventFromElsewhere), LoggerFilter::NEUTRAL);

		// But we can make them case insensitive.
		$filter->setCaseSensitive("false");
		$this->assertEquals($filter->decide($eventFromAccepted), LoggerFilter::ACCEPT);
		$this->assertEquals($filter->decide($eventFromAccepted2), LoggerFilter::ACCEPT);
		$this->assertEquals($filter->decide($eventFromElsewhere), LoggerFilter::NEUTRAL);
	}

	public function testPartialMatch() {
		$filter = new LoggerFilterNameMatch();
		$filter->setAcceptOnMatch("true");
		$filter->setStringToMatch("Accept");

		$eventFromAccept = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Accept'), LoggerLevel::getLevelInfo(), "Irrelevant");
		$eventFromAccepted = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Accepted'), LoggerLevel::getLevelInfo(), "Irrelevant");
		$eventFromElsewhere = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Elsewhere'), LoggerLevel::getLevelInfo(), "Irrelevant");

		// Partial matches are accepted.
		$this->assertEquals($filter->decide($eventFromAccept), LoggerFilter::ACCEPT);
		$this->assertEquals($filter->decide($eventFromAccepted), LoggerFilter::ACCEPT);
		$this->assertEquals($filter->decide($eventFromElsewhere), LoggerFilter::NEUTRAL);
	}

	public function testExactMatch() {
		$filter = new LoggerFilterNameMatch();
		$filter->setAcceptOnMatch("true");
		$filter->setStringToMatch("Accept");
		$filter->setExactMatch("true");

		$eventFromAccept = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Accept'), LoggerLevel::getLevelInfo(), "Irrelevant");
		$eventFromAccepted = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Accepted'), LoggerLevel::getLevelInfo(), "Irrelevant");
		$eventFromElsewhere = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Elsewhere'), LoggerLevel::getLevelInfo(), "Irrelevant");

		// Partial matches are accepted.
		$this->assertEquals($filter->decide($eventFromAccept), LoggerFilter::ACCEPT);
		$this->assertEquals($filter->decide($eventFromAccepted), LoggerFilter::NEUTRAL);
		$this->assertEquals($filter->decide($eventFromElsewhere), LoggerFilter::NEUTRAL);
	}

	public function testAcceptOnMatchTrue() {
		$filter = new LoggerFilterNameMatch();
		$filter->setAcceptOnMatch("true");
		$filter->setStringToMatch("Accept");

		$eventFromAccept = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Accept'), LoggerLevel::getLevelInfo(), "Irrelevant");
		$eventFromNeutral = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Neutral'), LoggerLevel::getLevelInfo(), "Irrelevant");

		$this->assertEquals($filter->decide($eventFromAccept), LoggerFilter::ACCEPT);
		$this->assertEquals($filter->decide($eventFromNeutral), LoggerFilter::NEUTRAL);
	}

	public function testAcceptOnMatchFalse() {
		$filter = new LoggerFilterNameMatch();
		$filter->setAcceptOnMatch("false");
		$filter->setStringToMatch("Deny");

		$eventFromDeny = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Deny'), LoggerLevel::getLevelInfo(), "Irrelevant");
		$eventFromNeutral = new LoggerLoggingEvent("LoggerFilterNameMatchTest", new Logger('Neutral'), LoggerLevel::getLevelInfo(), "Irrelevant");

		$this->assertEquals($filter->decide($eventFromDeny), LoggerFilter::DENY);
		$this->assertEquals($filter->decide($eventFromNeutral), LoggerFilter::NEUTRAL);
	}
}
