<?php
namespace resque\core\job;

/**
 * Runtime exception class for a job that does not exit cleanly.
 *
 * @author      Chris Boulton <chris@bigcommerce.com>
 * @license     http://www.opensource.org/licenses/mit-license.php
 */
class DirtyExitException extends \RuntimeException
{
}
