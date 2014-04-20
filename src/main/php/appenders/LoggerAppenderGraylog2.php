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
 * LoggerAppenderGraylog2 appends log events to a Graylog2 server.
 *
 * ## Configurable parameters: ##
 *
 * - **host** - Server on which graylog2-server instance is located (optional,
 *     defaults to localhost).
 * - **port** - Port on which the instance is bound (optional, defaults to 12201).
 * - **timeout** - Connection timeout in seconds (optional, defaults to
 *     'default_socket_timeout' from php.ini)
 * - **chunkSize** - Message chunk size in bytes (optional, defaults to 8152).
 *
 * The socket will by default be opened in blocking mode.
 *
 * @package log4php
 * @subpackage appenders
 * @since 2.4.0
 * @author Dmitry Ulyanov dmitriy.ulyanov@wikimart.ru
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link http://logging.apache.org/log4php/docs/appenders/graylog2.html Appender documentation
 * @link http://github.com/d-ulyanov/log4php-graylog2 Dmitry Ulyanov's original submission
 * @link http://graylog2.org/ Graylog2 website
 */
class LoggerAppenderGraylog2 extends LoggerAppender
{
    /** Default value for {@link $host} */
    const DEFAULT_HOST = 'localhost';

    /** Default value for {@link $port} */
    const DEFAULT_PORT = 12201;

    /** Default value for {@link $chunkSize} */
    const DEFAULT_CHUNK_SIZE = 8152;

    /**
     * Server on which graylog2-server instance is located
     * @var string
     */
    protected $host = self::DEFAULT_HOST;

    /**
     * Port on which the instance is bound
     * @var int
     */
    protected $port = self::DEFAULT_PORT;

    /**
     * Message chunk size in bytes
     * @var int
     */
    protected $chunkSize = self::DEFAULT_CHUNK_SIZE;

    /**
     * Connection timeout in seconds (defaults to
     * 'default_socket_timeout' from php.ini)
     * @var int
     */
    protected $timeout;

    /** Override the default layout. */
    public function getDefaultLayout() {
        return new LoggerLayoutGelf();
    }

    public function activateOptions() {
        if (is_null($this->getTimeout())) {
            $this->setTimeout(ini_get("default_socket_timeout"));
        }
    }

    /**
     * Forwards the logging event to the Graylog2 server.
     * @param LoggerLoggingEvent $event
     */
    protected function append(LoggerLoggingEvent $event) {
        $message = gzcompress($this->layout->format($event));

        $stream = $this->createConnection(
            $this->getHostAddr(),
            $this->getPort(),
            $this->getTimeout()
        );

        if ($stream === false) {
            $this->close();
            $this->warn(sprintf('Failed to connect to socket: %s:%i', $this->getHost(), $this->getPort()));
            return;
        }

        foreach ($this->splitMessageIntoChunks($message, $this->getChunkSize()) as $chunk) {
            $bytesWritten = $this->writeMessageToSocket($stream, $chunk);

            if(false === $bytesWritten) {
                $this->close();
                return;
            }
        }

        $this->closeConnection($stream);
    }

    /**
     * Returns array of message chunks
     * @param $message
     * @param $chunkSize
     * @return array
     */
    public function splitMessageIntoChunks($message, $chunkSize) {
        $chunks = array();

        if (mb_strlen($message) <= $chunkSize) {
            // Return original message
            $chunks[] = $message;
        } else {
            $messageId = uniqid();

            // Split the message into chunks
            $messageChunks = $this->splitUnicodeString($message, $chunkSize);
            $messageChunksCount = count($messageChunks);

            // Send chunks to graylog server
            foreach($messageChunks as $messageChunkIndex => $messageChunk) {
                $chunks[] = $this->createMessageFromChunk(
                    $messageId,
                    $messageChunk,
                    $messageChunkIndex,
                    $messageChunksCount
                );
            }
        }

        return $chunks;
    }

    /**
     * Returns socket connection
     * @return resource
     */
    protected function createConnection($hostAddr, $port, $timeout) {
        $socket = sprintf('udp://%s:%d', $hostAddr, $port);
        $errno = $errstr = null;
        $stream = $this->createStreamSocketClient($socket, $timeout);
        if ($stream !== false) {
            stream_set_timeout($stream, $timeout);
        }

        return $stream;
    }

    /**
     * @param string $socket
     * @param int $timeout
     * @return resource
     */
    protected function createStreamSocketClient($socket, $timeout) {
        $errno = $errstr = null;
        return stream_socket_client($socket, $errno, $errstr, $timeout);
    }

    /**
     * Closes connection
     * @param resource $stream
     * @return bool
     */
    protected function closeConnection($stream) {
        return stream_socket_shutdown($stream, STREAM_SHUT_RDWR);
    }

    /**
     * @param string $messageId
     * @param string $messageChunk
     * @param int $messageChunkIndex
     * @param int $messageChunksCount
     * @return string
     */
    protected function createMessageFromChunk($messageId, $messageChunk, $messageChunkIndex, $messageChunksCount) {
        return pack('CC', 30, 15) .
            substr(md5($messageId, true), 0, 8) .
            pack('CC', $messageChunkIndex, $messageChunksCount) .
            $messageChunk;
    }

    /**
     * @param resource $handle
     * @param string $message
     * @return int|boolean
     */
    protected function writeMessageToSocket($handle, $message) {
        return fwrite($handle, $message);
    }

    /**
     * Return splitted unicode string
     *
     * @param string $string
     * @param int $chunkLength
     * @return array
     */
    public function splitUnicodeString($string, $chunkLength = 1) {
        if ($chunkLength < 1) {
            throw new Exception("The length of each segment must be greater than zero");
        }

        $out = array();
        $length = mb_strlen($string, "UTF-8");

        for ($i = 0; $i < $length; $i += $chunkLength) {
            $out[] = mb_substr($string, $i, $chunkLength, "UTF-8");
        }

        return $out;
    }

    /**
     * Returns IPv4 host address
     * @return string
     */
    public function getHostAddr() {
        if (filter_var($this->getHost(), FILTER_VALIDATE_IP)) {
            $host = $this->getHost();
        } else {
            $host = gethostbyname($this->getHost());
        }

        return $host;
    }

    /**
     * @param int $chunkSize
     */
    public function setChunkSize($chunkSize) {
        $this->setPositiveInteger('chunkSize', $chunkSize);
    }

    /**
     * @return int
     */
    public function getChunkSize() {
        return $this->chunkSize;
    }

    /**
     * @param string $host
     */
    public function setHost($host) {
        $this->setString('host', $host);
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @param int $port
     */
    public function setPort($port) {
        $this->setPositiveInteger('port', $port);
    }

    /**
     * @return int
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout) {
        $this->setPositiveInteger('timeout', $timeout);
    }

    /**
     * @return int
     */
    public function getTimeout() {
        return $this->timeout;
    }
}
