<?php

class DevolucaoRepositorioEmBDR implements IDevolucaoRepositorio{
    public function __construct(
        private PDO $pdo,
    ) {}

    public function salvar(Devolucao $devolucao): int
    {
        $sql = "INSERT INTO devolucao (locacao_id, funcionario_id, data_hora, horas_usadas, desconto_aplicado, valor_pago)
                VALUES (:locacao_id, :funcionario_id, :data_hora, :horas_usadas, :desconto_aplicado, :valor_pago)";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([
                'locacao_id' => $devolucao->getLocacao()->getId(),
                'funcionario_id' => $devolucao->getFuncionario()->getId(),
                'data_hora' => $devolucao->getDataHora()->format('Y-m-d H:i:s'),
                'horas_usadas' => $devolucao->getHorasUsadas(),
                'desconto_aplicado' => $devolucao->getDescontoAplicado(),
                'valor_pago' => $devolucao->getValorPago(),
            ]);
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function buscarPorDevolucaoId(int $id): array
    {
        $sql = "SELECT * FROM devolucao WHERE locacao_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $rows =  $stmt->fetchAll();
        return array_map(function ($row) {
            return DevolucaoResumo::fromArray($row);
        }, $rows);
    }

    
    public function buscarTodos(): array
    {
        $sql = "SELECT * FROM devolucao order by data_hora DESC";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();
        $devolucoes = [];
        foreach ($rows as $row) {
            $locacaoRepo = new LocacaoRepositorioEmBDR($this->pdo);
            $locacaoResumo = $locacaoRepo->buscarPorId($row['locacao_id']);
            $funcionarioRepo = new FuncionarioRepositorioEmBDR($this->pdo);
            $funcionario = $funcionarioRepo->buscarPorId($row['funcionario_id']);
            $devolucoes[] = new DevolucaoResumo(
                id: (int)$row['id'],
                cliente: $locacaoResumo->getCliente(),
                funcionario: [
                    'id' => $funcionario->getId(),
                    'nome' => $funcionario->getNome()
                ],
                dataHora: new DateTimeImmutable($row['data_hora']),
                locacaoId: (int)$row['locacao_id'],
                desconto: (float)$row['desconto_aplicado'],
                valorPago: (float)$row['valor_pago'],
            );
        }
        return $devolucoes;
    }

    public function atualizar(Devolucao $devolucao): bool
    {
        throw new Exception("Não Implementado");
    }

    public function deletarPorId($id): bool
    {
        $sql = "DELETE FROM devolucao WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function buscarPorId(int $id): DevolucaoResumo
    {
        $sql = "SELECT * FROM devolucao WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new RepositorioException("Devolução não encontrada.");
        }
        $locacaoRepo = new LocacaoRepositorioEmBDR($this->pdo);
        $locacaoResumo = $locacaoRepo->buscarPorId($row['locacao_id']);
        $funcionarioRepo = new FuncionarioRepositorioEmBDR($this->pdo);
        $funcionario = $funcionarioRepo->buscarPorId($row['funcionario_id']);

        return new DevolucaoResumo(
            id: (int)$row['id'],
            cliente: $locacaoResumo->getCliente(),
            funcionario: [
                'id' => $funcionario->getId(),
                'nome' => $funcionario->getNome()
            ],
            dataHora: new DateTimeImmutable($row['data_hora']),
            locacaoId: (int)$row['locacao_id'],
            desconto: (float)$row['desconto_aplicado'],
            valorPago: (float)$row['valor_pago']
        );
    }

    public function remover(int $locacaoId): bool
    {
        $sql = "DELETE FROM devolucao WHERE locacao_id = :locacaoId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['locacaoId' => $locacaoId]);
    }
}
?>