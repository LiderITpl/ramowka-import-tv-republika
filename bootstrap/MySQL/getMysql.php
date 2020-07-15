<?php
  namespace RamowkaTvRepublika\Bootstrap\MySQL;

  function getMysql() {
    return MySqlSingleton::getInstance();
  }