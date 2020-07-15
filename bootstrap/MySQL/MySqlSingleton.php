<?php
  namespace RamowkaTvRepublika\Bootstrap\MySQL;
  
  use RuntimeException;

  class MySqlSingleton {
    /**
     * @var MySQLDB
     */
    private static $instance = null;
  
    /**
     * MySqlSingleton constructor.
     * @param array $dbCredentials
     * @throws MySqlDbConnException
     */
    public static function open($dbCredentials) {
      if(gettype($dbCredentials)!=="array") {
        throw new RuntimeException("Oczekiwano, że paramter `dbCredentials` będzie typu `array`.");
      }
      MySqlSingleton::$instance = new MySqlDB($dbCredentials);
      MySqlSingleton::$instance->open();
    }
  
    /**
     * @return MySQLDB
     */
    public static function getInstance() {
      return MySqlSingleton::$instance;
    }
    
    public static function close() {
      if(
          MySqlSingleton::$instance !== null
          && MySqlSingleton::$instance instanceof MySQLDB
          && MySqlSingleton::$instance->isOpen()
      ) {
        MySqlSingleton::$instance->close();
      }
    }
  
  }