<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;
use Framework\Database\Table;
use Framework\Database\PaginatedQuery;

class PostTable extends Table
{
    protected $entity = Post::class;

    protected $table = 'posts';

    /**
     * Pagine les éléments
     *
     * @param int $perPage
     * @param int $currentPage
     *
     * @return PagerFanta
     */
    public function findPaginatedPublic(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            "SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM posts as p
            LEFT JOIN categories as c on c.id = p.category_id
            ORDER BY p.created_at DESC",
            "SELECT COUNT(id)
            FROM {$this->table}",
            $this->entity
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findPaginatedPublicForCategory(int $perPage, int $currentPage, int $categoryId): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            "SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM posts as p
            LEFT JOIN categories as c on c.id = p.category_id
            WHERE p.category_id = :category
            ORDER BY p.created_at DESC",
            "SELECT COUNT(id)
            FROM {$this->table}
            WHERE category_id = :category",
            $this->entity,
            ['category' => $categoryId]
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findWithCategory(int $id)
    {
        return $this->fetchOrFail(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM posts as p
            LEFT JOIN categories as c ON c.id = p.category_id
            WHERE p.id = ?",
            [$id]
        );
    }

    protected function paginationQuery()
    {
        return "SELECT p.id, p.name, c.name category_name
                FROM {$this->table} as p
                LEFT JOIN categories as c ON p.category_id = c.id
                ORDER BY created_at DESC";
    }
}
