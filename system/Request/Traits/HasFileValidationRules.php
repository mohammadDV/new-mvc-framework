<?php

namespace System\Request\Traits;


trait HasFileValidationRules
{
    /**
     * File validation method to validate the file
     * @param string $name
     * @param array $ruleArray
     * @return void
     */
    protected function fileValidation(string $name, array $ruleArray) :void {
        
        foreach ($ruleArray as $rule) {
            if ($rule == "required") {
                $this->fileRequired($name);
            } elseif (strpos($rule, "mimes:") === 0) {
                $rule = str_replace('mimes:', "", $rule);
                $rule = explode(',', $rule);
                $this->fileType($name, $rule);
            } elseif (strpos($rule, "max:") === 0) {
                $rule = str_replace('max:', "", $rule);
                $this->maxFile($name, $rule);
            } elseif (strpos($rule, "min:") === 0) {
                $rule = str_replace('min:', "", $rule);
                $this->minFile($name, $rule);
            }
        }
    }

    /**
     * File required method to validate the file is required
     * @param string $name
     * @return void
     */
    protected function fileRequired(string $name) : void {
        
        if(!isset($this->files[$name]['name']) || 
            empty($this->files[$name]['name']) && 
            $this->checkFirstError($name)){
                
            $this->setError($name, "$name is required");
        }
    }

    /**
     * File type method to validate the file type
     * @param string $name
     * @param array $typesArray
     * @return void
     */
    protected function fileType(string $name, array $typesArray) : void {
    
        if($this->checkFirstError($name) && $this->checkFileExist($name)){
            $currentFileType = explode('/', $this->files[$name]['type'])[1];
            if(!in_array($currentFileType, $typesArray)){
                $this->setError($name, "$name type must be " . implode(', ', $typesArray));
            }
        }
    }

    /**
     * Max file method to validate the file size
     * @param string $name
     * @param int $size
     * @return void
     */
    protected function maxFile(string $name, int $size) : void {
    
        $size = $size * 1024;
        if ($this->checkFirstError($name) && $this->checkFileExist($name)) {
            if($this->files[$name]['size'] > $size){
                $this->setError($name, "$name size must be lower than " . ($size / 1024) . " kb");
            }
        }
    }

    
    /**
     * Min file method to validate the file size
     * @param string $name
     * @param int $size
     * @return void
     */
    protected function minFile(string $name, int $size) : void {
        
        $size = $size * 1024;
        if ($this->checkFirstError($name) && $this->checkFileExist($name)) {
            if($this->files[$name]['size'] < $size){
                $this->setError($name, "$name size must be upper than " . ($size / 1024) . " kb");
            }
        }
    }

}