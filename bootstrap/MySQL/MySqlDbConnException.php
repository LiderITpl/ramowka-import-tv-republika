<?php
  namespace RamowkaTvRepublika\Bootstrap\MySQL;
  
  use Exception;

  class MySqlDbConnException extends Exception {
  
    public function __construct($message="", $code = 0, Exception $previous = null) {
    
      parent::__construct(
        "Nie udało się połączyć z bazą: ".$message,
        $code,
        $previous
      );
    }
  
  }