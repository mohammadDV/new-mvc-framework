<?php

declare(strict_types=1);

namespace System\Request;

use System\Request\Traits\HasFileValidationRules;
use System\Request\Traits\HasRunValidation;
use System\Request\Traits\HasValidationRules;

class Request
{
    use HasValidationRules, HasFileValidationRules, HasRunValidation;

    /**
     * Error exist storage for request attributes.
     * 
     * @var bool
     */
    protected $errorExist = false;

    /**
     * Request storage for request attributes.
     * 
     * @var array|null
     */
    protected $request;

    /**
     * Files storage for request attributes.
     * 
     * @var array|null
     */
    protected $files = null;
    
    /**
     * Error variables name storage for request attributes.
     * 
     * @var array
     */
    protected $errorVariablesName = [];
    
    /**
     * Dynamic properties storage for request attributes.
     * 
     * @var array
     */
    protected array $attributes = [];

    /**
     * Constructor method to initialize the request object
     */
    public function __construct()
    {
        if(isset($_POST)) {
            $this->postAttributes();
        }
        
        if(!empty($_FILES)) {
            $this->files = $_FILES;
        }
        
        $rules = $this->rules();
        empty($rules) ? : $this->run($rules);
        $this->errorRedirect();
    }

    /**
     * Rules method to define the validation rules for the request
     * @return array
     */
    protected function rules() : array
    {
        return [];
    }

    /**
     * Run method to run the validation rules for the request
     * @param array $rules
     * @return void
     */
    protected function run($rules) :void  {
        foreach($rules as $att => $values){
            
            $ruleArray = explode('|', $values);
            
            if(in_array('file', $ruleArray))
            {
                unset($ruleArray[array_search('file', $ruleArray)]);
                $this->fileValidation($att, $ruleArray);
                
            } elseif (in_array('number', $ruleArray)) {
                $this->numberValidation($att, $ruleArray);
                
            } else {
                $this->normalValidation($att, $ruleArray);
                
            }
        }
    }

    /**
     * File method to get the file from the request
     * @param string $name
     * @return array|false
     */
    public function file($name) : array|false {
        return isset($this->files[$name]) ? $this->files[$name] : false;
    }

    /**
     * Post attributes method to get the post attributes from the request
     * @return void
     */
    protected function postAttributes() :void {
        
        foreach($_POST as $key => $value) {
            $this->attributes[$key] = trim($value);
            $this->request[$key] = trim($value);
        }
    }
    
    /**
     * Magic method to get dynamic properties.
     * 
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }
    
    /**
     * Magic method to set dynamic properties.
     * 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }
    
    /**
     * Magic method to check if dynamic property exists.
     * 
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }
    
    /**
     * Magic method to unset dynamic property.
     * 
     * @param string $name
     * @return void
     */
    public function __unset(string $name): void
    {
        unset($this->attributes[$name]);
    }

    /**
     * All method to get all the request attributes
     * @return array
     */
    public function all() : array {
       return $this->request;
    }

}