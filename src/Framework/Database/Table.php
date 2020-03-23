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
    protected $pdo;


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
     * @param int $currentPage
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
            ->query(
                "SELECT id, name 
                FROM {$this->table}"
            )
            ->fetchAll(PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * Récupère tout les enregistrements
     *
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->pdo
            ->query(
                "SELECT * FROM
                {$this->table}"
            );
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(PDO::FETCH_OBJ);
        }
        return $query->fetchAll();
    }

    /**
     * Récupère une ligne par rapport à un champs
     *
     * @param string $field
     * @param string $value
     * @return array
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE $field = ?", [$value]);
    }

    /**
     * Récupère un élément à partir de son ID
     *
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    /**
     * Récupère le nombre d'enregistrement
     *
     * @return integer
     */
    public function count(): int
    {
        return $this->fetchColumn(
            "SELECT COUNT(id)
            FROM {$this->table}"
        );
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
        $query = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $query->execute($params);
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

        $query = $this->pdo->prepare(
            "INSERT INTO {$this->table} ($fields)
            VALUES ($values)"
        );
        return $query->execute($params);
    }

    /**
     * Supprimer un enregistrement au niveau de la bdd
     *
     * @param integer $id
     * @return boolean
     */
    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare(
            "DELETE FROM {$this->table}
            WHERE id = ?"
        );

        return $query->execute([$id]);
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
     * Permet d'éxécuter une requète et de récupérer le premier résultat
     *
     * @param string $query
     * @param array $params
     * @return void
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);

        // On set les attributs
        $query->execute($params);

        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }

        // On stocke le résultat de la requète
        $record = $query->fetch();

        // On vérifie si le resultat est false
        if ($record === false) {
            throw new NoRecordException();
        }

        // On retourne le résultat
        return $record;
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
        $query = $this->pdo->prepare(
            "SELECT id
            FROM {$this->table}
            WHERE id = ?"
        );
        $query->execute([$id]);
        return $query->fetchColumn() !== false;
    }

    /**
     * Récupère la première colonne
     *
     * @param string $query
     * @param array $params
     * @return mixed
     */
    private function fetchColumn(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);

        $query->execute($params);

        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }

        return $query->fetchColumn();
    }
}
