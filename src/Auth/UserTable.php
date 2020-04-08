<?php

namespace App\Auth;

use PDO;
use App\Auth\User;
use Framework\Database\Table;

class UserTable extends Table
{
    protected $table = "users";

    protected $entity = User::class;

    public function __construct(PDO $pdo, string $entity = User::class)
    {
        $this->entity = $entity;
        parent::__construct($pdo);
    }
}
