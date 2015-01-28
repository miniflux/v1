<?php

/*
 * This file is part of Simple Validator.
 *
 * (c) Frédéric Guillot <contact@fredericguillot.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SimpleValidator\Validators;

use SimpleValidator\Base;

/**
 * @author Frédéric Guillot <contact@fredericguillot.com>
 * @author Olivier Maridat
 */
class Unique extends Base
{
    private $pdo;
    private $primary_key;
    private $table;


    public function __construct($field, $error_message, \PDO $pdo, $table, $primary_key = 'id')
    {
        parent::__construct($field, $error_message);

        $this->pdo = $pdo;
        $this->primary_key = $primary_key;
        $this->table = $table;
    }


    public function execute(array $data)
    {
      if (! is_array($this->field)) {
          $this->field = array($this->field);
      }
      $fields = array();
      $parameters = array();
      foreach($this->field AS $field) {
          if (! isset($data[$field]) || $data[$field] === '') {
              return true;
          }
          $fields[] = $field;
          $parameters[] = $data[$field];
      }
      
      if (isset($data[$this->primary_key])) {
          $parameters[] = $data[$this->primary_key];
      }

      $rq = $this->pdo->prepare(
          'SELECT COUNT(*) FROM '.$this->table.' WHERE '.implode('=? AND ', $fields).'=?'.(isset($data[$this->primary_key]) ? ' AND '.$this->primary_key.'!=?' : '')
      );
      
      $rq->execute($parameters);
      
      $result = $rq->fetch(\PDO::FETCH_NUM);
      
      if (isset($result[0]) && $result[0] === '1') {
          return false;
      }
      
      return true;
    }
}