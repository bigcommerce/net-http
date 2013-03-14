<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

/**
 * Any general HTTP protocol error.
 *
 * See {@link Net_Http_ClientError} and {@link Net_Http_ServerError}
 */
abstract class Net_Http_ProtocolError extends Net_Http_Exception {}