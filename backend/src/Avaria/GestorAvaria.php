<?php

class GestorAvaria
{
    private IAvariaRepositorio $avariaRepositorio;

    public function __construct(
        IAvariaRepositorio $avariaRepositorio,
    ) {
        $this->avariaRepositorio = $avariaRepositorio;
    }

    public function criarAvaria(Avaria $avaria): Avaria
    {
        $erros = $avaria->validar();
        if (!empty($erros)) {
            throw new InvalidArgumentException(implode(', ', $erros));
        }

        return $this->avariaRepositorio->criar($avaria);
    }

    public function buscarPorId(int $id): ?Avaria
    {
        return $this->avariaRepositorio->buscarPorId($id);
    }

    public function atualizarAvaria(Avaria $avaria): void
    {
        $erros = $avaria->validar();
        if (!empty($erros)) {
            throw new InvalidArgumentException(implode(', ', $erros));
        }

        $this->avariaRepositorio->atualizar($avaria);
    }

    public function atualizarFoto(int $id, string $caminhoFoto): void
    {
        if (empty($caminhoFoto)) {
            throw new InvalidArgumentException('Caminho da foto não pode ser vazio.');
        }

        $this->avariaRepositorio->atualizarFoto($id, $caminhoFoto);
    }
    public function buscarPorItem(int $itemId): array
    {
        if ($itemId <= 0) {
            throw new InvalidArgumentException('ID do item inválido.');
        }

        return $this->avariaRepositorio->buscarPorItem($itemId);
    }
    public function remover(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('ID inválido.');
        }

        $this->avariaRepositorio->remover($id);
    }
}
