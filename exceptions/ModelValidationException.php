<?php
  namespace RamowkaTvRepublika\Exceptions;
  
  use Exception;

  class ModelValidationException extends Exception {
  
    public function __construct(string $message, Exception $previous = null) {
      parent::__construct( $message, 0, $previous );
    }
  
  }