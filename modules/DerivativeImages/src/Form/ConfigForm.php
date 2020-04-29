<?php
namespace DerivativeImages\Form;

use Doctrine\DBAL\Connection;
use Zend\Form\Element;
use Zend\Form\Form;

class ConfigForm extends Form
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    public function init()
    {
        $this->add([
            'name' => 'ingesters',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Ingesters to process', // @translate
                'empty_option' => 'All ingesters', // @translate
                'value_options' => $this->listIngesters(),
            ],
            'attributes' => [
                'id' => 'ingesters',
                'class' => 'chosen-select',
                'multiple' => true,
                'placeholder' => 'Select ingesters to process', // @ translate
                'data-placeholder' => 'Select ingesters to process', // @ translate
            ],
        ]);

        $this->add([
            'name' => 'renderers',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Renderers to process', // @translate
                'empty_option' => 'All renderers', // @translate
                'value_options' => $this->listRenderers(),
            ],
            'attributes' => [
                'id' => 'renderers',
                'class' => 'chosen-select',
                'multiple' => true,
                'placeholder' => 'Select renderers to process', // @ translate
                'data-placeholder' => 'Select renderers to process', // @ translate
            ],
        ]);

        $this->add([
            'name' => 'media_types',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Media types to process', // @translate
                'empty_option' => 'All media types', // @translate
                'value_options' => $this->listMediaTypes(),
            ],
            'attributes' => [
                'id' => 'media_types',
                'class' => 'chosen-select',
                'multiple' => true,
                'placeholder' => 'Select media types to process', // @ translate
                'data-placeholder' => 'Select media types to process', // @ translate
            ],
        ]);

        $this->add([
            'name' => 'media_ids',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Media ids', // @translate
            ],
            'attributes' => [
                'id' => 'media_ids',
                'placeholder' => '2-6 8 38-52 80-', // @ translate
            ],
        ]);

        $this->add([
            'name' => 'process',
            'type' => Element\Submit::class,
            'options' => [
                'label' => 'Run in background', // @translate
            ],
            'attributes' => [
                'id' => 'process',
                'value' => 'Process', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'ingesters',
            'required' => false,
        ]);
        $inputFilter->add([
            'name' => 'renderers',
            'required' => false,
        ]);
        $inputFilter->add([
            'name' => 'media_types',
            'required' => false,
        ]);
    }

    /**
     * @return array
     */
    protected function listIngesters()
    {
        $sql = 'SELECT DISTINCT(ingester) FROM media ORDER BY ingester';
        $stmt = $this->getConnection()->query($sql);
        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        return ['' => 'All ingesters'] // @translate
            + array_combine($result, $result);
    }

    /**
     * @return array
     */
    protected function listRenderers()
    {
        $sql = 'SELECT DISTINCT(renderer) FROM media ORDER BY renderer';
        $stmt = $this->getConnection()->query($sql);
        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        return ['' => 'All renderers'] // @translate
            + array_combine($result, $result);
    }

    /**
     * @return array
     */
    protected function listMediaTypes()
    {
        $sql = 'SELECT DISTINCT(media_type) FROM media WHERE media_type IS NOT NULL AND media_type != "" ORDER BY media_type';
        $stmt = $this->getConnection()->query($sql);
        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        return ['' => 'All media types'] // @translate
            + array_combine($result, $result);
    }

    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
