<?php


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;


require __DIR__ . '/../vendor/autoload.php';
$permissions = require __DIR__ . '/../config/permissions.php';

$app = AppFactory::create();
$app->add(new ErrorMiddleware());

$app->addBodyParsingMiddleware();

$app->add(function (Request $request, RequestHandler $handler): Response {
    $allowedOrigins = ['http://127.0.0.1:5173', 'http://localhost:5173'];
    $origin = $request->getHeaderLine('Origin');
    $response = $handler->handle($request);
    if (in_array($origin, $allowedOrigins)) {
        $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
    }
    return $response
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
});

$app->options(
    '/{routes:.+}',
    function (Request $request, Response $response) {
        return $response;
    }
);

$pdo = null;
try {
    $pdo = Connection::getConnection();
} catch (PDOException $e) {
    http_response_code(500);
    die('Erro ao criar o banco de dados.');
}
$session = new Session();
$repositorioLocacao = new LocacaoRepositorioEmBDR($pdo);
$repositorioItem = new ItemRepositorioEmBDR($pdo);
$repositorioLocacaoItem = new LocacaoItemRepositorioEmBDR($pdo);
$repositorioCliente = new ClienteRepositorioEmBDR($pdo);
$repositorioFuncionario = new FuncionarioRepositorioEmBDR($pdo);
$repositorioUsuario = new UsuarioRepositorioEmBDR($pdo);
$transacao = new TransacaoEmBDR($pdo);
$gestorItem = new GestorItem($repositorioItem, $transacao);
$gestorFuncionario = new GestorFuncionario($repositorioFuncionario);
$gestorCliente = new GestorCliente($repositorioCliente);
$gestorLocacaoItem = new GestorLocacaoItem($repositorioLocacaoItem);
$gestorLocacao = new GestorLocacao(
    $repositorioLocacao,
    $repositorioItem,
    $repositorioLocacaoItem,
    $gestorCliente,
    $gestorFuncionario,
    $transacao
);
$avariaRepositorio = new AvariaRepositorioEmBDR($pdo);
$gestorDevolucao = new GestorDevolucao(
    new DevolucaoRepositorioEmBDR($pdo),
    $repositorioItem,
    $repositorioLocacao,
    $repositorioLocacaoItem,
    $gestorLocacaoItem,
    $gestorItem,
    $gestorCliente,
    $gestorFuncionario,
    $transacao,
    $avariaRepositorio
);
$gestorUsuario = new GestorUsuario($repositorioUsuario);
$gestorAvaria = new GestorAvaria($avariaRepositorio, $transacao);
$relatorioRepositorio = new RelatorioRepositorioEmBDR($pdo);
$gestorRelatorio = new GestorRelatorio($relatorioRepositorio, $transacao);


$authorization = new AuthorizationMiddleware($permissions, $gestorFuncionario);

$auth = function (Request $request, RequestHandler $handler): Response {
    if (!Session::isSessaoValida()) {
        $response = new \Slim\Psr7\Response();
        $payload  = json_encode(['mensagem' => 'Não autorizado. Sessão inválida ou expirada.']);
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }
    return $handler->handle($request);
};


$app->post('/login', function ($req, $res) use ($gestorUsuario, $session) {
    $dados = json_decode($req->getBody(), true);
    $login = $gestorUsuario->fazerLogin($dados);
    Session::definirSessao($login);
    $payload = json_encode($login);
    $res->getBody()->write($payload);
    return $res->withStatus(200)
        ->withHeader('Content-Type', 'application/json');
})->setName('auth.login');

$app->post('/logout', function ($req, $res) use ($session) {
    Session::encerrarSessao();
    $res->getBody()->write(json_encode(['message' => 'Sessão encerrada com sucesso']));
    return $res
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
})->setName('auth.logout');


