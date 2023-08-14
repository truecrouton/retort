<?php

namespace Retort\Controller;

use Error;
use mysqli;
use mysqli_result;

class MysqlController extends RetortController
{
    public function __construct(public mysqli $myDb)
    {
    }

    protected function executeQuery(string $query, array $params = []): mysqli_result | bool
    {
        $res = $this->myDb->execute_query($query, $params);
        if ($res === false) throw new Error('Could not execute query.');

        return $res;
    }
}
