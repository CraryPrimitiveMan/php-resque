<?php
namespace resque;

/**
 * Resque base job.
 *
 * @author Harry Sun <sunguangjun@126.com>
 */
class Job
{
    /**
     * Returns the fully qualified name of this class.
     *
     * @return string the fully qualified name of this class.
     */
    public static function className()
    {
        return get_called_class();
    }
}
