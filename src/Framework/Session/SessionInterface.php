<?php

namespace Framework\Session;

interface SessionInterface
{
    /**
     * Récupère une information en Session
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Ajoute une information en Session
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set(string $key, $value): void;

    /**
     * Supprime une clef en Session
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
}
