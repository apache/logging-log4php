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

namespace Apache\Log4php;

/**
 * The NDC class implements *nested diagnostic contexts*.
 *
 * NDC was defined by Neil Harrison in the article "Patterns for Logging
 * Diagnostic Messages" part of the book *"Pattern Languages of
 * Program Design 3"* edited by Martin et al.
 *
 * A Nested Diagnostic Context, or NDC in short, is an instrument
 * to distinguish interleaved log output from different sources. Log
 * output is typically interleaved when a server handles multiple
 * clients near-simultaneously.
 *
 * This class is similar to the {@link MDC} class except that it is
 * based on a stack instead of a map.
 *
 * Interleaved log output can still be meaningful if each log entry
 * from different contexts had a distinctive stamp. This is where NDCs
 * come into play.
 *
 * **Note that NDCs are managed on a per thread basis**.
 *
 * NDC operations such as {@link push()}, {@link pop()},
 * {@link clear()}, {@link getDepth()} and {@link setMaxDepth()}
 * affect the NDC of the *current* thread only. NDCs of other
 * threads remain unaffected.
 *
 * For example, a servlet can build a per client request NDC
 * consisting the clients host name and other information contained in
 * the the request. *Cookies* are another source of distinctive
 * information. To build an NDC one uses the {@link push()}
 * operation.
 *
 * Simply put,
 *
 * - Contexts can be nested.
 * - When entering a context, call <kbd>NDC::push()</kbd>
 *   As a side effect, if there is no nested diagnostic context for the
 *   current thread, this method will create it.
 * - When leaving a context, call <kbd>NDC::pop()</kbd>
 * - **When exiting a thread make sure to call {@link remove()}**
 *
 * There is no penalty for forgetting to match each
 * <kbd>push</kbd> operation with a corresponding <kbd>pop</kbd>,
 * except the obvious mismatch between the real application context
 * and the context set in the NDC.
 *
 * If configured to do so, {@link LoggerPatternLayout}
 * instances automatically retrieve the nested diagnostic
 * context for the current thread without any user intervention.
 * Hence, even if a servlet is serving multiple clients
 * simultaneously, the logs emanating from the same code (belonging to
 * the same category) can still be distinguished because each client
 * request will have a different NDC tag.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php
 * @since 0.3
 */
class NDC
{
    /** This is the repository of NDC stack */
    private static $stack = array();

    /**
     * Clear any nested diagnostic information if any. This method is useful in
     * cases where the same thread can be potentially used over and over in
     * different unrelated contexts.
     *
     * This method is equivalent to calling the {@link setMaxDepth()} method
     * with a zero <var>maxDepth</var> argument.
     */
    public static function clear()
    {
        self::$stack = array();
    }

    /**
     * Never use this method directly, use the {@link LoggingEvent::getNDC()}
     * method instead.
     * @return array
     */
    public static function get()
    {
        return implode(' ', self::$stack);
    }

    /**
     * Get the current nesting depth of this diagnostic context.
     *
     * @see setMaxDepth()
     * @return integer
     */
    public static function getDepth()
    {
        return count(self::$stack);
    }

    /**
     * Clients should call this method before leaving a diagnostic context.
     *
     * The returned value is the value that was pushed last. If no context is
     * available, then the empty string "" is returned.
     *
     * @return string The innermost diagnostic context.
     */
    public static function pop()
    {
        if (count(self::$stack) > 0) {
            return array_pop(self::$stack);
        } else {
            return '';
        }
    }

    /**
     * Looks at the last diagnostic context at the top of this NDC without
     * removing it.
     *
     * The returned value is the value that was pushed last. If no context is
     * available, then the empty string "" is returned.
     *
     * @return string The innermost diagnostic context.
     */
    public static function peek()
    {
        if (count(self::$stack) > 0) {
            return end(self::$stack);
        } else {
            return '';
        }
    }

    /**
     * Push new diagnostic context information for the current thread.
     *
     * The contents of the <var>message</var> parameter is determined solely by
     * the client.
     *
     * @param string $message The new diagnostic context information.
     */
    public static function push($message)
    {
        array_push(self::$stack, (string) $message);
    }

    /**
     * Remove the diagnostic context for this thread.
     */
    public static function remove()
    {
        NDC::clear();
    }

    /**
     * Set maximum depth of this diagnostic context. If the current depth is
     * smaller or equal to <var>maxDepth</var>, then no action is taken.
     *
     * This method is a convenient alternative to multiple {@link pop()} calls.
     * Moreover, it is often the case that at the end of complex call sequences,
     * the depth of the NDC is unpredictable. The {@link setMaxDepth()} method
     * circumvents this problem.
     *
     * @param integer $maxDepth
     * @see getDepth()
     */
    public static function setMaxDepth($maxDepth)
    {
        $maxDepth = (int) $maxDepth;
        if (NDC::getDepth() > $maxDepth) {
            self::$stack = array_slice(self::$stack, 0, $maxDepth);
        }
    }
}
