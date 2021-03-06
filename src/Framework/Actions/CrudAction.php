<?php

namespace Framework\Actions;

use Framework\Database\Hydrator;
use Framework\Router;
use Framework\Validator;
use Framework\Database\Table;
use Framework\Session\FlashService;
use Psr\Http\Message\ResponseInterface;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use stdClass;

class CrudAction
{

    /**
     * Instance du Renderer
     *
     * @var RendererInterface
     */
    private $renderer;

    /**
     * Instance du router
     *
     * @var Router
     */
    private $router;

    /**
     * Instance de table à utiliser
     *
     * @var Table
     */
    protected $table;

    /**
     * Instance de FlashService
     *
     * @var FlashService
     */
    private $flash;

    /**
     * Tableau contenant les erreurs
     *
     * @var array
     */
    private $errors = [];

    /**
     * Chemin de la vue
     *
     * @var string
     */
    protected $viewPath;

    /**
     * Préfix de la route
     *
     * @var string
     */
    protected $routePrefix;

    /**
     * Tableau des messages flash
     *
     * @var string[]
     */
    protected $messages = [
        'create' => "L'élément à bien été crée",
        'edit' => "L'élément à bien été modifié",
    ];

    protected $acceptedParams = [];

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Table $table,
        Router $router,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->router = $router;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string) $request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    /**
     * Affiche la liste des éléments
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->table->findAll()->paginate(12, $params['p'] ?? 1);

        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }

    /**
     * Edite un élément
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request)
    {
        $item = $this->table->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update($item->id, $this->getParams($request, $item));
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $this->errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item);
        }

        return $this->renderer->render(
            $this->viewPath . '/edit',
            $this->formParams([
                'item' => $item,
                'errors' => $this->errors
            ])
        );
    }

    /**
     * Crée un nouvel élément
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->insert($this->getParams($request, $item));
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }
            Hydrator::hydrate($request->getParsedBody(), $item);
            $this->errors = $validator->getErrors();
        }

        return $this->renderer->render(
            $this->viewPath . '/create',
            $this->formParams([
                'item' => $item,
                'errors' => $this->errors
            ])
        );
    }

    /**
     * Action de suppression
     *
     * @param Request $request
     * @return ResponseInterface
     */
    public function delete(Request $request)
    {
        $this->table->delete($request->getAttribute('id'));

        return $this->redirect($this->routePrefix . '.index');
    }

    /**
     * Filtre les paramètres reçu par la requête
     *
     * @param Request $request
     * @return array
     */
    protected function getParams(Request $request, $item): array
    {

        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, $this->acceptedParams);
        }, ARRAY_FILTER_USE_KEY);
    }


    /**
     * Génère le validateur pour valider les données
     *
     * @param Request $request
     * @return Validator
     */
    protected function getValidator(Request $request)
    {
        return (new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles())));
    }

    /**
     * Génère une nouvelle entité pour l'action de création
     *
     * @return stdClass
     */
    protected function getNewEntity()
    {
        return new stdClass;
    }

    /**
     * Permet de traiter les paramètres à envoyer à la vue
     *
     * @param array $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }
}
