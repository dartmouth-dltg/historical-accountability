<?php
namespace Log\View\Helper;

use Omeka\Api\Exception\NotFoundException;
use Zend\View\Helper\AbstractHelper;

/**
 * View helper for rendering search filters.
 */
class LogSearchFilters extends AbstractHelper
{
    /**
     * The default partial view script.
     */
    const PARTIAL_NAME = 'common/search-filters';

    /**
     * Render filters from search query.
     *
     * @return array
     */
    public function __invoke($partialName = null)
    {
        $partialName = $partialName ?: self::PARTIAL_NAME;

        $view = $this->getView();
        $translate = $view->plugin('translate');

        $filters = [];
        $api = $view->api();
        $query = $view->params()->fromQuery();

        $severities = [
            \Zend\Log\Logger::EMERG => 'emergency', // @translate
            \Zend\Log\Logger::ALERT => 'alert', // @translate
            \Zend\Log\Logger::CRIT => 'critical', // @translate
            \Zend\Log\Logger::ERR => 'error', // @translate
            \Zend\Log\Logger::WARN => 'warning', // @translate
            \Zend\Log\Logger::NOTICE => 'notice', // @translate
            \Zend\Log\Logger::INFO => 'info', // @translate
            \Zend\Log\Logger::DEBUG => 'debug', // @translate
        ];

        foreach ($query as $key => $value) {
            if (is_null($value) || $value === '') {
                continue;
            }
            switch ($key) {
                case 'created':
                    $filterLabel = $translate('Created'); // @translate
                    $filterValue = $value;
                    $filters[$filterLabel][] = $filterValue;
                    break;

                case 'message':
                    $filterLabel = $translate('Message contains'); // @translate
                    $filterValue = $value;
                    $filters[$filterLabel][] = $filterValue;
                    break;

                case 'reference':
                    $filterLabel = $translate('Reference'); // @translate
                    $filterValue = $value;
                    $filters[$filterLabel][] = $filterValue;
                    break;

                case 'severity':
                    $filterLabel = $translate('Severity'); // @translate
                    $filterValue = isset($severities[$value]) ? $severities[$value] : $value;
                    $filters[$filterLabel][] = $filterValue;
                    break;
                case 'severity_min':
                    $filterLabel = $translate('Severity'); // @translate
                    $filterValue = '>=';
                    $filterValue .= isset($severities[$value]) ? $severities[$value] : $value;
                    $filters[$filterLabel][] = $filterValue;
                    break;
                case 'severity_max':
                    $filterLabel = $translate('Severity'); // @translate
                    $filterValue = '<=';
                    $filterValue .= isset($severities[$value]) ? $severities[$value] : $value;
                    $filters[$filterLabel][] = $filterValue;
                    break;

                case 'owner_id':
                    $filterLabel = $translate('User'); // @translate
                    try {
                        $filterValue = $api->read('users', $value)->getContent()->name();
                    } catch (NotFoundException $e) {
                        $filterValue = $translate('Unknown user'); // @translate
                    }
                    $filters[$filterLabel][] = $filterValue;
                    break;

                case 'job_id':
                    $filterLabel = $translate('Job');
                    try {
                        $filterValue = $api->read('jobs', $value)->getContent()->id();
                    } catch (NotFoundException $e) {
                        $filterValue = $translate('Unknown job'); // @translate
                    }
                    $filters[$filterLabel][] = $filterValue;
                    break;
            }
        }

        $result = $this->getView()->trigger(
            'view.search.filters',
            ['filters' => $filters, 'query' => $query],
            true
        );
        $filters = $result['filters'];

        return $this->getView()->partial(
            $partialName,
            [
                'filters' => $filters,
            ]
        );
    }
}
