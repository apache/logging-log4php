<?php
/**
 * Copyright 2004 The Apache Software Foundation.
 *
 * This software is published under the terms of the Apache Software
 * License version 2.0, a copy of which has been included with this
 * distribution in the LICENSE file.
 * 
 * @package tests
 * @author Marco V. <marco@apache.org>
 * @subpackage others
 * @version $Revision$
 * @since 0.6
 */

echo chunk_split(serialize(LoggerManager::getLoggerRepository()));
 
?>