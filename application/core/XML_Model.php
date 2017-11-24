<?php

/**
 * XML-persisted collection.
 * 
 * @author		JLP
 * @copyright           Copyright (c) 2010-2017, James L. Parry
 * ------------------------------------------------------------------------
 */
class XML_Model extends Memory_Model
{
    private $_set;
    private $_record;
//---------------------------------------------------------------------------
//  Housekeeping methods
//---------------------------------------------------------------------------

	/**
	 * Constructor.
	 * @param string $origin Filename of the CSV file
	 * @param string $keyfield  Name of the primary key field
	 * @param string $entity	Entity name meaningful to the persistence
     * @param string $tag  the tag name for an sigle record in the xml file 
	 */
	function __construct($origin = null, $keyfield = 'id', $entity = null)
	{
		parent::__construct();

		// guess at persistent name if not specified
		if ($origin == null)
			$this->_origin = get_class($this);
		else
			$this->_origin = $origin;

		// remember the other constructor fields
		$this->_keyfield = $keyfield;
		$this->_entity = $entity;

		// start with an empty collection
		$this->_data = array(); // an array of objects
		$this->_fields = array(); // an array of strings
		// and populate the collection
		$this->load();
	}

	/**
	 * Load the collection state appropriately, depending on persistence choice.
	 * OVER-RIDE THIS METHOD in persistence choice implementations
	 */
	protected function load()
	{
        $doc = new DOMDocument();

        $doc->preserveWhiteSpace = false; // otherwise the whitespaces will generate "#text" nodes in the node list

        // load xml content from file 
        $doc->load($this->_origin);

        // read the structure information from description part of the xml file
        $this->_set = $doc->getElementsByTagName('set_name')->item(0)->nodeValue;
        $this->_record = $doc->getElementsByTagName('record_name')->item(0)->nodeValue;

        // read fileds infomation from the description part of the xml file
        foreach ($doc->getElementsByTagName('field') as $field) 
        {
            $this->_fields [] = $field->nodeValue;
        }

        // read actual data from the data part 
        foreach ($doc->getElementsByTagName($this->_record) as $recordNode)
        {
            $record = new stdClass();
            foreach($recordNode->childNodes as $propertyNode)
            {
                $record->{$propertyNode->tagName} = $propertyNode->nodeValue;
            }

            $this->_data []= $record;
        }

		$this->reindex();
	}

	/**
	 * Store the collection state appropriately, depending on persistence choice.
	 * OVER-RIDE THIS METHOD in persistence choice implementations
	 */
	protected function store()
	{
		// rebuild the keys table
		$this->reindex();
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;

        // create root node
        $root = $doc->createElement('records'); 

        // store the meta data (description about the data)
        $description = $doc->createElement('description');
        $setDescription = $doc->createElement('set_name', $this->_set);
        $recordDescription = $doc->createElement('record_name', $this->_record);
        $fieldsDescription = $doc->createElement('fields');

        foreach ($this->_fields as $field) 
        {
            $fieldsDescription->appendChild($doc->createElement('field', $field)); 
        }
        $description->appendChild($setDescription);
        $description->appendChild($recordDescription);
        $description->appendChild($fieldsDescription);

        $root->appendChild($description);

        $dataNode = $doc->createElement($this->_set);

        // store the actual data
        $data = $this->_data;

        foreach ($this->_data as $record)
        {
            foreach ($this->_data as $record)
            {
                $item = $doc->createElement($this->_record);
                foreach ($record as $key => $value) 
                {
                    //var_dump($key, $value);
                    $property = $doc->createElement($key, $value);
                    $item->appendChild($property);
                }
                $dataNode->appendChild($item);
            }
            //var_dump($root);
        }
        $root->appendChild($dataNode);
        $doc->appendChild($root);

        //var_dump($doc->saveXML());
        $doc->save($this->_origin);

	}
}
