<?php
namespace Log\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Comparison;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;
use Zend\Log\Logger;

class LogAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [
        'id' => 'id',
        'owner' => 'owner',
        'job' => 'job',
        'reference' => 'reference',
        'severity' => 'severity',
        'created' => 'created',
    ];

    public function getResourceName()
    {
        return 'logs';
    }

    public function getRepresentationClass()
    {
        return \Log\Api\Representation\LogRepresentation::class;
    }

    public function getEntityClass()
    {
        return \Log\Entity\Log::class;
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    {
        $isOldOmeka = \Omeka\Module::VERSION < 2;
        $alias = $isOldOmeka ? $this->getEntityClass() : 'omeka_root';
        $expr = $qb->expr();

        // User table is not joined to get only existing users: useless with
        // "on delete set null".
        if (isset($query['owner_id']) && strlen($query['owner_id'])) {
            $qb->andWhere($expr->eq(
                $alias . '.owner',
                $this->createNamedParameter($qb, $query['owner_id'])
            ));
        }

        // Job table is not joined to get only existing jobs: useless with
        // "on delete cascade".
        if (isset($query['job_id']) && strlen($query['job_id'])) {
            $qb->andWhere($expr->eq(
                $alias . '.job',
                $this->createNamedParameter($qb, $query['job_id'])
            ));
        }

        if (isset($query['reference']) && strlen($query['reference'])) {
            $qb->andWhere($expr->eq(
                $alias . '.reference',
                $this->createNamedParameter($qb, $query['reference'])
            ));
        }

        // TODO Allow to search severity by standard name.
        if (isset($query['severity']) && strlen($query['severity'])) {
            $this->buildQuerySeverityComparison($qb, $query, $query['severity'], 'severity');
        }

        // TODO Remove severity_min and severity_max here and replace them by a javascript.
        if (isset($query['severity_min']) && strlen($query['severity_min'])) {
            $this->buildQuerySeverityComparison($qb, $query, '<=' . $query['severity_min'], 'severity');
        }
        if (isset($query['severity_max']) && strlen($query['severity_max'])) {
            $this->buildQuerySeverityComparison($qb, $query, '>=' . $query['severity_max'], 'severity');
        }

        if (isset($query['created']) && strlen($query['created'])) {
            $this->buildQueryDateComparison($qb, $query, $query['created'], 'created');
        }

        // TODO Manage search in translated messages as they are displayed.
        if (isset($query['message']) && strlen($query['message'])) {
            if (!is_array($query['message'])) {
                $query['message'] = ['text' => $query['message'], 'type' => 'in'];
            }
            foreach ($query['message'] as $message) {
                if (!is_array($message)) {
                    $message = ['text' => $message, 'type' => 'in'];
                }
                if (!isset($message['text']) || !strlen($message['text'])) {
                    continue;
                }
                $text = $message['text'];
                $queryType = isset($message['type']) ? $message['type'] : 'in';
                switch ($queryType) {
                    case 'neq':
                        $qb->andWhere($expr->neq(
                            $alias . '.message',
                            $this->createNamedParameter($qb, $text)
                        ));
                        break;
                    case 'eq':
                        $qb->andWhere($expr->eq(
                            $alias . '.message',
                            $this->createNamedParameter($qb, $text)
                        ));
                        break;
                    case 'nin':
                        $qb->andWhere($expr->notLike(
                            $alias . '.message',
                            $this->createNamedParameter($qb,'%' . $text . '%')
                        ));
                        break;
                    case 'in':
                        $qb->andWhere($expr->like(
                            $alias . '.message',
                            $this->createNamedParameter($qb,'%' . $text . '%')
                        ));
                        break;
                    default:
                        continue 2;
                }
            }
        }
    }

    public function hydrate(
        Request $request,
        EntityInterface $entity,
        ErrorStore $errorStore
    ) {
        switch ($request->getOperation()) {
            case Request::CREATE:
                $data = $request->getContent();
                if (empty($data['o:owner'])) {
                    $owner = null;
                } elseif (is_object($data['o:owner'])) {
                    $owner = $data['o:owner'];
                } else {
                    $owner = $this->getAdapter('users')->findEntity($data['o:owner']['o:id']);
                }
                if (empty($data['o:job'])) {
                    $job = null;
                } elseif (is_object($data['o:job'])) {
                    $job = $data['o:job'];
                } else {
                    $job = $this->getAdapter('jobs')->findEntity($data['o:job']['o:id']);
                }
                $entity->setOwner($owner);
                $entity->setJob($job);
                $entity->setReference($data['o:reference']);
                $entity->setSeverity($data['o:severity']);
                $entity->setMessage($data['o:message']);
                $entity->setContext($data['o:context']);
                $entity->setCreated(new \DateTime('now'));
                break;
        }
    }

    /**
     * Add a comparison condition to query from a value containing an operator.
     *
     * @param QueryBuilder $qb
     * @param array $query
     * @param string $value
     * @param string $column
     */
    protected function buildQueryComparison(QueryBuilder $qb, array $query, $value, $column)
    {
        $isOldOmeka = \Omeka\Module::VERSION < 2;
        $alias = $isOldOmeka ? $this->getEntityClass() : 'omeka_root';

        $matches = [];
        preg_match('/^[^\d]+/', $value, $matches);
        if (!empty($matches[0])) {
            $operators = [
                '>=' => Comparison::GTE,
                '>' => Comparison::GT,
                '<' => Comparison::LT,
                '<=' => Comparison::LTE,
                '<>' => Comparison::NEQ,
                '=' => Comparison::EQ,
                'gte' => Comparison::GTE,
                'gt' => Comparison::GT,
                'lt' => Comparison::LT,
                'lte' => Comparison::LTE,
                'neq' => Comparison::NEQ,
                'eq' => Comparison::EQ,
            ];
            $operator = isset($operators[$matches[0]])
                ? $operators[$matches[0]]
                : Comparison::EQ;
            $value = (int) substr($value, strlen($matches[0]));
        } else {
            $operator = Comparison::EQ;
        }
        $qb->andWhere(new Comparison(
            $alias . '.' . $column,
            $operator,
            $this->createNamedParameter($qb, $value)
        ));
    }

    /**
     * Add a comparison condition to query from a severity (string or numeric).
     *
     * @param QueryBuilder $qb
     * @param array $query
     * @param string $value
     * @param string $column
     */
    protected function buildQuerySeverityComparison(QueryBuilder $qb, array $query, $value, $column)
    {
        $map = [
            'emergency' => Logger::EMERG,
            'emerg' => Logger::EMERG,
            'alert' => Logger::ALERT,
            'critical' => Logger::CRIT,
            'crit' => Logger::CRIT,
            'errror' => Logger::ERR,
            'err' => Logger::ERR,
            'warning' => Logger::WARN,
            'warn' => Logger::WARN,
            'notice' => Logger::NOTICE,
            'informational' => Logger::INFO,
            'information' => Logger::INFO,
            'info' => Logger::INFO,
            'debug' => Logger::DEBUG,
        ];
        $value = str_ireplace(array_keys($map), array_values($map), $value);
        $this->buildQueryComparison($qb, $query, $value, $column);
    }

    /**
     * Add a comparison condition to query from a date.
     *
     * @param QueryBuilder $qb
     * @param array $query
     * @param string $value
     * @param string $column
     */
    protected function buildQueryDateComparison(QueryBuilder $qb, array $query, $value, $column)
    {
        $isOldOmeka = \Omeka\Module::VERSION < 2;
        $alias = $isOldOmeka ? $this->getEntityClass() : 'omeka_root';

        // TODO Format the date into a standard mysql datetime.
        $matches = [];
        preg_match('/^[^\d]+/', $value, $matches);
        if (!empty($matches[0])) {
            $operators = [
                '>=' => Comparison::GTE,
                '>' => Comparison::GT,
                '<' => Comparison::LT,
                '<=' => Comparison::LTE,
                '<>' => Comparison::NEQ,
                '=' => Comparison::EQ,
                'gte' => Comparison::GTE,
                'gt' => Comparison::GT,
                'lt' => Comparison::LT,
                'lte' => Comparison::LTE,
                'neq' => Comparison::NEQ,
                'eq' => Comparison::EQ,
                'ex' => 'IS NOT NULL',
                'nex' => 'IS NULL',
            ];
            $operator = trim($matches[0]);
            $operator = isset($operators[$operator])
                ? $operators[$operator]
                : Comparison::EQ;
            $value = substr($value, strlen($matches[0]));
        } else {
            $operator = Comparison::EQ;
        }
        $value = trim($value);

        // By default, sql replace missing time by 00:00:00, but this is not
        // clear for the user. And it doesn't allow partial date/time.
        // See module Advanced Search Plus.

        $expr = $qb->expr();

        // $qb->andWhere(new Comparison(
        //     $alias . '.' . $column,
        //     $operator,
        //     $this->createNamedParameter($qb, $value)
        // ));
        // return;

        $field = $alias . '.' . $column;
        switch ($operator) {
            case Comparison::GT:
                if (strlen($value) < 19) {
                    $value = substr_replace('9999-12-31 23:59:59', $value, 0, strlen($value) - 19);
                }
                $param = $this->createNamedParameter($qb, $value);
                $predicateExpr = $expr->gt($field, $param);
                break;
            case Comparison::GTE:
                if (strlen($value) < 19) {
                    $value = substr_replace('0000-01-01 00:00:00', $value, 0, strlen($value) - 19);
                }
                $param = $this->createNamedParameter($qb, $value);
                $predicateExpr = $expr->gte($field, $param);
                break;
            case Comparison::EQ:
                if (strlen($value) < 19) {
                    $valueFrom = substr_replace('0000-01-01 00:00:00', $value, 0, strlen($value) - 19);
                    $valueTo = substr_replace('9999-12-31 23:59:59', $value, 0, strlen($value) - 19);
                    $paramFrom = $this->createNamedParameter($qb, $valueFrom);
                    $paramTo = $this->createNamedParameter($qb, $valueTo);
                    $predicateExpr = $expr->between($field, $paramFrom, $paramTo);
                } else {
                    $param = $this->createNamedParameter($qb, $value);
                    $predicateExpr = $expr->eq($field, $param);
                }
                break;
            case Comparison::NEQ:
                if (strlen($value) < 19) {
                    $valueFrom = substr_replace('0000-01-01 00:00:00', $value, 0, strlen($value) - 19);
                    $valueTo = substr_replace('9999-12-31 23:59:59', $value, 0, strlen($value) - 19);
                    $paramFrom = $this->createNamedParameter($qb, $valueFrom);
                    $paramTo = $this->createNamedParameter($qb, $valueTo);
                    $predicateExpr = $expr->not(
                        $expr->between($field, $paramFrom, $paramTo)
                    );
                } else {
                    $param = $this->createNamedParameter($qb, $value);
                    $predicateExpr = $expr->neq($field, $param);
                }
                break;
            case Comparison::LTE:
                if (strlen($value) < 19) {
                    $value = substr_replace('9999-12-31 23:59:59', $value, 0, strlen($value) - 19);
                }
                $param = $this->createNamedParameter($qb, $value);
                $predicateExpr = $expr->lte($field, $param);
                break;
            case Comparison::LT:
                if (strlen($value) < 19) {
                    $value = substr_replace('0000-01-01 00:00:00', $value, 0, strlen($value) - 19);
                }
                $param = $this->createNamedParameter($qb, $value);
                $predicateExpr = $expr->lt($field, $param);
                break;
            case 'IS NOT NULL':
                $predicateExpr = $expr->isNotNull($field);
                break;
            case 'IS NULL':
                $predicateExpr = $expr->isNull($field);
                break;
            default:
                return;
        }

        $qb->andWhere($predicateExpr);
    }
}
