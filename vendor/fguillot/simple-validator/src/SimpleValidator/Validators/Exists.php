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
 * @author Olivier Maridat
 */
class Exists extends Base
{
    private $pdo;
    private $key;
    private $table;


    public function __construct($field, $error_message, \PDO $pdo, $table, $key = '')
    {
        parent::__construct($field, $error_message);

        $this->pdo = $pdo;
        $this->table = $table;
        $this->key = $key;
    }


    public function execute(array $data)
    {
        if (! isset($data[$this->field]) || '' === $data[$this->field]) {
            return true;
        }
        if ($this->key === '') {
            $this->key = $this->field;
        }

        $rq = $this->pdo->prepare('SELECT COUNT(*) FROM '.$this->table.' WHERE '.$this->key.'=?');
        $rq->execute(array(
            $data[$this->field]
        ));

        $result = $rq->fetch(\PDO::FETCH_NUM);

        if (isset($result[0]) && $result[0] >= '1') {
            return true;
        }

        return false;
    }
}