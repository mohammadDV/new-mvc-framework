<?php

namespace System\Request\Traits;

trait HasRunValidation
{
    
    /**
     * Error redirect method to redirect the user to the previous page if there are errors
     * @return array|string
     */
    protected function errorRedirect()
    {
        if ($this->errorExist === false) {
            return $this->request;
        }
        return back();
    }

    /**
     * Check first error method to check if the error exists for the given name
     * @param string $name
     * @return bool
     */
    private function checkFirstError(string $name) : bool
    {
        if (!error_exist($name) && !in_array($name, $this->errorVariablesName)) {
            return true;
        }
        return false;
    }

    /**
     * Check field exist method to check if the field exists in the request
     * @param string $name
     * @return bool
     */
    private function checkFieldExist(string $name) : bool
    {
        return (isset($this->request[$name]) && !empty($this->request[$name])) ? true : false;
    }

    /**
     * Check file exist method to check if the file exists in the request
     * @param string $name
     * @return bool
     */
    private function checkFileExist(string $name) : bool
    {
       if (isset($this->files[$name]['name']) && !empty($this->files[$name]['name'])) {
            return true;
       }
       return false;
    }

    /**
     * Set error method to set the error for the given name
     * @param string $name
     * @param string $errorMessage
     * @return void
     */
    private function setError(string $name, string $errorMessage) : void
    {
        array_push($this->errorVariablesName, $name);
        error($name, $errorMessage);
        $this->errorExist = true;
    }

}