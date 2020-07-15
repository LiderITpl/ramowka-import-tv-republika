<?php
  namespace RamowkaTvRepublika;
  
  use PhpOffice\PhpSpreadsheet\IOFactory;
  use PhpOffice\PhpSpreadsheet\Reader\IReader;
  use RamowkaTvRepublika\Bootstrap\MySQL\MySqlSingleton;
  use Exception;
  use RamowkaTvRepublika\Exceptions\SpreadsheetReadException;

  class RamowkaTvRepublika {
  
    /**
     * RamowkaTvRepublika constructor.
     * @param array $dbCredentials
     * @throws Bootstrap\MySQL\MySqlDbConnException
     */
    public function __construct(array $dbCredentials) {
      MySqlSingleton::open([
          $dbCredentials["MYSQL_HOST"],
          $dbCredentials["MYSQL_USER"],
          $dbCredentials["MYSQL_PASSWORD"],
          $dbCredentials["MYSQL_DB_NAME"],
      ]);
      MySqlSingleton::getInstance()->getConnection()->autocommit(false);
    }
  
    /**
     * @param string $documentLocation
     * @return array
     * @throws SpreadsheetReadException
     * @throws Bootstrap\MySQL\MySqlDbConnException
     */
    public function importDocument(string $documentLocation) {
      MySqlSingleton::getInstance()->getConnection()->begin_transaction();
      try {
        try {
          /**  Identify the type of $inputFileName  **/
          $inputFileType = IOFactory::identify($documentLocation);
          /**  Create a new Reader of the type that has been identified  **/
          $reader = IOFactory::createReader($inputFileType);
          /**  Advise the Reader that we only want to load cell data  **/
          //$reader->setReadDataOnly(true);
          /**  Create an Instance of our Read Filter  **/
          $filterSubset = new SpreadsheetReadFilter();
          /**  Tell the Reader that we want to use the Read Filter  **/
          $reader->setReadFilter($filterSubset);
    
          $response = $this->briefCheckFile($reader, $documentLocation);
    
          /**  Load $inputFileName to a Spreadsheet Object  **/
          $spreadsheet = $reader->load($documentLocation);
          /**  Iterate over worksheets  **/
          $sheetsCount = $spreadsheet->getSheetCount();
          $sheetIndex = 0; $iteratedRows = 0;
          
          for(; $sheetIndex<$sheetsCount; $sheetIndex++) {
            $worksheet = $spreadsheet->getSheet($sheetIndex);
            $iteratedRows += WorksheetReader::importWorksheet($documentLocation, $worksheet);
          }
    
          $response["processedWorksheetsCount"] = $sheetIndex;
          $response["totalProcessedRows"] = $iteratedRows;
          
          MySqlSingleton::getInstance()->getConnection()->commit();
          
          return $response;
        } catch(SpreadsheetReadException $e) {
          throw $e;
        } catch(Exception $e) {
          throw new SpreadsheetReadException(
              SpreadsheetReadExceptionType::WHOLE_DOCUMENT_ERROR,
              $documentLocation,
              null, null, $e
          );
        }
      } catch(SpreadsheetReadException $e) {
        MySqlSingleton::getInstance()->getConnection()->rollback();
        throw $e;
      }
    }
  
    private function briefCheckFile(IReader $reader, string $documentLocation) {
      $data = [];
      $worksheets = $reader->listWorksheetInfo($documentLocation);
      $data['worksheetsCount'] = sizeof($worksheets);
      return $data;
    }
    
    public function __destruct() {
      MySqlSingleton::close();
    }
  
  }