<?php

namespace Framework\Database;

use PDO;
use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface
{
    /**
     * Instance de pdo
     *
     * @var PDO
     */
    private $pdo;

    /**
     * Requète permettant de récupérer X résultats
     *
     * @var string
     */
    private $query;

    /**
     * Requète permettant de compter le nombre de résultat total
     *
     * @var string
     */
    private $countQuery;

    /**
     * @var string
     */
    private $entity;

    /**
     * PaginatedQuery constructor.
     *
     * @param PDO $pdo Instance de pdo
     * @param string $query Requète permettant de récupérer X résultats
     * @param string $countQuery Requète permettant de compter le nbr de résultats total
     * @param string $entity
     */
    public function __construct(PDO $pdo, string $query, string $countQuery, string $entity)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entity = $entity;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    function getNbResults(): int
    {
        return $this->pdo->query($this->countQuery)->fetchColumn();
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Iterator|\IteratorAggregate The slice.
     */
    function getSlice($offset, $length): array
    {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        $statement->bindParam('offset', $offset, PDO::PARAM_INT);
        $statement->bindParam('length', $length, PDO::PARAM_INT);
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        $statement->execute();

        return $statement->fetchAll();
    }
}
