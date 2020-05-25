<?php

namespace AgileThemeTools\Form\Element;

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
            'region:default' => 'Primary Content Tier',
            'region:splash' => 'Splash Area',
            'region:secondary-content' => 'Secondary Content Tier',
        ]);
    }
}