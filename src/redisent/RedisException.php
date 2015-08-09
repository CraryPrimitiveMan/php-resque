<?php
namespace resque\redisent;

/**
 * @author Harry Sun <sunguangjun@126.com>
 * @copyright 2015 Harry Sun <sunguangjun@126.com>
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package resque\redisent
 */

/**
 * Wraps native Redis errors in friendlier PHP exceptions
 * Only declared if class doesn't already exist to ensure compatibility with php-redis
 */
if (!class_exists('\RedisException', false)) {
    class RedisException extends \Exception
    {
    }
} else {
    class RedisException extends \RedisException
    {
    }
}
