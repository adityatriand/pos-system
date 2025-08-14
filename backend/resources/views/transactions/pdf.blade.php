<!DOCTYPE html>
<html>
<head>
    <title>Sales History Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary h3 {
            margin-top: 0;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales History Report</h1>
        <p>Generated on: {{ now()->format('d M Y H:i:s') }}</p>
        <p>Period: {{ $transactions->first()?->transaction_date?->format('d M Y') ?? 'N/A' }} - {{ $transactions->last()?->transaction_date?->format('d M Y') ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Transaction #</th>
                <th>Date</th>
                <th>Branch</th>
                <th>Employee</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Service Charge</th>
                <th class="text-right">Total</th>
                <th>Payment Method</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalSubtotal = 0;
                $totalServiceCharge = 0;
                $totalAmount = 0;
            @endphp
            
            @foreach($transactions as $transaction)
                @php
                    $totalSubtotal += $transaction->subtotal;
                    $totalServiceCharge += $transaction->service_charge;
                    $totalAmount += $transaction->total_amount;
                @endphp
                <tr>
                    <td>{{ $transaction->transaction_number }}</td>
                    <td>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->branch->name }}</td>
                    <td>{{ $transaction->employee->name }}</td>
                    <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($transaction->service_charge, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                    <td class="text-center">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                </tr>
            @endforeach
            
            <tr class="total-row">
                <td colspan="4"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalServiceCharge, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="summary">
        <h3>Summary</h3>
        <p><strong>Total Transactions:</strong> {{ $transactions->count() }}</p>
        <p><strong>Total Revenue:</strong> Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
        <p><strong>Total Service Charge:</strong> Rp {{ number_format($totalServiceCharge, 0, ',', '.') }}</p>
        <p><strong>Average Transaction:</strong> Rp {{ $transactions->count() > 0 ? number_format($totalAmount / $transactions->count(), 0, ',', '.') : '0' }}</p>
    </div>
</body>
</html>