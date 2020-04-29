<?php
namespace Log\Job\DispatchStrategy;

use Doctrine\ORM\EntityManager;
use Log\Log\Writer\Job as JobWriter;
use Omeka\Entity\Job;
use Zend\Log\Logger;

class Synchronous extends \Omeka\Job\DispatchStrategy\Synchronous
{
    /**
     * Copy of parent method, but with psr message and full logging.
     * Logger may be null in order to manage various versions of core.
     *
     * @inheritdoc
     */
    public function handleFatalError(Job $job, EntityManager $entityManager, Logger $logger = null)
    {
        $lastError = error_get_last();
        if ($lastError) {
            $errors = [E_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR];
            if (in_array($lastError['type'], $errors)) {
                // Make sure we only flush this Job and nothing else
                $entityManager->clear();

                // Reload job that may have been updated during process.
                $job = $entityManager->find(Job::class, $job->getId());
                $job->setStatus(Job::STATUS_ERROR);

                if (is_null($logger)) {
                    $logger = $this->serviceLocator->get('Omeka\Logger');

                    // Job writer should be reenabled.
                    if ($this->serviceLocator->get('Config')['logger']['writers']['job']) {
                        $logger->addWriter(new JobWriter($job));
                    }

                    // Enable the user and job id in the default logger.
                    $userJobIdProcessor = new \Log\Processor\UserJobId($job);
                    // The priority "0" fixes a precedency issue with the processor UserId.
                    $logger->addProcessor($userJobIdProcessor, 0);
                }

                $logger->err(
                    "Fatal error: {message}\nin {file} on line {line}", // @translate
                    [
                        'message' => $lastError['message'],
                        'file' => $lastError['file'],
                        'line' => $lastError['line'],
                    ]
                );
                $entityManager->flush();
            }
            // Log other errors according to the config for severity.
            else {
                if (is_null($logger)) {
                    $logger = $this->serviceLocator->get('Omeka\Logger');

                    // Job writer should be reenabled.
                    if ($this->serviceLocator->get('Config')['logger']['writers']['job']) {
                        $logger->addWriter(new JobWriter($job));
                    }

                    // Enable the user and job id in the default logger.
                    $userJobIdProcessor = new \Log\Processor\UserJobId($job);
                    // The priority "0" fixes a precedency issue with the processor UserId.
                    $logger->addProcessor($userJobIdProcessor, 0);
                }
                $logger->warn(
                    "Warning: {message}\nin {file} on line {line}", // @translate
                    [
                        'message' => $lastError['message'],
                        'file' => $lastError['file'],
                        'line' => $lastError['line'],
                    ]
                );
            }
        }
    }
}
