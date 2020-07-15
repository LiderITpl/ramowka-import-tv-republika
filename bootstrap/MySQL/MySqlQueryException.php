<?php
  namespace RamowkaTvRepublika\Bootstrap\MySQL;
  
  use Exception;

  class MySqlQueryException extends Exception {
  
    public function __construct($message="", $code = 0, Exception $previous = null) {
    
      parent::__construct(
        "Odpytywanie bazy danych nie powiodło się: ".$message,
        $code,
        $previous
      );
    }
    
  }