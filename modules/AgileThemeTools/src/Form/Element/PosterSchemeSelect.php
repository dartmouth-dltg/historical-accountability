<?php

namespace AgileThemeTools\Form\Element;

use Zend\Form\Element\Select;

class PosterSchemeSelect extends Select {

    function __construct($name = 'o:block[__blockIndex__][o:data][scheme]', $options = [])
    {
        parent::__construct($name, $options);
        $this->setLabel('Colour Scheme');
        $this->setValueOptions(count($options) > 0 ? $options: $this->getSchemeValueOptiopns());
    }

    public function getSchemeValueOptiopns() {
        return([
            'scheme:scheme1' => 'Colour Scheme 1',
            'scheme:scheme2' => 'Colour Scheme 2',
            'scheme:scheme3' => 'Colour Scheme 3',
            'scheme:scheme4' => 'Colour Scheme 4'
        ]);
    }
}