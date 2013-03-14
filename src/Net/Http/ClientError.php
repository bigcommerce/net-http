<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

/**
 * Represents an HTTP protocol error in the 400 range: "It's not you, it's me".
 *
 * Occurs when the client sends a malformed request, has not authenticated correctly,
 * or is requesting a URL that doesn't exist.
 *
 * - 400 Bad Request
 * - 401 Unauthorized
 * - 402 Payment Required
 * - 403 Forbidden
 * - 404 Not Found
 * - 405 Method Not Allowed
 * - 406 Not Acceptable
 * - 407 Proxy Authentication Required4
 * - 408 Request Time-Out
 * - 409 Conflict
 * - 410 Gone
 * - 411 Length Required
 * - 412 Precondition Failed
 * - 413 Request Entity Too Large
 * - 414 Request-URL Too Large
 * - 415 Unsupported Media Type
 * - 416 Requested Range not satisfiable
 * - 417 Expectation failed
 */
class Net_Http_ClientError extends Net_Http_ProtocolError {}