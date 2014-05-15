<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * A mailer which uses mail() to dispatch emails.
 *
 * @package log4php
 * @subpackage helpers
 * @since 2.4.0
 */
class LoggerMailerPHP implements LoggerMailerInterface {

    public function send($to, $subject, $message, $headers = "") {

        return mail($to, $subject, $message, $headers);
    }
}
