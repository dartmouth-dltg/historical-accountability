<?php

namespace AgileThemeTools\Form\Element;

use Zend\Form\Element\Select;

class PosterTextTreatmentSelect extends Select {

    function __construct($name = 'o:block[__blockIndex__][o:data][treatment]', $options = [])
    {
        parent::__construct($name, $options);
        $this->setLabel('Text Treatment');
        $this->setValueOptions(count($options) > 0 ? $options: $this->getTextValueOptions());
    }

    public function getTextValueOptions() {
        return([
            'treatment:treatment-1' => 'Large Text',
            'treatment:treatment-2' => 'Mid-size Text'
        ]);
    }
}