<?php

namespace Framework\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Rajoute des méthodes liées à l'utilisation du router
 */
trait RouterAwareAction
{

    /**
     * Renvoie une réponse de redirection
     *
     * @param string $path
     * @param array $params
     *
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirectUrl = $this->router->generateUrl($path, $params);

        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUrl);
    }
}
