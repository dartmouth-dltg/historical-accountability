<?php

namespace Log\Formatter;

use DateTime;
use Zend\Log\Formatter\Base;

class PsrLogDb extends Base
{
    use PsrLogAwareTrait;

    protected $dateTimeFormat = 'Y-m-d H:i:s';

    public function __construct($dateTimeFormat = null)
    {
        // The date time format cannot be changed for this table in database.
        // TODO Fix the construct/method for date time format to log in db.
    }

    public function setDateTimeFormat($dateTimeFormat)
    {
        // The date time format cannot be changed for this table in database.
        return $this;
    }

    /**
     * Formats data to be written by the writer.
     *
     * To be used with the processors UserId and JobId.
     *
     * @param array $event event data
     * @return array
     */
    public function format($event)
    {
        $event = parent::format($event);
        $event = $this->normalizeLogContext($event);
        $event = $this->normalizeLogDateTimeFormat($event);

        if (!empty($event['extra']['context']['extra'])) {
            $event['extra']['context']['extra'] = json_encode($event['extra']['context']['extra'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (empty($event['extra']['context'])) {
            $event['extra']['context'] = '[]';
        } else {
            $event['extra']['context'] = json_encode($event['extra']['context'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return $event;
    }

    /**
     * Formats the date time for mysql.
     *
     * @see \Zend\Log\Formatter\Db::format()
     *
     * @param array $event
     * @return array
     */
    protected function normalizeLogDateTimeFormat($event)
    {
        $format = $this->getDateTimeFormat();
        array_walk_recursive($event, function (&$value) use ($format) {
            if ($value instanceof DateTime) {
                $value = $value->format($format);
            }
        });

        return $event;
    }
}
