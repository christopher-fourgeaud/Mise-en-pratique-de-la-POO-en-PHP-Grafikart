<?php


namespace App\Auth\Actions;

use Framework\Router;
use App\Auth\DatabaseAuth;
use Framework\Session\FlashService;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAttemptAction
{
    private $renderer;

    private $auth;

    private $router;

    private $session;

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        DatabaseAuth $auth,
        Router $router,
        SessionInterface $session
    ) {
        $this->renderer = $renderer;
        $this->session = $session;
        $this->router = $router;
        $this->auth = $auth;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $user = $this->auth->login($params['username'], $params['password']);

        if ($user) {
            $path = $this->session->get('auth.redirect') ?: $this->router->generateUrl('admin');
            $this->session->delete('auth.redirect');
            return new RedirectResponse($path);
        } else {
            (new FlashService($this->session))->error('Identifiant ou mot de pass incorrect');
            return $this->redirect('auth.login');
        }
    }
}
