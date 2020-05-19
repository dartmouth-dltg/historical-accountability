<?php

namespace AgileThemeTools\Form\Element;

use Zend\Form\Element\Select;

class PosterSchemeSelect extends Select {

    function __construct($name = 'o:block[__blockIndex__][o:data][scheme]', $options = [])
    {
        parent::__construct($name, $options);
        $this->setLabel('Colour Scheme');
        $this->setValueOptions(count($options) > 0 ? $options: $this->getSchemeValueOptions());
    }

    public function getSchemeValueOptions() {
        return([
            'scheme:scheme-1' => 'Colour Scheme 1',
            'scheme:scheme-2' => 'Colour Scheme 2',
            'scheme:scheme-3' => 'Colour Scheme 3',
            'scheme:scheme-4' => 'Colour Scheme 4'
        ]);
    }
}