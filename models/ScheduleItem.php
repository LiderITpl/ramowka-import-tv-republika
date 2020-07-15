<?php
  namespace RamowkaTvRepublika\Models;
  
  use RamowkaTvRepublika\Bootstrap\MySQL\MySqlQueryException;
  use RamowkaTvRepublika\Exceptions\ModelValidationException;
  use function RamowkaTvRepublika\Bootstrap\MySQL\getMysql;

  /**
   * @property int|null id
   * @property string|null date
   * @property string|null time
   * @property string|null timestamp
   * @property string|null title
   * @property string|null label
   */
  class ScheduleItem extends ModelBase {
    
    public function __construct(array $attributes = []) {
      parent::__construct(
          ['id', 'date', 'time', 'timestamp', 'title', 'label'],
          $attributes
      );
      $this->excludedValidationFields = ['id', 'timestamp', 'label'];
    }
  
    /**
     * @throws MySqlQueryException
     * @throws ModelValidationException
     */
    protected function insert() {
      $attrs = $this->validateAttrs();
      
      $timestamp = $attrs["timestamp"] === null ? "NULL" : "FROM_UNIXTIME({$attrs['timestamp']})";
      
      $sql = <<<EOD
        INSERT INTO planner (date, time, timestamp, title, label)
        VALUES ('{$attrs['date']}', '{$attrs['time']}', {$timestamp}, '{$attrs['title']}', '{$attrs['label']}');
EOD;
      $this->id = getMysql()->insert($sql);
    }
  
    protected function update() {
      // TODO: Na razie nie jest potrzebne
    }
    
  }