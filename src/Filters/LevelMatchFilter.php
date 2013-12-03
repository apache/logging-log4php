<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apache\Log4php\Filters;

use Apache\Log4php\LoggingEvent;

/**
 * This is a very simple filter based on level matching.
 *
 * The filter admits two options <var>LevelToMatch</var> and
 * <var>AcceptOnMatch</var>. If there is an exact match between the value of the
 * <var>LevelToMatch</var> option and the level of the {@link LoggingEvent},
 * then the {@link decide()} method returns {@link AbstractFilter::ACCEPT} in
 * case the <var>AcceptOnMatch</var> option value is set to *true*, if it is
 * *false* then {@link AbstractFilter::DENY} is returned. If there is no match,
 * {@link AbstractFilter::NEUTRAL} is returned.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @since 0.6
 */
class LevelMatchFilter extends AbstractFilter
{
    /**
     * Indicates if this event should be accepted or denied on match
     * @var boolean
     */
    protected $acceptOnMatch = true;

    /**
     * The level, when to match
     * @var Level
     */
    protected $levelToMatch;

    /**
     * @param boolean $acceptOnMatch
     */
    public function setAcceptOnMatch($acceptOnMatch)
    {
        $this->setBoolean('acceptOnMatch', $acceptOnMatch);
    }

    /**
     * @param string $l the level to match
     */
    public function setLevelToMatch($level)
    {
        $this->setLevel('levelToMatch', $level);
    }

    /**
     * Return the decision of this filter.
     *
     * Returns {@link AbstractFilter::NEUTRAL} if the <var>LevelToMatch</var>
     * option is not set or if there is not match. Otherwise, if there is a
     * match, then the returned decision is {@link AbstractFilter::ACCEPT} if
     * the <var>AcceptOnMatch</var> property is set to *true*. The returned
     * decision is {@link AbstractFilter::DENY} if the <var>AcceptOnMatch</var>
     * property is set to *false*.
     *
     * @param LoggingEvent $event
     * @return integer
     */
    public function decide(LoggingEvent $event)
    {
        if ($this->levelToMatch === null) {
            return AbstractFilter::NEUTRAL;
        }

        if ($this->levelToMatch->equals($event->getLevel())) {
            return $this->acceptOnMatch ? AbstractFilter::ACCEPT : AbstractFilter::DENY;
        } else {
            return AbstractFilter::NEUTRAL;
        }
    }
}
