<?php
/**
 * User: idgu
 * Date: 29.11.2017
 * Time: 09:32
 */

namespace App;

class Form {




    /**
     * @var String Name in error message.
     */
    private $_name;

    private $_inputName;


    private $_options = array();

	public function __construct($name, $input_name, $options){
        $this->_inputName = trim($input_name);
        $this->_name = $name;
        $this->_options = $options;
    }


    public function getOptions()    { return $this->_options; }
    public function getInputName() { return $this->_inputName;}
	public function getName()       { return $this->_name; }

}