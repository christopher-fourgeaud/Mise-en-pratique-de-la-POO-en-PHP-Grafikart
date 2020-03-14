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
     * @return Post
     */
    public function find(int $id): Post
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
        return $query->fetch();
    }
}
