<?php

namespace Storymap\Form;

use Omeka\Form\Element\PropertySelect;
use Omeka\Form\Element\ResourceSelect;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;

class StorymapBlockForm extends Form {

  public function init() {
    $this->add([
      'name' => 'o:block[__blockIndex__][o:data][args]',
      'type' => Fieldset::class,
      'options' => [
        'label' => 'Parameters', // @translate
      ],
    ]);


    $argsFieldset = $this->get('o:block[__blockIndex__][o:data][args]');
    $map_type = new Element\Select('map_type');
    $map_type->setLabel('Choose map type');
    $map_type->setValueOptions([
      'stamen:toner-lite' => 'default',
      'stamen:toner' => 'High contrast black and white',
      'stamen:toner-lines' => 'just the lines (mostly roads) from the Toner style',
      'stamen:toner-labels' => 'just the labels (place names and roads) from the Toner style',
      'stamen:terrain' => 'map with roads as well as some natural features',
      'stamen:watercolor' => 'an artistic representation',
      'osm:standard' => 'maps used by OpenStreetMap',
      'mapbox:map-id' => 'replace map-id with a Mapbox Map ID (requires a MapBox account)',
    ]);

    $argsFieldset->add([
      'type' => Element\Checkbox::class,
      'name' => 'overview',
      'options' => [
        'label' => 'Use first asset in list as overview?',
        'info' => "The Storymap does not require an overview slide, but it is strongly recommended",
        'use_hidden_element' => TRUE,
        'checked_value' => 'overview',
        'unchecked_value' => 'no',
      ],
      'attributes' => [
        'value' => 'overview',
      ],
    ]);


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
        'info' => "The date field to use to retrieve and display items on a storymap. Default is \"dcterms:date\"",
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
        'info' => 'The date field to use to retrieve and display items on a storymap. Default is "dcterms:date".',
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
        'term_as_value' => TRUE,
      ],
      'attributes' => [
        'required' => TRUE,
        'class' => 'chosen-select',
      ],
    ]);

    $argsFieldset->add([
      'name' => 'item_contributor',
      'type' => PropertySelect::class,
      'options' => [
        'label' => 'Media credit', // @translate
        'empty_option' => 'Select a property...', // @translate
        'term_as_value' => TRUE,
      ],
      'attributes' => [
        'required' => FALSE,
        'class' => 'chosen-select',
      ],
    ]);

    $argsFieldset->add($map_type);
    $argsFieldset->add([
      'name' => 'viewer',
      'type' => 'Textarea',
      'options' => [
        'label' => 'Viewer',
        'info' => 'Set the default params of the viewer as json, or leave empty for the included default.',

      ],
      'attributes' => [
        'rows' => 15,
      ],
    ]);
    $argsFieldset->add([
      'name' => 'gigapixel_image',
      'type' => ResourceSelect::class,
      'attributes' => [
        'value' => NULL,
        'class' => 'chosen-select',
        'data-placeholder' => 'Select an image', // @translate
      ],
      'options' => [
        'label' => 'Select image for gigapixel display', // @translate
        'empty_option' => '',
        'resource_value_options' => [
          'resource' => 'items',
          'query' => [
            'joiner' => 'and',
            'property' => "8",
            'type' => "eq",
            'text' => 'gigapixel',
          ],
          'option_text_callback' => function ($image) {
            $itemType = $image->value('dcterms:type', [
              'type' => 'literal',
              'default' => '',
            ]);
            if ($itemType && $itemType->value() == 'gigapixel') {
              return $image->displayTitle();
            }

          },
        ],
      ],
    ]);
    $argsFieldset->add([
      'name' => 'map_background',
      'type' => 'Zend\Form\Element\Color',
      'options' => [
        'info' => 'Optional background color for Gigapixel maps',
        'label' => 'Select Gigapixel background color', // @translate
        'empty_option' => '', // @translate
        'term_as_value' => TRUE,
      ],
      'attributes' => [
        'required' => FALSE,
        'class' => 'chosen-select',
      ],
    ]);
    $argsFieldset->add([
      'name' => 'attribution',
      'type' => 'text',
      'options' => [
        'info' => 'Attribution for images in Gigapixel storymaps',
        'label' => 'Gigapixel Attribution',
        'empty_option' => '',
        'term_as_value' => TRUE,
      ],
      'attributes' => [
        'required' => FALSE,
        'class' => 'chosen-select',
      ],
    ]);
    $argsFieldset->add([
      'name' => 'tolerance',
      'type' => 'text',
      'options' => [
        'info' => 'Zoomify Tolerance for Gigapixel storymaps - changes potential size of background images.  Default value is .9',
        'label' => 'Gigapixel Tolerance',
        'empty_option' => '',
        'term_as_value' => TRUE,
      ],
      'attributes' => [
        'required' => FALSE,
        'class' => 'chosen-select',
      ],
    ]);


    $inputFilter = $this->getInputFilter();
    $inputFilter->add([
      'name' => 'o:block[__blockIndex__][o:data][args]',
      'required' => FALSE,
    ]);
  }

}
