<?php
  namespace RamowkaTvRepublika;
  
  use Exception;
  use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
  use RamowkaTvRepublika\Exceptions\SpreadsheetReadException;
  use RamowkaTvRepublika\Models\ScheduleItem;

  class WorksheetReader {
  
    /**
     * @param string $documentLocation
     * @param Worksheet $worksheet
     * @return int
     * @throws SpreadsheetReadException
     */
    public static function importWorksheet(string $documentLocation, Worksheet $worksheet) {
      $iteratedRows = 0;
      $worksheetTitle = str_replace('  ', ' ', trim($worksheet->getTitle()));
  
      try {
        if(strpos($worksheetTitle, ' ') === false)
          throw new Exception('Nieprawidłowy tutuł arkusza. Tytuł powinien wyglądać następująco: "sobota 11.07".');
        
        $titleParts = explode(' ', $worksheetTitle);
        //$dayName = $titleParts[0];
        $shortenedDate = $titleParts[1];
        $fullDate = $shortenedDate . "." . date("Y");
  
        $firstCellDataType = $worksheet->getColumnIterator()->current()->getCellIterator()->current()->getDataType();
        if($firstCellDataType !== 'n')
          return 0; // Koniec? Następny arkusz jest pusty, bądź posiada nieprawidłowe dane
        
        foreach ($worksheet->getRowIterator() as $row) {
          $cellIterator = $row->getCellIterator();
          $scheduleItem = new ScheduleItem();
          $rowIndex = 0;
          try {
            foreach ($cellIterator as $cell) {
              switch($rowIndex) {
                case 0: {
                  $scheduleItem->time = $cell->getFormattedValue();
                  break;
                }
                case 1: {
                  $scheduleItem->title = $cell->getValue();
                  break;
                }
                case 2: {
                  $scheduleItem->label = $cell->getValue();
                  break;
                }
              }
              $rowIndex++;
              if($rowIndex > 2)
                break;
            }
            
            if(!self::isSchedulePartiallyFulfilled($scheduleItem))
              return $iteratedRows;
  
            $scheduleItem->date = $fullDate;
            $scheduleItem->timestamp = strtotime($fullDate . ' ' . $scheduleItem->time);
            
            if($scheduleItem->timestamp === false)
              $scheduleItem->timestamp = null;
        
            $scheduleItem->save();
            $iteratedRows++;
          } catch(Exception $e) {
            throw new SpreadsheetReadException(
                SpreadsheetReadExceptionType::SINGLE_ROW_ERROR,
                $documentLocation,
                $worksheet->getTitle(),
                $rowIndex,
                $e
            );
          }
        }
      } catch(SpreadsheetReadException $e) {
        throw $e;
      } catch(Exception $e) {
        throw new SpreadsheetReadException(
            SpreadsheetReadExceptionType::SINGLE_WORKSHEET_ERROR,
            $documentLocation,
            $worksheet->getTitle(),
            null, $e
        );
      }
      
      return $iteratedRows;
    }
    
    private static function isSchedulePartiallyFulfilled(ScheduleItem $item) {
      return isset($item->time) && isset($item->title);
    }
  
  }