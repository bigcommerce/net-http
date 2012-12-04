<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

/**
 * Represents an HTTP protocol error in the 500 range: "It's not me, it's you".
 *
 * Generally indicates that an error condition happened on the server, either
 * broken application code, or too many requests bringing it down.
 *
 * - 500 Server Error
 * - 501 Not Implemented
 * - 502 Bad Gateway
 * - 503 Out of Resources
 * - 504 Gateway Time-Out
 * - 505 HTTP Version not supported
 */
class Net_Http_ServerError extends Net_Http_ProtocolError {}