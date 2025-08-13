<?php


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;


class AuthorizationMiddleware
{

    public function __construct(private array $permissions, private GestorFuncionario $gestorFuncionario) {}

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $route = $request->getAttribute('__route__');

        if ($route === null || $route->getName() === null) {
            return $handler->handle($request);
        }
        $routeName = $route->getName();

        $userId = $_SESSION['id'] ?? null;
        if ($userId === null) {
            return $this->forbiddenResponse("ID do usuário não encontrado na sessão.");
        }

        $funcionario = $this->gestorFuncionario->buscarPorUsuarioId($userId);
        if ($funcionario === null) {
        }

        $userRole = strtoupper($funcionario->getCargo());

        $allowedRoutesForRole = $this->permissions[$userRole] ?? [];

        if (!in_array($routeName, $allowedRoutesForRole)) {
            return $this->forbiddenResponse("O cargo '{$userRole}' não tem permissão para acessar a rota '{$routeName}'.");
        }

        return $handler->handle($request);
    }

    private function forbiddenResponse(string $reason = ''): Response
    {
        $response = new SlimResponse();
        $message = 'Acesso negado. Você não tem permissão para executar esta ação.';

        if (!empty($reason) && (getenv('APP_ENV') === 'development')) {
            $message .= " Motivo: " . $reason;
        }

        $payload = json_encode(['mensagem' => $message]);
        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }
}
