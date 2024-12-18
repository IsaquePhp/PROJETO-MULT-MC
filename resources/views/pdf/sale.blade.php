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
            font-size: 16px;
        }
        .header {
            background-color: #3949AB;
            color: white;
            padding: 25px;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .logo {
            font-family: Arial, sans-serif;
            font-size: 48px;
            font-weight: lighter;
            margin-right: 25px;
            padding-right: 25px;
            border-right: 1px solid white;
        }
        .company-info {
            font-size: 14px;
            line-height: 1.6;
            margin-left: 5px;
        }
        .content {
            padding: 25px;
        }
        .order-info {
            margin-bottom: 25px;
            font-size: 16px;
            line-height: 1.8;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background-color: #3949AB;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: normal;
            font-size: 16px;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 16px;
        }
        .totals {
            text-align: right;
            font-size: 16px;
            line-height: 2;
            padding-right: 25px;
        }
        .total-blue {
            color: #3949AB;
            font-size: 18px;
        }
        .footer {
            position: fixed;
            bottom: 25px;
            width: 100%;
            text-align: center;
            font-size: 14px;
        }
        .qr-section {
            position: fixed;
            bottom: 70px;
            left: 25px;
            font-size: 14px;
            line-height: 1.6;
        }
        .page-number {
            position: fixed;
            bottom: 25px;
            right: 25px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">LojaMC</div>
        <div class="company-info">
            CNPJ: 12.345.678/0001-90<br>
            Telefone: (31) 3434-3434 | WhatsApp: (31) 99999-9999<br>
            R. dos Marmelos, 97 - Vila Clóris, Belo Horizonte - MG, 31744-093<br>
            Email: contato@lojamc.com.br | Site: www.lojamc.com.br
        </div>
    </div>

    <div class="content">
        <div class="order-info">
            <strong>Pedido #{{ $sale->id }}</strong><br>
            Data: {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y, h:i:s A') }}<br>
            Cliente: {{ $sale->customer->name }}
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Qtde</th>
                    <th>Unitário</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div style="margin-bottom: 8px;">Subtotal: R$ {{ number_format($sale->items->sum(function($item) { return $item->quantity * $item->unit_price; }), 2, ',', '.') }}</div>
            <div style="margin-bottom: 8px;">Entrega: R$ {{ number_format(0, 2, ',', '.') }}</div>
            <div class="total-blue">Total: R$ {{ number_format($sale->items->sum(function($item) { return $item->quantity * $item->unit_price; }), 2, ',', '.') }}</div>
        </div>
    </div>

    <div class="qr-section">
        Para verificar a autenticidade deste comprovante,<br>
        acesse: www.lojamc.com.br/verify<br>
        e informe o código: {{ $sale->id }}
    </div>

    <div class="footer">
        Sua loja, seu estilo!
    </div>

    <div class="page-number">
        Página 1 de 1
    </div>
</body>
</html>
