<?php

namespace Framework;

use PDO;
use DateTime;
use Framework\Database\Table;
use Framework\Validator\ValidationError;
use Psr\Http\Message\UploadedFileInterface;

class Validator
{
    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf'
    ];

    /**
     * Tableau contenant les paramètres
     *
     * @var array
     */
    private $params;

    /**
     * Tableau contenant les erreurs
     *
     * @var string[]
     */
    private $errors = [];


    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Vérifie que les champs soient présent dans le tableau
     *
     * @param string[] ...$keys
     * @return self
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    /**
     * Vérifie que le champs n'est pas vide
     *
     * @param string ...$keys
     * @return self
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }

    public function checkLength(string $key, ?int $minLength, ?int $maxLength = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (!is_null($minLength) &&
            !is_null($maxLength) &&
            ($length < $minLength || $length > $maxLength)
        ) {
            $this->addError($key, 'betweenLength', [$minLength, $maxLength]);
            return $this;
        }
        if (!is_null($minLength) &&
            $length < $minLength
        ) {
            $this->addError($key, 'minLength', [$minLength]);
            return $this;
        }
        if (!is_null($maxLength) &&
            $length > $maxLength
        ) {
            $this->addError($key, 'maxLength', [$maxLength]);
        }
        return $this;
    }

    /**
     * Vérifie que l'élément est un slug correct
     *
     * @param string $key
     * @return self
     */
    public function checkSlug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }

    public function checkDateTime(string $key, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        $date = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }

    /**
     * Vérifie que la clef existe dans la base de donnée
     *
     * @param string $key
     * @param string $table
     * @param PDO $pdo
     * @return self
     */
    public function checkExists(string $key, string $table, PDO $pdo): self
    {
        $value = $this->getValue($key);
        $statement = $pdo->prepare(
            "SELECT id
            FROM $table
            WHERE id = ?"
        );
        $statement->execute([$value]);
        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'exists', [$table]);
        }
        return $this;
    }

    /**
     * Vérifie que la clef est unique dans la base de donnée
     *
     * @param string $key
     * @param string $table
     * @param PDO $pdo
     * @return self
     */
    public function checkUnique(string $key, string $table, PDO $pdo, ?int $exclude = null): self
    {
        $value = $this->getValue($key);
        $query =
            "SELECT id
                FROM $table
                WHERE $key = ?";
        $params = [$value];

        if ($exclude !== null) {
            $query .= "AND id != ?";
            $params[] = $exclude;
        }

        $statement = $pdo->prepare($query);
        $statement->execute($params);
        if ($statement->fetchColumn() !== false) {
            $this->addError($key, 'unique', [$value]);
        }
        return $this;
    }

    /**
     * Vérifie si le fichier à bien été uploader
     *
     * @param string $key
     * @return self
     */
    public function checkUploaded(string $key): self
    {
        $file = $this->getValue($key);
        if ($file === null || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }
        return $this;
    }

    /**
     * Vérifie si l'email est valide
     *
     * @param string $key
     * @return self
     */
    public function checkEmail(string $key): self
    {
        $value = $this->getValue($key);
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($key, 'email');
        }
        return $this;
    }

    /**
     * Vérifie le format d'un fichier
     *
     * @param string $key
     * @param array $extensions
     * @return self
     */
    public function checkExtension(string $key, array $extensions): self
    {
        /** @var UploadedFileInterface $file */

        $file = $this->getValue($key);
        if ($file !== null && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectedType = self::MIME_TYPES[$extension] ?? null;
            if (!in_array($extension, $extensions) || $expectedType !== $type) {
                $this->addError($key, 'filetype', [join(', ', $extensions)]);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Récupère les erreurs
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Ajoute une erreur
     *
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }


    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }
}
