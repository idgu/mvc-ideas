<?php
/**
 * User: idgu
 * Date: 30.11.2017
 * Time: 13:48
 */

namespace App;

use Core\Model;

class Validator
{
    /**
     * @var Form Array with Form instances
     */
    private $_elements = array();

    private $_errors = array();

    private $_currentElement;

    private $_validateObj;

    public function __construct($validateObj){

        $this->_validateObj = $validateObj;
    }

    public function add(Form $input)
    {
        $this->_elements[]= $input;
    }


    public function validate(){

        foreach ($this->_elements as $key => $element) {

            if (isset($element->getOptions()['notRequired']) &&  $this->_validateObj->{$element->getInputName()} === '') {
                continue;
            }

            $this->_currentElement = $element;

            foreach($element->getOptions() as  $option => $value){


                switch ($option) {

                    case 'maxlength':
                        $this->maxLength($value);
                        break;

                    case 'minlength':
                        $this->minLength($value);
                        break;

                    case 'oneLetter':
                        $this->oneLetter();
                        break;

                    case 'oneNumber':
                        $this->oneNumber();
                        break;

                    case 'email':
                        $this->email();
                        break;

                    case 'existDb':
                        $this->existDb($value);
                        break;

                    case 'notExistDb':
                        $this->notExistDb($value);
                        break;

                    case 'equals':
                        $this->equals($value);
                        break;
                }
            }
        }
    }



    public function getErrors(){
        return $this->_errors;
    }


    public function getElementData(){
        return $this->_validateObj->{$this->_currentElement->getInputName()};
    }


    public function getElementName(){
        return $this->_currentElement->getName();
    }



    private function equals($value){
        if (isset($value)) {
            if (!($this->getElementData() == $this->_validateObj->{$value->getInputName()})) {
                $this->_errors[] = 'Field "'.$this->getElementName(). '" oraz "' . $value->getName().'" nie jest takie same!';
            }
        }
    }


    private function maxLength($value){
        if (strlen($this->getElementData()) > $value) {
            $this->_errors[] = 'Field "'.$this->getElementName(). '" can has (max '. $value . ' zn.)';
        }
    }


    private function minLength($value){
        if (strlen($this->getElementData()) < $value) {
            $this->_errors[] = 'Field "'.$this->getElementName(). '" needs at least  (min '. $value . ' ch.)';
        }
    }


    private function oneLetter()
    {
        if (preg_match('/.*[a-z]+.*/i', $this->getElementData()) == 0) {
            $this->_errors[] = $this->getElementName().' needs at least one letter';
        }
    }


    private function oneNumber()
    {
        if (preg_match('/.*\d+.*/i', $this->getElementData()) == 0) {
            $this->_errors[] = $this->getElementName().' needs at least one number';
        }
    }


    private function email()
    {
        if (filter_var($this->getElementData(), FILTER_VALIDATE_EMAIL) === false) {
            $this->_errors[] = 'Invalid email';
        }
    }


    private function existDb($value){
        $path = explode('/', $value);
        $where = [$path[1], '=', $this->getElementData()];

        $record = Model::valueExists($path[0], $where);


        if (!$record) {
            $this->_errors[] = 'Wartość pola "'. $this->getElementName(). '" nie istnieje w bazie!' ;
        }
    }


    private function notExistDb($value){
        $path = explode('/', $value);
        $where = [$path[1], '=', $this->getElementData()];

        $record = Model::valueExists($path[0], $where);


        if ($record) {
            $this->_errors[] = 'Wartość pola "'. $this->getElementName(). '" istnieje w bazie!' ;
        }
    }
}