<?php

/**
 * Wrapper class to handle certain generic structures for our Facebook app
 *
 * @category Database Access
 * @package fbAppDb
 * @author Ben Freke <benfreke@gmail.com> 
 * @copyright Copyright (c) 2012
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @version 0.1
 */


class fbAppDb extends MysqliDB
{    
    /**
     * The fields to tbe inserted into the entries folder
     * You can provide a default value, otherwise these are updated with the postvalues
     * 
     * @var array Key Value pair of field keys to values     * 
     */
    protected $entriesData = array(        
        'fullname' => '',
        'email' => '',
        'entered' => ''
    );
    
    /**
     * The fields to tbe inserted into the entries folder
     * You can provide a default value, otherwise these are updated with the postvalues
     * 
     * @var array Key Value pair of field keys to values     * 
     */
    protected $entriesExtraData = array(
        
    );

    /**
     * A list of fields that are checkboxes, so we can test for whether they are set or not
     * @var array Name of checkbox fields, so they can be set to true or false 
     */
    protected $checkboxes = array();
    
    /**
     * A list of columns that should default to an empty string
     * @var array  
     */
    protected $defaultEmptyString = array();
    
    /**
     * Insert linked date into the entries fields
     * @param array $postData The data supplied, normally via a form post
     * @return int The entry id
     */
    public function insertEntry($postData)
    {
        // Prepare my data for the entry insert
        $entryData = $this->getValuesFromSubmission($this->entriesData, $postData);
                
        $this->insert('entries', $entryData);
        $insertId = $this->getInsertId();
        
        // Prepare data for the entries_extra insert
        $entryExtraData = $this->getValuesFromSubmission($this->entriesExtraData, $postData);
        // Make sure it is linked correctly
        $entryExtraData['entries_id'] = $insertId;
        $this->insert('entries_extra', $entryExtraData);
        
        // return the id in case I need it for something
        return $insertId;
    }
    
    /**
     * Given a list of possible fields to insert, copy over in the correct format the values
     *  from the provided data
     * @todo Add error checking so we don't get exceptions
     * @todo Proper type checking
     * @param array $possibleFields
     * @param array $providedValues
     */
    protected function getValuesFromSubmission($possibleFields, $providedValues)
    {
        $insertData = array();
        // For each of our possible fields, try and provide a value
        foreach($possibleFields as $key => $value) {
            /*
             * This should change to a switch, based on the type of input it is
             * Get that from another function, not within this one!
             */
            if (!in_array($key, $this->checkboxes)) {
                // Not a checkbox
                if (isset($providedValues[$key]) && !empty($providedValues[$key])) {
                    // We have been passed a value to use
                    $insertData[$key] = $providedValues[$key];
                } else {
                    // No value supplied, check for default
                    if (!empty($value) || in_array($key, $this->defaultEmptyString)) {
                        // Use the default
                        $insertData[$key] = $value;
                    } else {
                        // We don't want to include this value in our insert
                    }
                }
            } else {
                // this is a checkbox, and we use smallints to record this
                $insertData[$key] = (isset($providedValues[$key])) ? 1 : 0;
            }            
        }
        return $insertData;
    }
    
    /**
     * Sets the extra fields that we're looking for. These match exactly to the columns in the database
     * You have set up the database, right? 
     * @param string $fields
     * @param array $defaults
     */
    public function setExtraFields($fields, $defaults = null)
    {
        // No error checking at this stage
        foreach(explode(',', $fields) as $fieldName) {
            $this->entriesExtraData[$fieldName] = '';
        }
        // Just to be absolutely sure
        unset($fieldName);
        
        // Try and set some defaults
        if (!empty($defaults)) {
            // Set default values. Not being used at present
            foreach($defaults as $fieldName => $defaultValue) {
                // We have to check both tables, as we don't know where it is
                if (isset($this->entriesData[$fieldName])) {
                    $this->entriesData[$fieldName] = $defaultValue;
                }
                if (isset($this->entriesExtraData[$fieldName])) {
                    $this->entriesExtraData[$fieldName] = $defaultValue;
                }
            }
        }
    }
    
    /**
     * Dynamically define my field types for later use
     * @param array $fieldValues
     */
    public function setFieldTypes($fieldValues)
    {
        foreach($fieldValues as $type => $values) {
            // Probably don't need the switch, but I'm keeping it just in case
            //  we want to know which fields are which types in the future
            switch($type) {
                case 'checkboxes':
                    $this->$type = explode(',', $values);
                    break;
            }
        }
    }
}

?>
