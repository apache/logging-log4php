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
 * This is a very simple filter based on level matching, which can be used to
 * reject messages with priorities outside a certain range.
 *
 * The filter admits three options <var>levelMin</var>, <var>levelMax</var> and
 * <var>acceptOnMatch</var>.
 *
 * If the level of the {@link LoggingEvent} is not between Min and Max
 * (inclusive), then {@link AbstractFilter::DENY} is returned.
 *
 * If the Logging event level is within the specified range, then if
 * <var>acceptOnMatch</var> is *true*, {@link AbstractFilter::ACCEPT} is
 * returned, and if <var>acceptOnMatch</var> is *false*, {@link
 * AbstractFilter::NEUTRAL} is returned.
 *
 * If <var>levelMin</var> is not defined, then there is no minimum acceptable
 * level (i.e. a level is never rejected for being too "low"/unimportant). If
 * <var>levelMax</var> is not defined, then there is no maximum acceptable level
 * (ie a level is never rejected for being too "high"/important).
 *
 * Refer to the {@link Appender::setThreshold()} method available to **all**
 * appenders extending {@link Appender} for a more convenient way to filter out
 * events by level.
 *
 * @author Simon Kitching
 * @author based on the org.apache.log4j.varia.LevelRangeFilte Java code by Ceki G&uuml;lc&uuml;
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @since 0.6
 */
class LevelRangeFilter extends AbstractFilter
{
    /**
     * @var boolean
     */
    protected $acceptOnMatch = true;

    /**
     * @var Level
     */
    protected $levelMin;

    /**
     * @var Level
     */
    protected $levelMax;

    /**
     * @param boolean $acceptOnMatch
     */
    public function setAcceptOnMatch($acceptOnMatch)
    {
        $this->setBoolean('acceptOnMatch', $acceptOnMatch);
    }

    /**
     * @param string $l the level min to match
     */
    public function setLevelMin($level)
    {
        $this->setLevel('levelMin', $level);
    }

    /**
     * @param string $l the level max to match
     */
    public function setLevelMax($level)
    {
        $this->setLevel('levelMax', $level);
    }

    /**
     * Return the decision of this filter.
     *
     * @param  LoggingEvent $event
     * @return integer
     */
    public function decide(LoggingEvent $event)
    {
        $level = $event->getLevel();

        if ($this->levelMin !== null) {
            if ($level->isGreaterOrEqual($this->levelMin) == false) {
                // level of event is less than minimum
                return AbstractFilter::DENY;
            }
        }

        if ($this->levelMax !== null) {
            if ($level->toInt() > $this->levelMax->toInt()) {
                // level of event is greater than maximum
                // Alas, there is no Level.isGreater method. and using
                // a combo of isGreaterOrEqual && !Equal seems worse than
                // checking the int values of the level objects..
                return AbstractFilter::DENY;
            }
        }

        if ($this->acceptOnMatch) {
            // this filter set up to bypass later filters and always return
            // accept if level in range
            return AbstractFilter::ACCEPT;
        } else {
            // event is ok for this filter; allow later filters to have a look..
            return AbstractFilter::NEUTRAL;
        }
    }
}
