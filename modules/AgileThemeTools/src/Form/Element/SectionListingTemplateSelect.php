<?php

namespace AgileThemeTools\Form\Element;

use Zend\Form\Element\Select;

class SectionListingTemplateSelect extends Select {

    function __construct($name = 'o:block[__blockIndex__][o:data][template]', $options = [])
    {
        parent::__construct($name, $options);
        $this->setLabel('Select a template');
        $this->setValueOptions(count($options) > 0 ? $options: $this->getTemplateValueOptions());
    }

    public function getTemplateValueOptions() {
        return([
            'common/block-layout/cards' => 'Cards',
            'common/block-layout/cards-compact' => 'Compact Cards',
            'common/block-layout/item-list' => 'List',
            'common/block-layout/poster' => 'Poster',
            'common/block-layout/feature-1' => 'Feature One',
            'common/block-layout/feature-2' => 'Feature Two',
            'common/block-layout/feature-3' => 'Feature Three'
        ]);
    }
}