<?php
  namespace RamowkaTvRepublika\Exceptions;
  
  use Exception;

  class SpreadsheetReadException extends Exception {
    private $type, $documentPath, $worksheetName, $row;
  
    public function __construct(string $type, string $documentPath, $worksheetName, $row, Exception $previous = null) {
      parent::__construct("Wystąpił błąd podczas odczytu pliku excela typu `{$type}`.", 0, $previous );
      $this->type = $type;
      $this->documentPath = $documentPath;
      $this->worksheetName = $worksheetName;
      $this->row = $row;
    }
    
    public function getExceptionType() {
      return $this->type;
    }
    
    public function getDocumentPath() {
      return $this->documentPath;
    }
  
    public function getWorksheetName() {
      return $this->worksheetName;
    }
  
    public function getRow() {
      return $this->row;
    }
  
  }