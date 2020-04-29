<?php

namespace Log\Processor;

use Omeka\Entity\Job;
use Zend\Log\Processor\ProcessorInterface;

class JobId implements ProcessorInterface
{
    /**
     * @var int
     */
    protected $jobId;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->jobId = $job->getId();
    }

    /**
     * Adds the job id to the log, even if it has already been set manually, in
     * order to keep consistency in database.
     *
     * @param array $event event data
     * @return array event data
     */
    public function process(array $event)
    {
        if (!isset($event['extra'])) {
            $event['extra'] = [];
        }

        $event['extra']['jobId'] = $this->jobId;

        return $event;
    }
}
