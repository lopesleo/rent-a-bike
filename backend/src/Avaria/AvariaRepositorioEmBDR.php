<?php

class AvariaRepositorioEmBDR implements IAvariaRepositorio
{

    public function __construct(private PDO $conexao) {}

    public function criar(Avaria $avaria): Avaria
    {
        $stmt = $this->conexao->prepare(
            'INSERT INTO avaria (data_hora, funcionario_id, descricao, valor, foto, item_id, devolucao_id) 
             VALUES (:data_hora, :funcionario_id, :descricao, :valor, :foto, :item_id, :devolucao_id)'
        );

        $stmt->execute([
            ':data_hora' => $avaria->getDataHora()->format('Y-m-d H:i:s'),
            ':funcionario_id' => $avaria->getFuncionario()->getId(),
            ':descricao' => $avaria->getDescricao(),
            ':valor' => $avaria->getValor(),
            ':foto' => $avaria->getFoto(),
            ':item_id' => $avaria->getItem()->getId(),
            ':devolucao_id' => $avaria->getDevolucao()->getId(),
        ]);

        $avaria->setId((int) $this->conexao->lastInsertId());
        return $avaria;
    }

    public function atualizarFoto(int $id, string $caminhoFoto): void
    {
        $stmt = $this->conexao->prepare('UPDATE avaria SET foto = :foto WHERE id = :id');
        $stmt->execute([':foto' => $caminhoFoto, ':id' => $id]);
    }

    public function buscarPorId(int $id): ?Avaria
    {
        //todo
    }

    public function buscarTodos(): array
    {
        $stmt = $this->conexao->query('SELECT * FROM avaria');
        $avarias = $stmt->fetchAll(PDO::FETCH_CLASS, Avaria::class);
        return $avarias;
    }

    public function buscarPorItem(int $itemId): array
    {
        $query = "SELECT * FROM avaria WHERE item_id = ?";
        $stmt = $this->conexao->prepare($query);
        $stmt->execute([$itemId]);

        $resultados = $stmt->fetchAll();

        $avarias = [];

        foreach ($resultados as $row) {
            $avarias[] = AvariaDTO::fromArray($row);
        }

        return $avarias;
    }

    public function atualizar(Avaria $avaria): void {}

    public function remover(int $id): void {}
}
