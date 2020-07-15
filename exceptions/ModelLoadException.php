<?php
  namespace RamowkaTvRepublika\Exceptions;
  
  use Exception;

  class ModelLoadException extends Exception {
  
    public function __construct(string $modelName, Exception $previous = null) {
      parent::__construct( "Nie udało się wczytać modelu `{$modelName}`.", 0, $previous );
    }
  
  }