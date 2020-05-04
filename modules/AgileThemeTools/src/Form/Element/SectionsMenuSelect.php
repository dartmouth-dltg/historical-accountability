<?php

namespace AgileThemeTools\Form\Element;

use Zend\Form\Element\Select;
use AgileThemeTools\Controller\SectionManager;

class SectionsMenuSelect extends Select {

    protected $sectionManager;

    /**
     * SectionsMenuSelect constructor.
     * @param string $name
     * @param array $options
     */

    function __construct($name = 'o:block[__blockIndex__][o:data][section]', $options = [])
    {
        parent::__construct($name,$options);
        $this->setLabel('Select a Section');

    }

    /**
     * @param SectionManager $sectionManager
     */
    public function setSectionManager(SectionManager $sectionManager)
    {
        $this->sectionManager = $sectionManager;
    }

    /**
     * @return SectionManager
     */
    public function getSectionmanager()
    {
        return $this->sectionManager;
    }

    public function setSectionOptions($sections = []) {

        //$index = $this->sectionManager->indexAction();

        $this->setValueOptions($sections);

        //$this->setValueOptions(count($options) > 0 ? $options: $this->getRegionValueOptions());

    }


}