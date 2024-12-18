<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        @page {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
            font-size: 14px;
            line-height: 1.4;
        }
        .header {
            background-color: #3F51B5;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-right: 20px;
            padding-right: 20px;
            border-right: 2px solid white;
        }
        .company-info {
            font-size: 14px;
            line-height: 1.5;
        }
        .company-info p {
            margin: 0;
        }
        .content {
            padding: 20px;
        }
        .order-info {
            margin-bottom: 20px;
        }
        .order-info p {
            margin: 5px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background-color: #3F51B5;
            color: white;
            padding: 8px;
            text-align: left;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .totals p {
            margin: 5px 0;
        }
        .total {
            color: #3F51B5;
            font-weight: bold;
            font-size: 16px;
        }
        .observation {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        .observation-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">LojaMC</div>
        <div class="company-info">
            <p>CNPJ: 12.345.678/0001-90</p>
            <p>Telefone: (31) 3434-3434 | WhatsApp: (31) 99999-9999</p>
            <p>R. dos Marmelos, 97 - Vila Cloris, Belo Horizonte - MG, 31744-093</p>
            <p>Email: contato@lojasmc.com.br | Site: www.lojasmc.com.br</p>
        </div>
    </div>

    <div class="content">
        <div class="order-info">
            <p><strong>Pedido #{{ $sale->id }}</strong></p>
            <p>Data: {{ date('d/m/Y, H:i:s', strtotime($sale->created_at)) }}</p>
            <p>Cliente: {{ $sale->customer->name }}</p>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th style="text-align: center;">Qtde</th>
                    <th style="text-align: right;">Unitário</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td style="text-align: right;">R$ {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <p>Subtotal: R$ {{ number_format($sale->subtotal, 2, ',', '.') }}</p>
            <p>Entrega: R$ {{ number_format($sale->shipping_cost, 2, ',', '.') }}</p>
            <p class="total">Total: R$ {{ number_format($sale->total, 2, ',', '.') }}</p>
        </div>

        <div class="observation">
            <p class="observation-title">Observação:</p>
            <p>{{ $sale->observation ?? 'Nenhuma observação adicional.' }}</p>
        </div>
    </div>

    <div class="footer">
        Página 1 de 1
    </div>
</body>
</html>
