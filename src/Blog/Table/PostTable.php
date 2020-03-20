<?php

namespace App\Blog\Table;

use PDO;
use stdClass;
use App\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;
use Framework\Database\PaginatedQuery;

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
     * @param int $perPage
     *
     * @return PagerFanta
     */
    public function findPaginated(int $perPage, int $currentPage): PagerFanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            'SELECT *
            FROM posts
            ORDER BY created_at DESC',
            'SELECT COUNT(id)
            FROM posts',
            Post::class
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Récupère un article à partir de son id
     *
     * @param int $id
     * @return Post|null
     */
    public function find(int $id): ?Post
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

        $query->setFetchMode(PDO::FETCH_CLASS, Post::class);

        // On lance la requète
        return $query->fetch() ?: null;
    }

    /**
     * Met à jour un enregistrement au niveau de la base de données
     *
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params["id"] = $id;
        $statement = $this->pdo->prepare("UPDATE posts SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }

    /**
     * Ajoute un enregistrement au niveau de la bdd
     *
     * @param array $params
     * @return boolean
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = array_map(function ($fields) {
            return ':' . $fields;
        }, $fields);


        $statement = $this->pdo->prepare(
            "INSERT INTO posts (" . join(',', $fields) . ")
            VALUES (" . join(',', $values) . ")"
        );
        return $statement->execute($params);
    }

    /**
     * Supprimer un enregistrement au niveau de la bdd
     *
     * @param integer $id
     * @return boolean
     */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM posts
            WHERE id = ?'
        );

        return $statement->execute([$id]);
    }

    private function buildFieldQuery(array $params)
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }
}
