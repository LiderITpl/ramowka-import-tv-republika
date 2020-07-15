<?php
  namespace RamowkaTvRepublika;
  
  use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

  class SpreadsheetReadFilter implements IReadFilter {
  
    public function readCell($column, $row, $worksheetName = '') {
      //  Read columns A to C only
      if (in_array($column,range('A','C')))
        return true;
      else
        return false;
    }
    
  }