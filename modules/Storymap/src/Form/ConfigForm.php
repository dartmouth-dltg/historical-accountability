<?php

namespace Storymap\Form;

use Omeka\Form\Element\PropertySelect;
use Storymap\Mvc\Controller\Plugin\StorymapData;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Radio;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;

class ConfigForm extends Form implements TranslatorAwareInterface {

  use TranslatorAwareTrait;

  public function init() {


    $this->add([
      'name' => 'storymap_defaults',
      'type' => Fieldset::class,
      'options' => [
        'label' => 'Default parameters',
        // @translate
        'info' => $this->translate('These parameters are used as default for all storymaps.') // @translate
          . ' ' . $this->translate('They can be overridden in the form of each storymap.'),
        // @translate
      ],
    ]);
    $argsFieldset = $this->get('storymap_defaults');

    $argsFieldset->add([
      'name' => 'item_title',
      'type' => PropertySelect::class,
      'options' => [
        'label' => 'Item title',
        // @translate
        'info' => 'The title field to use when displaying an item on a storymap. Default is "dcterms:title".',
        // @translate
        'empty_option' => 'Select a property...',
        // @translate
        'term_as_value' => TRUE,
      ],
      'attributes' => [
        'class' => 'chosen-select',
        'required' => TRUE,
      ],
    ]);

    $argsFieldset->add([
      'name' => 'item_description',
      'type' => PropertySelect::class,
      'options' => [
        'label' => 'Item description',
        // @translate
        'info' => 'The description field to use when displaying an item on a storymap. Default is "dcterms:description".',
        // @translate
        'empty_option' => 'Select a property...',
        // @translate
        'term_as_value' => TRUE,
      ],
      'attributes' => [
        'class' => 'chosen-select',
        'required' => TRUE,
      ],
    ]);

    $argsFieldset->add([
      'name' => 'item_date',
      'type' => PropertySelect::class,
      'options' => [
        'label' => 'Item date',
        // @translate
        'info' => $this->translate('The date field to use to retrieve and display items on a storymap. Default is "dcterms:date".') // @translate
          . ' ' . $this->translate('Items with empty value for this field will be skipped.'),
        // @translate
        'empty_option' => 'Select a property...',
        // @translate
        'term_as_value' => TRUE,
      ],
      'attributes' => [
        'class' => 'chosen-select',
        'required' => TRUE,
      ],
    ]);
    $argsFieldset->add([
      'name' => 'item_date',
      'type' => PropertySelect::class,
      'options' => [
        'label' => 'Item date',
        // @translate
        'info' => $this->translate('The date field to use to retrieve and display items on a storymap. Default is "dcterms:date".') // @translate
          . ' ' . $this->translate('Items with empty value for this field will be skipped.'),
        // @translate
        'empty_option' => 'Select a property...',
        // @translate
        'term_as_value' => TRUE,
      ],
      'attributes' => [
        'class' => 'chosen-select',
        'required' => TRUE,
      ],
    ]);
    $argsFieldset->add([
      'name' => 'item_location',
      'type' => PropertySelect::class,
      'options' => [
        'info' => 'Latitude and longitude, as comma separated values',
        'label' => 'Item location', // @translate
        'empty_option' => 'Select a property...', // @translate
        'term_as_value' => true,
      ],
      'attributes' => [
        'required' => true,
        'class' => 'chosen-select',
      ],
    ]);
    $argsFieldset->add([
      'name' => 'item_type',
      'type' => PropertySelect::class,
      'options' => [
        'info' => "Slide type - must be either blank, or 'overview'",
        'label' => 'Item type', // @translate
        'empty_option' => 'Select a property...', // @translate
        'term_as_value' => true,
      ],
      'attributes' => [
        'required' => false,
        'class' => 'chosen-select',
      ],
    ]);
    $argsFieldset->add([
      'name' => 'item_contributor',
      'type' => PropertySelect::class,
      'options' => [
        'label' => 'Media credit', // @translate
        'empty_option' => 'Select a property...', // @translate
        'term_as_value' => true,
      ],
      'attributes' => [
        'required' => false,
        'class' => 'chosen-select',
      ],
    ]);

    $map_type = new Element\Select('map_type');
    $map_type->setLabel('Choose map type');
    $map_type->setValueOptions(array(
      'stamen:toner-lite' => 'default',
      'stamen:toner' => 'High contrast black and white',
      'stamen:toner-lines' => 'just the lines (mostly roads) from the Toner style',
      'stamen:toner-labels' => 'just the labels (place names and roads) from the Toner style',
      'stamen:terrain' => 'map with roads as well as some natural features',
      'stamen:watercolor' => 'an artistic representation',
      'osm:standard' => 'maps used by OpenStreetMap',
      'mapbox:map-id' => 'replace map-id with a Mapbox Map ID (requires a MapBox account)',
    ));
    $argsFieldset->add($map_type);
    $argsFieldset->add([
      'name' => 'viewer',
      'type' => 'Textarea',
      'options' => [
        'label' => 'Viewer',
        // @translate
        'info' => $this->translate('Set the default params of the viewer as json, or let empty for the included default.'), // @translate
        // @translate
      ],
      'attributes' => [
        'rows' => 15,
      ],
    ]);

    $inputFilter = $this->getInputFilter();
    $defaultsFilter = $inputFilter->get('storymap_defaults');
    $defaultsFilter->add([
      'name' => 'item_title',
      'required' => TRUE,
    ]);
    $defaultsFilter->add([
      'name' => 'item_description',
      'required' => TRUE,
    ]);
    $defaultsFilter->add([
      'name' => 'item_date',
      'required' => TRUE,
    ]);
    $defaultsFilter->add([
      'name' => 'item_location',
      'required' => TRUE,
    ]);
    $defaultsFilter->add([
      'name' => 'item_type',
      'required' => FALSE,
    ]);
    $defaultsFilter->add([
      'name' => 'item_contributor',
      'required' => FALSE,
    ]);
    $defaultsFilter->add([
      'name' => 'map_type',
      'required' => FALSE,
    ]);
    $defaultsFilter->add([
      'name' => 'viewer',
      'required' => FALSE,
    ]);
  }

  protected function translate($args) {
    $translator = $this->getTranslator();
    return $translator->translate($args);
  }
}
