<?php
  namespace RamowkaTvRepublika\Bootstrap\MySQL;
  
  use mysqli;
  use RuntimeException;

  /**
   * Class MySQLDB
   * @package Leona
   */
  class MySqlDB {
    /**
     * @var array $dbCredentials
     */
    private $dbCredentials;
  
    /**
     * @var mysqli
     */
    private $conn = null;
  
    /**
     * MySQLDB constructor.
     * @param array $dbCredentials
     */
    public function __construct($dbCredentials) {
      $this->dbCredentials = $dbCredentials;
    }
  
    /**
     * Otwiera połączenie z bazą danych
     * @throws MySqlDbConnException
     */
    public function open() {
      $this->conn = $this->getConnection();
    }
  
    /**
     * Zwraca boolean - czy połączenie jest otwarte
     * @return bool
     */
    public function isOpen() {
      return $this->conn !== null;
    }
  
    /**
     * Zamyka połączenie z bazą danych
     */
    public function close() {
      $this->conn->close();
      $this->conn = null;
    }
  
    /**
     * @param $sql
     * @param null | string $param
     * @return mixed
     * @throws MySqlQueryException
     */
    private function internal_query(string $sql, string $param = QueryParam::SELECT) {
      // Sprawdzamy połączenie
      if (!$this->isOpen())
        throw new RuntimeException("Połączenie z bazą jest zamknięte.");
      // Wysyłamy zapytanie do bazy
      $result = $this->conn->query($sql);
      // Sprawdzamy błędy
      if ($this->conn->error) {
        throw new MySqlQueryException($this->conn->error);
      }
      switch ($param) {
        // SELECT - TO SAMO, CO DEFAULT
        case QueryParam::INSERT:
          {
            return $this->conn->insert_id;
          }
        case QueryParam::UPDATE:
        case QueryParam::DELETE:
          {
            return null;
          }
        default:
          {
            return $result;
          }
      }
    }
  
    /**
     * @param string $sql
     * @return mixed
     * @throws MySqlQueryException
     */
    public function select(string $sql) {
      return $this->internal_query($sql, QueryParam::SELECT);
    }
  
    /**
     * @alias select(string $sql)
     * @param string $sql
     * @return mixed
     * @throws MySqlQueryException
     */
    public function query(string $sql) {
      return $this->internal_query($sql, QueryParam::SELECT);
    }
  
    /**
     * @param string $sql
     * @return mixed
     * @throws MySqlQueryException
     */
    public function insert(string $sql) {
      return $this->internal_query($sql, QueryParam::INSERT);
    }
  
    /**
     * @param string $sql
     * @return mixed
     * @throws MySqlQueryException
     */
    public function update(string $sql) {
      return $this->internal_query($sql, QueryParam::UPDATE);
    }
  
    /**
     * @param string $sql
     * @throws MySqlQueryException
     */
    public function delete(string $sql) {
      $this->internal_query($sql, QueryParam::DELETE);
    }
  
    /**
     * @throws MySqlDbConnException
     * @return mysqli
     */
    public function getConnection() {
      if ($this->dbCredentials === null)
        throw new MySqlDbConnException("Credentials not provided.");
      // Create connection
      $conn = new mysqli(
          $this->dbCredentials[0],
          $this->dbCredentials[1],
          $this->dbCredentials[2],
          $this->dbCredentials[3]
      );
      // Check connection
      if ($conn->connect_error) {
        throw new MySqlDbConnException($conn->connect_error);
      }
      $conn->set_charset("utf8");
      return $conn;
    }
    
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  