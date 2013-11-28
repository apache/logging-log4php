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

namespace Apache\Log4php\Appenders;

use Apache\Log4php\LoggingEvent;

/**
 * A NullAppender merely exists, it never outputs a message to any device.
 *
 * This appender has no configurable parameters.
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/null.html Appender documentation
 */
class NullAppender extends AbstractAppender
{
    /**
     * This appender does not require a layout.
     */
    protected $requiresLayout = false;

    /**
     * Do nothing.
     *
     * @param LoggingEvent $event
     */
    public function append(LoggingEvent $event)
    {
    }
}
