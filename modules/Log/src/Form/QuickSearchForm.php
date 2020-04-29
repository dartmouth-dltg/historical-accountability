<?php

namespace Log\Form;

use Omeka\Form\Element\ResourceSelect;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\View\Helper\Url;

class QuickSearchForm extends Form
{
    /**
     * @var Url
     */
    protected $urlHelper;

    public function init()
    {
        $this->setAttribute('method', 'get');

        // No csrf: see main search form.
        $this->remove('csrf');

        $urlHelper = $this->getUrlHelper();

        $this->add([
            'type' => Element\Text::class,
            'name' => 'created',
            'options' => [
                'label' => 'Date', // @translate
            ],
            'attributes' => [
                'placeholder' => 'Set a date with optional comparator…', // @translate
            ],
        ]);

        $valueOptions = [
            '0' => 'Emergency',  // @translate
            '1' => 'Alert', // @translate
            '2' => 'Critical', // @translate
            '3' => 'Error', // @translate
            '4' => 'Warning', // @translate
            '5' => 'Notice', // @translate
            '6' => 'Info', // @translate
            '7' => 'Debug', // @translate
        ];
        $this->add([
            'name' => 'severity_min',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Minimum severity', // @translate
                'value_options' => $valueOptions,
                'empty_option' => '',
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select minimum severity…', // @translate
            ],
        ]);
        $this->add([
            'name' => 'severity_max',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Maximum severity', // @translate
                'value_options' => $valueOptions,
                'empty_option' => '',
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select maximum severity…', // @translate
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'reference',
            'options' => [
                'label' => 'Reference', // @translate
            ],
            'attributes' => [
                'placeholder' => 'Set a reference…', // @translate
            ],
        ]);

        $this->add([
            'type' => Element\Number::class,
            'name' => 'job_id',
            'options' => [
                'label' => 'Job', // @translate
            ],
            'attributes' => [
                'placeholder' => 'Set a job id…', // @translate
            ],
        ]);

        $this->add([
            'name' => 'owner_id',
            'type' => ResourceSelect::class,
            'options' => [
                'label' => 'Owner', // @translate
                'resource_value_options' => [
                    'resource' => 'users',
                    'query' => [],
                    'option_text_callback' => function ($user) {
                        return $user->name();
                    },
                ],
                'empty_option' => '',
            ],
            'attributes' => [
                'id' => 'owner_id',
                'class' => 'chosen-select',
                'data-placeholder' => 'Select a user…', // @translate
                'data-api-base-url' => $urlHelper('api/default', ['resource' => 'users']),
            ],
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'message',
            'options' => [
                // TODO Manage search in translated messages as they are displayed.
                'label' => 'Untranslated message', // @translate
            ],
            'attributes' => [
                'placeholder' => 'Set an untranslated string…', // @translate
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Search', // @translate
                'type' => 'submit',
            ],
        ]);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'severity',
            'required' => false,
        ]);
    }

    /**
     * @param Url $urlHelper
     */
    public function setUrlHelper(Url $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * @return \Zend\View\Helper\Url
     */
    public function getUrlHelper()
    {
        return $this->urlHelper;
    }
}
