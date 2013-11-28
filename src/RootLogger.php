<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *	   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apache\Log4php;

/**
 * The root logger.
 * @see Logger
 */
class RootLogger extends Logger
{
	/**
	 * Constructor
	 *
	 * @param integer $level initial log level
	 */
	public function __construct(Level $level = null) {
		parent::__construct('root');

		if($level == null) {
			$level = Level::getLevelAll();
		}
		$this->setLevel($level);
	}

	/**
	 * @return Level the level
	 */
	public function getEffectiveLevel() {
		return $this->getLevel();
	}

	/**
	 * Override level setter to prevent setting the root logger's level to
	 * null. Root logger must always have a level.
	 *
	 * @param Level $level
	 */
	public function setLevel(Level $level = null) {
		if (isset($level)) {
			parent::setLevel($level);
		} else {
			trigger_error("log4php: Cannot set RootLogger level to null.", E_USER_WARNING);
		}
	}

	/**
	 * Override parent setter. Root logger cannot have a parent.
	 * @param Logger $parent
	 */
	public function setParent(Logger $parent) {
		trigger_error("log4php: RootLogger cannot have a parent.", E_USER_WARNING);
	}
}
