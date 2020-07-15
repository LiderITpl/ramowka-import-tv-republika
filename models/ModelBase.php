<?php
  namespace RamowkaTvRepublika\Models;
  
  use RamowkaTvRepublika\Bootstrap\MySQL\MySqlQueryException;
  use RamowkaTvRepublika\Exceptions\ModelValidationException;

  abstract class ModelBase {
    protected $attrsKeys;
    protected $excludedValidationFields = ['id'];
  
    public function __construct(array $attrsKeys, array $attributes=[]) {
      $this->attrsKeys = $attrsKeys;
      $this->fill($attributes);
    }
  
    public function attributes() {
      return array_filter(
          get_object_vars($this),
          function ($key) {
            return in_array($key, $this->attrsKeys, true);
          },
          ARRAY_FILTER_USE_KEY
      );
    }
  
    public function fill(array $attributes) {
      foreach($attributes as $attrKey => $attrValue) {
        if(in_array($attrKey, $this->attrsKeys, true)) {
          $this->{$attrKey} = mb_convert_encoding($attrValue, 'UTF-8');
        }
      }
    }
  
    /**
     * @throws ModelValidationException
     * @throws MySqlQueryException
     */
    public function save() {
      if(!isset($this->id))
        $this->insert();
      else
        $this->update();
    }
  
    /**
     * @throws ModelValidationException
     * @throws MySqlQueryException
     */
    protected abstract function insert();
  
    /**
     * @throws ModelValidationException
     * @throws MySqlQueryException
     */
    protected abstract function update();
  
    /**
     * @return array
     * @throws ModelValidationException
     */
    protected function validateAttrs() {
      $thisAttrs = $this->attributes();
      $requiredAttrsKeys = $this->attrsKeys;
      if(sizeof($this->excludedValidationFields) > 0) {
        $requiredAttrsKeys = array_filter(
            $requiredAttrsKeys,
            function($key) {
              if(in_array($key, $this->excludedValidationFields, true))
                return false;
              else
                return true;
            }
        );
      }
      
      foreach($requiredAttrsKeys as $attrKey) {
        if(!isset($thisAttrs[$attrKey]))
          throw new ModelValidationException("Missing key: `{$attrKey}`.");
      }
      return $thisAttrs;
    }
    
  }