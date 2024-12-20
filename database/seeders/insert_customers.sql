-- Inserir 10 clientes de exemplo
INSERT INTO customers (name, email, phone, cpf_cnpj, type, status, address, address_number, neighborhood, city, state, zip_code, company_id, created_at, updated_at) VALUES
('João Silva', 'joao.silva@email.com', '(11) 98765-4321', '123.456.789-01', 'individual', 'active', 'Rua das Flores', '123', 'Centro', 'São Paulo', 'SP', '01234-567', 1, NOW(), NOW()),
('Maria Santos', 'maria.santos@email.com', '(11) 97654-3210', '987.654.321-02', 'individual', 'active', 'Avenida Principal', '456', 'Jardim Europa', 'São Paulo', 'SP', '04567-890', 1, NOW(), NOW()),
('Pedro Oliveira', 'pedro.oliveira@email.com', '(11) 96543-2109', '456.789.123-03', 'individual', 'active', 'Rua dos Pinheiros', '789', 'Pinheiros', 'São Paulo', 'SP', '05678-901', 1, NOW(), NOW()),
('Ana Costa', 'ana.costa@email.com', '(11) 95432-1098', '789.123.456-04', 'individual', 'active', 'Rua Augusta', '1011', 'Consolação', 'São Paulo', 'SP', '01234-567', 1, NOW(), NOW()),
('Carlos Ferreira', 'carlos.ferreira@email.com', '(11) 94321-0987', '321.654.987-05', 'individual', 'active', 'Avenida Paulista', '1213', 'Bela Vista', 'São Paulo', 'SP', '04567-890', 1, NOW(), NOW()),
('Mercado do Zé LTDA', 'contato@mercadoze.com', '(11) 93210-9876', '12.345.678/0001-01', 'company', 'active', 'Rua Comercial', '100', 'Centro', 'São Paulo', 'SP', '01234-567', 1, NOW(), NOW()),
('Padaria Bom Pão LTDA', 'contato@bompao.com', '(11) 92109-8765', '23.456.789/0001-02', 'company', 'active', 'Avenida do Comércio', '200', 'Vila Mariana', 'São Paulo', 'SP', '04567-890', 1, NOW(), NOW()),
('Lucia Pereira', 'lucia.pereira@email.com', '(11) 91098-7654', '654.321.987-06', 'individual', 'active', 'Rua das Palmeiras', '1415', 'Moema', 'São Paulo', 'SP', '05678-901', 1, NOW(), NOW()),
('Roberto Almeida', 'roberto.almeida@email.com', '(11) 90987-6543', '987.654.321-07', 'individual', 'active', 'Rua dos Jardins', '1617', 'Jardins', 'São Paulo', 'SP', '01234-567', 1, NOW(), NOW()),
('Supermercado Bom Preço LTDA', 'contato@bompreco.com', '(11) 89876-5432', '34.567.890/0001-03', 'company', 'active', 'Avenida do Povo', '300', 'Tatuapé', 'São Paulo', 'SP', '04567-890', 1, NOW(), NOW());