$app->group('', function ($group) use ($gestorDevolucao, $gestorRelatorio, $gestorLocacao, $gestorItem, $gestorFuncionario, $gestorCliente, $gestorLocacaoItem, $gestorAvaria) {

    $group->get('/locacoes', function ($req, $res) use ($gestorLocacao) {
        $query = $req->getQueryParams();
        $locacoes = $gestorLocacao->listarTodos($query);
        $payload = json_encode(array_map(
            fn(LocacaoResumo $l) => $l->toArray(),
            $locacoes
        ));
        $res->getBody()->write($payload);
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    })->setName('locacoes.listar');

    $group->post('/locacoes', function ($req, $res) use ($gestorLocacao) {
        $dados = json_decode($req->getBody(), true);

        $locacao = $gestorLocacao->criarLocacao($dados);
        $payload = json_encode($locacao);
        $res->getBody()->write($payload);
        return $res->withStatus(201)
            ->withHeader('Content-Type', 'application/json');
    })->setName('locacoes.criar');

    $group->get('/locacoes/{id}/itens', function ($req, $res, array $args) use ($gestorLocacaoItem) {
        $itens = $gestorLocacaoItem->obterItensPorLocacaoId($args['id']);
        if ($itens === null) {
            $res->getBody()->write(json_encode(['mensagem' => "Locação id {$args['id']}: não encontrada"]));
            return $res->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
        $itens = array_map(
            fn(LocacaoItemResumo $i) => $i->toArray(),
            $itens
        );
        $payload = json_encode($itens);
        $res->getBody()->write($payload);
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    })->setName('locacoes.itens.listar');

    $group->get('/itens', function ($req, $res) use ($gestorItem, $gestorAvaria) {
        $query  = $req->getQueryParams();
        $codigo = $query['codigo'] ?? null;
        $itens  = [];
        if ($codigo !== null) {
            $item = $gestorItem->buscarPorCodigo($codigo);
            if ($item) {
                $itens = [$item];
            }
        } else {
            $itens = $gestorItem->buscarTodos();
        }

        if (isset($query['disponivel'])) {
            $flag = filter_var($query['disponivel'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($flag !== null) {
                $itens = array_filter(
                    $itens,
                    fn(Item $i) => $i->isDisponivel() == $flag
                );
            }
        }

        foreach ($itens as $item) {
            $avarias = $gestorAvaria->buscarPorItem($item->getId());
            $item->setAvarias($avarias ?? []);
        }
        $dados = array_values(array_map(
            fn(Item $i) => $i->toArray(),
            $itens
        ));

        $res->getBody()->write(json_encode($dados, JSON_UNESCAPED_UNICODE));
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    })->setName('itens.listar');

    $group->get('/funcionarios', function ($req, $res) use ($gestorFuncionario) {

        $dados   = $gestorFuncionario->buscarTodos();
        $payload = json_encode(
            array_map(
                fn($f) => $f->toArray(),
                $dados
            ),
        );
        $res->getBody()->write($payload);
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    })->setName('funcionarios.listar');

    $group->get('/clientes', function ($req, $res) use ($gestorCliente) {

        $dados   = $gestorCliente->buscarTodos();
        $payload = json_encode(
            array_map(
                fn($f) => $f->toArray(),
                $dados
            ),

        );
        $res->getBody()->write($payload);
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    })->setName('clientes.listar');

    $group->post('/devolucoes', function ($req, $res) use ($gestorDevolucao) {
        $dados = json_decode($req->getBody(), true);
        $gestorDevolucao->criarDevolucao($dados);
        $payload = json_encode(['mensagem' => 'Devolução criada com sucesso']);
        $res->getBody()->write($payload);
        return $res->withStatus(201)
            ->withHeader('Content-Type', 'application/json');
    })->setName('devolucoes.criar');


    $group->get('/devolucoes', function ($req, $res) use ($gestorDevolucao) {
        $dados   = $gestorDevolucao->listarTodos();
        $payload = json_encode(array_map(
            fn(DevolucaoResumo $d) => $d->toArray(),
            $dados
        ));
        $res->getBody()->write($payload);
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    })->setName('devolucoes.listar');


    $group->get('/relatorio/locacoes', function (Request $req, Response $res) use ($gestorRelatorio) {
        $query = $req->getQueryParams();
        $dataInicial = $query['dataInicial'] ?? date('Y-m-01');
        $dataFinal = $query['dataFinal'] ?? date('Y-m-t');
        $dadosLocacoes = $gestorRelatorio->buscarLocacoesDevolvidas($dataInicial, $dataFinal);
        $payload = json_encode($dadosLocacoes);
        $res->getBody()->write($payload);
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    })->setName('relatorio.locacoes');

    $group->get('/relatorio/top-itens', function (Request $req, Response $res) use ($gestorRelatorio) {
        $query = $req->getQueryParams();
        $dataInicial = $query['dataInicial'] ?? date('Y-m-01');
        $dataFinal = $query['dataFinal'] ?? date('Y-m-t');


        $dadosItens = $gestorRelatorio->buscarTopItens($dataInicial, $dataFinal);
        $payload = json_encode($dadosItens);
        $res->getBody()->write($payload);
        return $res
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    })->setName('relatorio.top-itens');
})->add($authorization)->add($auth);

$app->run();
