<?php

namespace Framework\Database;

use PDO;
use Pagerfanta\Pagerfanta;
use stdClass;
use Traversable;

class Table
{

    /**
     * Instance de PDO
     *
     * @var null|PDO
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
     * @var string
     */
    protected $entity = stdClass::class;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
     * @return Query
     */
    public function makeQuery(): Query
    {
        return (new Query($this->pdo))
            ->from($this->table, $this->table[0])
            ->into($this->entity);
    }

    /**
     * Récupère tout les enregistrements
     *
     * @return Query
     */
    public function findAll(): Query
    {
        return $this->makeQuery();
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
        return $this->makeQuery()->where("$field = :field")->params(["field" => $value])->fetchOrFail();
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
        return $this->makeQuery()->where("id = $id")->fetchOrFail();
    }

    /**
     * Récupère le nombre d'enregistrement
     *
     * @return integer
     */
    public function count(): int
    {
        return $this->makeQuery()->count();
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
}
