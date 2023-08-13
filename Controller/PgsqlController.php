<?php

namespace Retort\Controller;

use PgSql\Connection;

class MysqlController extends RetortController
{
    public function __construct(public Connection $pgDb)
    {
    }
}
