<?php

namespace App\Blog\Table;

use PDO;
use stdClass;

class PostTable
{

    /**
     * Instance de PDO
     *
     * @var PDO
     */
    private $pdo;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Pagine les articles
     *
     * @return stdClass[]
     */
    public function findPaginated(): array
    {
        return $this->pdo
            ->query(
                'SELECT *
             FROM posts
             ORDER BY created_at
             DESC LIMIT 10'
            )->fetchAll();
    }

    /**
     * Récupère un article à partir de son id
     *
     * @param int $id
     * @return stdClass
     */
    public function find(int $id): stdClass
    {
        // On prépare la requète
        $query = $this->pdo->prepare(
            'SELECT *
             FROM posts
             WHERE id = ?'
        );

        // On set les attributs
        $query->execute([
            $id
        ]);

        // On lance la requète
        return $query->fetch();
    }
}
