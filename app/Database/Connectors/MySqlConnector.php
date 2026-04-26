<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\MySqlConnector as BaseMySqlConnector;
use PDO;
use SensitiveParameterValue;

class MySqlConnector extends BaseMySqlConnector
{
    /**
     * Use the classic PDO constructor until PDO::connect behaves correctly
     * with this local PHP 8.5 + MySQL Docker setup.
     */
    protected function createPdoConnection($dsn, $username, #[\SensitiveParameter] $password, $options): PDO
    {
        if ($password instanceof SensitiveParameterValue) {
            $password = $password->getValue();
        }

        return new PDO($dsn, $username, $password);
    }
}
