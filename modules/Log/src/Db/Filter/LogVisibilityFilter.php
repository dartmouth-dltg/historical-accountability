<?php
namespace Log\Db\Filter;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Log\Entity\Log;
use Omeka\Permissions\Acl;

/**
 * Filter log by visibility.
 *
 * Checks to see if the current user has permission to view a log.
 */
class LogVisibilityFilter extends SQLFilter
{
    /**
     * @var Acl
     */
    protected $acl;

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->getName() === Log::class) {
            return $this->getLogConstraint($targetTableAlias);
        }

        return '';
    }

    /**
     * Get the constraint for logs.
     *
     * @param string $alias
     * @return string
     */
    protected function getLogConstraint($alias)
    {
        if ($this->acl->userIsAllowed(Log::class, 'view-all')) {
            return '';
        }

        $constraint = '';

        // Users can view all logs they own.
        $identity = $this->acl->getAuthenticationService()->getIdentity();
        if ($identity) {
            $constraint = sprintf(
                $alias . '.owner_id = %s',
                $this->getConnection()->quote($identity->getId(), Type::INTEGER)
            );
        }

        return $constraint;
    }

    public function setAcl(Acl $acl)
    {
        $this->acl = $acl;
    }
}
