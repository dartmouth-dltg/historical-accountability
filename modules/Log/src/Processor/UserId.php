<?php

namespace Log\Processor;

use Omeka\Entity\User;
use Zend\Log\Processor\ProcessorInterface;

class UserId implements ProcessorInterface
{
    /**
     * @var int|null
     */
    protected $userId;

    /**
     * @param int|null $userId
     */
    public function __construct(User $user = null)
    {
        if ($user) {
            $this->userId = $user->getId();
        }
    }

    /**
     * Adds the user id to the log, even if it has already been set manually, in
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

        $event['extra']['userId'] = $this->userId;

        return $event;
    }
}
