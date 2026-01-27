<?php

namespace System\Request\Traits;

use System\Database\Database;

trait HasValidationRules
{

    /**
     * Normal validation method to validate the normal fields
     * @param string $name
     * @param array $ruleArray
     * @return void
     */
    public function normalValidation(string $name, array $ruleArray) : void
    {
        foreach ($ruleArray as $rule) {
            if ($rule == 'required') {
                $this->required($name);                
            } elseif (strpos($rule, 'max:') === 0) {
                $rule = str_replace('max:', "", $rule);
                $this->maxStr($name, $rule);                
            } elseif (strpos($rule, 'min:') === 0) {
                $rule = str_replace('min:', "", $rule);
                $this->minStr($name, $rule);
            } elseif (strpos($rule, 'exists:') === 0) {
                $rule = str_replace('exists:', "", $rule);
                $rule = explode(',', $rule);
                $key = isset($rule[1]) == false ? null : $rule[1];
                $this->existsIn($name, $rule[0], $key);
            } elseif (strpos($rule, 'unique:') === 0) {
                $rule = str_replace('unique:', "", $rule);
                $rule = explode(',', $rule);
                $key = isset($rule[1]) == false ? null : $rule[1];
                $this->unique($name, $rule[0], $key);
            } elseif ($rule == 'confirmed') {
                $this->confirm($name);
            } elseif ($rule == 'email') {                
                $this->email($name);
            } elseif ($rule == 'date') {
                $this->date($name);
            }
        }
    }

    /**
     * Number validation method to validate the number fields
     * @param string $name
     * @param array $ruleArray
     * @return void
     */
    public function numberValidation(string $name, array $ruleArray) : void
    {
        foreach($ruleArray as $rule){
            if($rule == 'required') {
                $this->required($name);
            } elseif(strpos($rule, 'max:') === 0) {
                $rule = str_replace('max:', "", $rule);
                $this->maxNumber($name, $rule);
            } elseif(strpos($rule, "min:") === 0) {
                $rule = str_replace('min:', "", $rule);
                $this->minNumber($name, $rule);
            } elseif(strpos($rule, 'exists:') === 0) {
                $rule = str_replace('exists:', "", $rule);
                $rule = explode(',', $rule);
                $key = isset($rule[1]) == false ? null : $rule[1];
                $this->existsIn($name, $rule[0], $key);
            } elseif($rule == 'number') {
                $this->number($name);
            }
        }
    }


    /**
     * Max string method to validate the string length
     * @param string $name
     * @param int $count
     * @return void
     */
    protected function maxStr(string $name, int $count) : void
    {
        if($this->checkFieldExist($name)){
            if (strlen($this->request[$name]) >= $count && $this->checkFirstError($name)){
                $this->setError($name, "$name max length equal or lower than $count character");
            }
        }
    }

    /**
     * Min string method to validate the string length
     * @param string $name
     * @param int $count
     * @return void
     */
    protected function minStr(string $name, int $count) : void
    {
        if($this->checkFieldExist($name)){
            if (strlen($this->request[$name]) <= $count && $this->checkFirstError($name)){
                $this->setError($name, "$name min length equal or upper than $count character");
            }
        }
    }

    /**
     * Max number method to validate the number
     * @param string $name
     * @param int $count
     * @return void
     */
    protected function maxNumber(string $name, int $count) : void
    {
        if($this->checkFieldExist($name)){
            if ($this->request[$name] >= $count && $this->checkFirstError($name)){
                $this->setError($name, " $name max number equal or lower than $count character");
            }
        }
    }

    /**
     * Min number method to validate the number
     * @param string $name
     * @param int $count
     * @return void
     */
    protected function minNumber(string $name, int $count) : void
    {
        if($this->checkFieldExist($name)){
            if ($this->request[$name] <= $count && $this->checkFirstError($name)){
                $this->setError($name, "$name min number equal or upper than $count character");
            }
        }
    }

    /**
     * Required method to validate the required fields
     * @param string $name
     * @return void
     */
    protected function required(string $name) : void
    {
        if((!isset($this->request[$name]) || $this->request[$name] === '') && $this->checkFirstError($name)){
            $this->setError($name,"$name is required");
        }
    }

    /**
     * Number method to validate the number
     * @param string $name
     * @return void
     */
    protected function number(string $name) : void
    {
        if($this->checkFieldExist($name)){
            if(!is_numeric($this->request[$name]) && $this->checkFirstError($name))
            {
                $this->setError($name,"$name must be number format");
            }
        }
    }

    /**
     * Date method to validate the date
     * @param string $name
     * @return void
     */
    protected function date(string $name) : void
    {
        if($this->checkFieldExist($name)){
           if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$this->request[$name]) && $this->checkFirstError($name)){
            $this->setError($name,"$name must be date format");
           }
        }
    }

    /**
     * Email method to validate the email
     * @param string $name
     * @return void
     */
    protected function email(string $name) : void {
        if($this->checkFieldExist($name)){
            if(!filter_var($this->request[$name], FILTER_VALIDATE_EMAIL) && $this->checkFirstError($name))
            {
                $this->setError($name,"$name must be email format");
            }
        }
    }

    /**
     * Exists in method to validate the exists in the database
     * @param string $name
     * @param string $table
     * @param string $field
     * @return void
     */
    public function existsIn(string $name, string $table, string $field = "id") : void {
        if($this->checkFieldExist($name)){
            if($this->checkFirstError($name)){
                $value = $this->$name;
                $sql = "SELECT COUNT(*) FROM $table WHERE $field = ?";
                $statement = Database::getInstance()->query($sql, [$value]);
                $result = $statement->fetchColumn();
                if($result == 0 || $result === false){
                    $this->setError($name,"$name not already exist");
                }
            }
        }
    }


    /**
     * Unique method to validate the unique in the database
     * @param string $name
     * @param string $table
     * @param string $field
     * @return void
     */
    public function unique(string $name, string $table, string $field = "id") : void {
        if($this->checkFieldExist($name)){
            if($this->checkFirstError($name)){
                $value = $this->$name;
                $sql = "SELECT COUNT(*) FROM $table WHERE $field = ?";
                $statement = Database::getInstance()->query($sql, [$value]);
                $result = $statement->fetchColumn();
                if($result != 0){
                    $this->setError($name,"$name must be unique");
                }
            }
        }
    }

    /**
     * Confirm method to validate the confirm fields
     * @param string $name
     * @return void
     */
    protected function confirm(string $name) : void {
        if ($this->checkFieldExist($name)) {
            $fieldName = "confirm_".$name;
            if(!isset($this->$fieldName)){
                $this->setError($name, " $name $fieldName not exist");
            }
            elseif($this->$fieldName != $this->$name){
                $this->setError($name, "$name confirmation does not match");
            }
        }
    }
        
}