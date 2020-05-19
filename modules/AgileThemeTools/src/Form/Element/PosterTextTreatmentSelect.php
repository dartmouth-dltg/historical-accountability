<?php

namespace AgileThemeTools\Form\Element;

use Zend\Form\Element\Select;

class PosterTextTreatmentSelect extends Select {

    function __construct($name = 'o:block[__blockIndex__][o:data][treatment]', $options = [])
    {
        parent::__construct($name, $options);
        $this->setLabel('Text Treatment');
        $this->setValueOptions(count($options) > 0 ? $options: $this->getTextgValueOptions());
    }

    public function getTextgValueOptions() {
        return([
            'treatment:treatment1' => 'Large Text',
            'treatment:text-treatment2' => 'Mid-size Text'
        ]);
    }
}