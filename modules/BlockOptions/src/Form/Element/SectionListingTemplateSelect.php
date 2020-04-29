<?php

namespace BlockOptions\Form\Element;

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
            'common/block-layout/profile-grid' => 'Profile Grid',
            'common/block-layout/homepage-featured-collections' => 'Homepage – Featured Collections',
            'common/block-layout/homepage-featured-exhibit' => 'Homepage – Featured Exhibit',
            'common/block-layout/homepage-featured-profiles' => 'Homepage – Featured Profiles',
        ]);
    }
}