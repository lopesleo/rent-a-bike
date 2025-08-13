USE rent_a_bike;

-- Inserções de locações de exemplo
INSERT INTO locacao (
  id,
  cliente_id,
  funcionario_id,
  data_hora_locacao,
  horas_contratadas,
  data_hora_entrega_prevista,
  desconto_aplicado,
  valor_total_previsto,
  status
) VALUES
  -- Locação normal: 2 horas, sem desconto
  (1, 1, 1,
   '2025-05-20 09:00:00',
   2,
   '2025-05-20 11:00:00',
   0.00,
   50.00,  -- ex: 2h × R$25,00/h
   'EM_ANDAMENTO'),
  -- Outra locação normal: 1h, com 10% de desconto
  (2, 2, 2,
   '2025-05-22 14:30:00',
   1,
   '2025-05-22 15:30:00',
   5.00,   -- desconto
   20.00,  -- ex: 1h × R$25,00/h = 25.00 − 5.00
   'EM_ANDAMENTO');
