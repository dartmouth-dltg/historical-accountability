<?php

namespace BlockOptions\Form\Element;

use Zend\Form\Element\Select;

class RegionMenuSelect extends Select {

    function __construct($name = 'o:block[__blockIndex__][o:data][region]', $options = [])
    {
        parent::__construct($name, $options);
        $this->setLabel('Assign to region');
        $this->setValueOptions(count($options) > 0 ? $options: $this->getRegionValueOptions());
    }

    public function getRegionValueOptions() {
        return([
            'region:default' => 'Default',
            'region:splash' => 'Splash Area',
            'region:deck' => 'Deck',
            'region:secondary-content' => 'Secondary Content Tier',
            'region:feature-tier-1' => 'Homepage: Feature Tier One',
            'region:feature-tier-2' => 'Homepage: Feature Tier Two',
            'region:feature-tier-3' => 'Homepage: Feature Tier Three',
        ]);
    }
}