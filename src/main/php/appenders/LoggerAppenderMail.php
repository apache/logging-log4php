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

/**
 * LoggerAppenderMail appends log events via email.
 *
 * This appender does not send individual emails for each logging requests but 
 * will collect them in a buffer and send them all in a single email once the 
 * appender is closed (i.e. when the script exists). Because of this, it may 
 * not appropriate for long running scripts, in which case 
 * LoggerAppenderMailEvent might be a better choice.
 * 
 * This appender uses a layout.
 * 
 * ## Configurable parameters: ##
 * 
 * - **to** - Email address(es) to which the log will be sent. Multiple email 
 *     addresses may be specified by separating them with a comma.
 * - **from** - Email address which will be used in the From field.
 * - **subject** - Subject of the email message.
 * - **bufferSize** - Output buffer size. Number of messages sent together.
 * 
 * @package log4php
 * @subpackage appenders
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/mail.html Appender documentation
 */
class LoggerAppenderMail extends LoggerAppender {

	/** 
	 * Email address to put in From field of the email.
	 * @var string
	 */
	protected $from;

	/** 
	 * The subject of the email.
	 * @var string
	 */
	protected $subject = 'Log4php Report';
	
	/**
	 * One or more comma separated email addresses to which to send the email. 
	 * @var string
	 */
	protected $to;

	/** 
	 * Buffer which holds the email contents before it is sent. 
	 * @var string  
	 */
	protected $body = '';
	
	/**
	 * Output buffer size. Number of meessages kept in buffer before sending.
	 * @var integer
	 */
	protected $bufferSize;

	/**
	 * Number of messages currently in buffer.
	 * @var string
	 */
	protected $bufferCount = 0;

	public function append(LoggerLoggingEvent $event) {
		$this->body .= $this->layout->format($event);
		$this->bufferCount += 1;
		if(isset($this->bufferSize) && $this->bufferCount >= $this->bufferSize) {
			$this->send();
		}
	}

	public function activateOptions() {
		if (empty($this->from)) {
			$this->warn("Required parameter 'from' not set. Closing appender.");
			$this->closed = true;
			return;
		}
		if (empty($this->to)) {
			$this->warn("Required parameter 'to' not set. Closing appender.");
			$this->closed = true;
			return;
		}
	}
	
	public function close() {
		if(!$this->closed) {
			if(!empty($this->body)) {
				$this->send();
			}
			$this->closed = true;
		}
	}

	protected function send() {
		$message = $this->layout->getHeader() . $this->body . $this->layout->getFooter();
		$contentType = $this->layout->getContentType();

		$headers = "From: {$this->from}\r\n";
		$headers .= "Content-Type: {$contentType}\r\n";

		$success = mail($this->to, $this->subject, $message, $headers);
		if ($success === false) {
			$this->warn("Failed sending email. Please check your php.ini settings. Closing appender.");
			$this->closed = true;
		}

		$this->bufferCount = 0;
		$this->body = '';
	}
	
	/** Sets the 'subject' parameter. */
	public function setSubject($subject) {
		$this->setString('subject', $subject);
	}
	
	/** Returns the 'subject' parameter. */
	public function getSubject() {
		return $this->subject;
	}
	
	/** Sets the 'to' parameter. */
	public function setTo($to) {
		$this->setString('to', $to);
	}
	
	/** Returns the 'to' parameter. */
	public function getTo() {
		return $this->to;
	}

	/** Sets the 'from' parameter. */
	public function setFrom($from) {
		$this->setString('from', $from);
	}
	
	/** Returns the 'from' parameter. */
	public function getFrom() {
		return $this->from;
	}

	/** Sets the 'bufferSize' parameter. */
	public function setBufferSize($bufferSize) {
		$this->setInteger('bufferSize', $bufferSize);
	}

	/** Returns the 'bufferSize' parameter. */
	public function getBufferSize() {
		return $this->bufferSize;
	}
}
