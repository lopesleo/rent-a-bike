<?php

class LocacaoRepositorioEmBDR implements ILocacaoRepositorio
{

    public function __construct(private PDO $pdo) {}

    function salvar(Locacao $locacao): int
    {
        try {
            $sql = "INSERT INTO locacao (cliente_id,funcionario_id,horas_contratadas,data_hora_locacao, data_hora_entrega_prevista,desconto_aplicado, valor_total_previsto) 
            VALUES (:cliente_id, :funcionario_id, :horas_contratadas, :data_hora_locacao, :data_hora_entrega_prevista, :desconto_aplicado, :valor_total_previsto)";
            $stmt = $this->pdo->prepare($sql);

            $params = [
                'cliente_id'                 => $locacao->getCliente()->getId(),
                'funcionario_id'             => $locacao->getFuncionario()->getId(),
                'horas_contratadas'          => $locacao->getHorasContratadas(),
                'data_hora_locacao'          => $locacao->getInicio()->format('Y-m-d H:i:s'),
                'data_hora_entrega_prevista' => $locacao->getPrevista()->format('Y-m-d H:i:s'),
                'desconto_aplicado'          => $locacao->getDesconto(),
                'valor_total_previsto'       => $locacao->getValorTotal(),
            ];
            $stmt->execute($params); // Assumes PDO is set to throw exceptions on error.
            $lastId = $this->pdo->lastInsertId();
            return (int)$lastId;
        } catch (PDOException $e) {
            throw new RepositorioException($e->getMessage());
        }
    }

    function atualizar(Locacao $locacao): bool
    {
        try {
            $sql = "UPDATE locacao SET cliente_id = :cliente_id, data_hora_locacao = :data_hora_locacao, data_hora_entrega_prevista = :data_hora_entrega_prevista, valor_total_previsto = :valor_total_previsto, desconto_aplicado = :desconto_aplicado
            WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'id'                         => $locacao->getId(),
                'cliente_id'                 => $locacao->getCliente()->getId(),
                'data_hora_locacao'          => $locacao->getInicio()->format('Y-m-d H:i:s'),
                'data_hora_entrega_prevista' => $locacao->getPrevista()->format('Y-m-d H:i:s'),
                'valor_total_previsto'       => $locacao->getValorTotal(),
                'desconto_aplicado'          => $locacao->getDesconto(),
            ]);
        } catch (PDOException $e) {
            throw new RepositorioException($e->getMessage());
        }
    }

    function deletarPorId($id): bool
    {
        try {
            $sql = "DELETE FROM locacao WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw new RepositorioException($e->getMessage());
        }
    }

    public function buscarPorId(int $id): LocacaoResumo
    {
        try {
            $sql = <<<SQL
            SELECT
                l.id,
                c.id   AS cliente_id,
                c.nome AS cliente_nome,
                c.telefone AS cliente_telefone,
                c.cpf  AS cliente_cpf,
                f.id   AS funcionario_id,
                f.nome AS funcionario_nome,
                f.telefone AS funcionario_telefone,
                l.data_hora_locacao,
                l.horas_contratadas,
                l.data_hora_entrega_prevista,
                l.desconto_aplicado,
                l.valor_total_previsto,
                l.status
            FROM locacao l
            JOIN cliente c    ON c.id = l.cliente_id
            JOIN funcionario f ON f.id = l.funcionario_id
            WHERE l.id = :id
        SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();

            if (empty($row)) {
                throw RepositorioException::com(["Locação não encontrada."]);
            }
            return LocacaoResumo::fromArray($row);
        } catch (PDOException $e) {
            throw RepositorioException::com([$e->getMessage()]);
        }
    }

    public function buscarTodos(): array
    {
        try {
            $sql = <<<SQL
        SELECT
            l.id,
            c.id   AS cliente_id,
            c.nome AS cliente_nome,
            c.telefone AS cliente_telefone,
            c.cpf  AS cliente_cpf,
            f.id   AS funcionario_id,
            f.nome AS funcionario_nome,
            l.data_hora_locacao,
            l.horas_contratadas,
            l.data_hora_entrega_prevista,
            l.desconto_aplicado,
            l.valor_total_previsto,
            l.status
        FROM locacao l
        JOIN cliente c    ON c.id = l.cliente_id
        JOIN funcionario f ON f.id = l.funcionario_id
        ORDER BY l.id DESC
        SQL;

            $rows = $this->pdo->query($sql)
                ->fetchAll();
            $array = [];
            foreach ($rows as $row) {
                $array[] = LocacaoResumo::fromArray($row);
            }
            return $array;
        } catch (PDOException $e) {
            throw RepositorioException::com([$e->getMessage()]);
        }
    }

    public function buscarPorClienteId(int $cliente_id): array
    {
        //todo
        return [];
    }
    public function atualizarStatus(int $id, string $status): bool
    {
        try {
            $sql = "UPDATE locacao SET status = :status WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'id'     => $id,
                'status' => $status,
            ]);
        } catch (PDOException $e) {
            throw new RepositorioException($e->getMessage());
        }
    }
    public function buscarPorIdModel(int $id): ?Locacao
    {
        try {
            $sql = <<<SQL
            SELECT *
            FROM locacao
            WHERE id = :id
        SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();
            if (!$row) {
                return null;
            }
            $clienteRepo = new ClienteRepositorioEmBDR($this->pdo);
            $cliente = $clienteRepo->buscarPorId((int)$row['cliente_id']);
            if (!$cliente) {
                throw new RepositorioException("Cliente não encontrado para a locação.");
            }
            $funcionarioRepo = new FuncionarioRepositorioEmBDR($this->pdo);
            $funcionario = $funcionarioRepo->buscarPorId((int)$row['funcionario_id']);
            if (!$funcionario) {
                throw new RepositorioException("Funcionário não encontrado para a locação.");
            }
            $locacaoItemRepo = new LocacaoItemRepositorioEmBDR($this->pdo);
            $itemRepo = new ItemRepositorioEmBDR($this->pdo);
            $itensResumo = $locacaoItemRepo->buscarPorLocacaoId((int)$row['id']); // LocacaoItemResumo[]
            $itens = [];
            foreach ($itensResumo as $itemResumo) {
                $item = $itemRepo->buscarPorId($itemResumo->getItemId());
                if ($item) {
                    $itens[] = new LocacaoItem(
                        locacao: null,
                        item: $item,
                        valorHora: $itemResumo->getValorHora()
                    );
                }
            }
            $locacao = new Locacao(
                cliente: $cliente,
                funcionario: $funcionario,
                horasContratadas: (int)$row['horas_contratadas'],
                itens: $itens
            );
            $locacao->setId((int)$row['id']);
            $locacao->setInicio(new DateTimeImmutable($row['data_hora_locacao'], new DateTimeZone('America/Sao_Paulo')));
            $locacao->setPrevista(new DateTimeImmutable($row['data_hora_entrega_prevista'], new DateTimeZone('America/Sao_Paulo')));
            $locacao->setDesconto((float)$row['desconto_aplicado']);
            $locacao->setValorTotal((float)$row['valor_total_previsto']);
            $locacao->setStatus($row['status']);
            foreach ($itens as $locacaoItem) {
                $locacaoItem->setLocacao($locacao);
            }
            return $locacao;
        } catch (PDOException $e) {
            throw new RepositorioException($e->getMessage());
        }
    }
}
