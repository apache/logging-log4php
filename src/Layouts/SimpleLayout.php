<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apache\Log4php\Layouts;

use Apache\Log4php\LoggingEvent;

/**
 * A simple layout.
 *
 * Returns the log statement in a format consisting of the
 * **level**, followed by " - " and then the **message**.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
class SimpleLayout extends AbstractLayout
{
    /**
     * Returns the log statement in a format consisting of the
     * **level**, followed by " - " and then the
     * **message**. For example,
     * <samp> INFO - "A message" </samp>
     *
     * @param  LoggingEvent $event
     * @return string
     */
    public function format(LoggingEvent $event)
    {
        $level = $event->getLevel();
        $message = $event->getRenderedMessage();

        return "$level - $message" . PHP_EOL;
    }
}
