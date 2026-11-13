<?php

declare(strict_types=1);

namespace App\Repositories\MySQL;

use App\Core\Database;
use PDO;

abstract class BaseRepository
{
    protected PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function getDb(): PDO
    {
        return $this->db;
    }
}

