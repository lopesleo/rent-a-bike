<?php

class AvariaDTO
{
    public function __construct(
        public int $id,
        public int $itemId,
        public string $descricao,
        public ?string $foto = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            itemId: (int) ($data['item_id'] ?? 0),
            descricao: (string) ($data['descricao'] ?? ''),
            foto: $data['foto'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->itemId,
            'descricao' => $this->descricao,
            'foto' => $this->foto,
        ];
    }
}
