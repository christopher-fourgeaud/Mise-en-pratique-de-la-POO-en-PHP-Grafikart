<?php

namespace Framework\Database;

use PDO;
use Pagerfanta\Pagerfanta;

class Table
{

    /**
     * Instance de PDO
     *
     * @var PDO
     */
    private $pdo;


    /**
     * Nom de la table en bdd
     *
     * @var string
     */
    protected $table;

    /**
     * Entité à utiliser
     *
     * @var string|null
     */
    protected $entity;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Pagine les éléments
     *
     * @param int $perPage
     *
     * @return PagerFanta
     */
    public function findPaginated(int $perPage, int $currentPage): PagerFanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id)
            FROM {$this->table}",
            $this->entity
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Récupère une liste clef valeur de nos enregistrements
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * Récupère un élément à partir de son id
     *
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        // On prépare la requète
        $query = $this->pdo->prepare(
            "SELECT *
             FROM {$this->table}
             WHERE id = ?"
        );

        // On set les attributs
        $query->execute([
            $id
        ]);

        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }

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
        $statement = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
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

        $values = join(', ', array_map(function ($fields) {
            return ':' . $fields;
        }, $fields));

        $fields = join(', ', $fields);

        $statement = $this->pdo->prepare(
            "INSERT INTO {$this->table} ($fields)
            VALUES ($values)"
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
            "DELETE FROM {$this->table}
            WHERE id = ?"
        );

        return $statement->execute([$id]);
    }

    private function buildFieldQuery(array $params)
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    /**
     * Get entité à utiliser
     *
     * @return  string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * Get nom de la table en bdd
     *
     * @return  string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    protected function paginationQuery()
    {
        return "SELECT *
                FROM {$this->table}";
    }

    /**
     * Get instance de PDO
     *
     * @return  PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Vérifie qu'un enregistrement éxiste
     *
     * @param $id
     * @return boolean
     */
    public function checkExists($id): bool
    {
        $statement = $this->pdo->prepare(
            "SELECT id
            FROM {$this->table}
            WHERE id = ?"
        );
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }
}
