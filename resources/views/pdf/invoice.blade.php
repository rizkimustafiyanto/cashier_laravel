<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>
        Invoice
    </title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }
    </style>
</head>
<body>

    <h2>
        Medical Transaction Invoice
    </h2>

    <p>
        Invoice:
        {{ $transaction->invoice_number }}
    </p>

    <p>
        Patient:
        {{ $transaction->patient_name }}
    </p>

    <p>
        Cashier:
        {{ $transaction->cashier->name }}
    </p>

    <table>

        <thead>
            <tr>
                <th>Procedure</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Final</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($transaction->items as $item)

                <tr>
                    <td>
                        {{ $item->procedure_name }}
                    </td>

                    <td>
                        Rp {{ number_format($item->base_price) }}
                    </td>

                    <td>
                        Rp {{ number_format($item->discount_amount) }}
                    </td>

                    <td>
                        Rp {{ number_format($item->final_price) }}
                    </td>
                </tr>

            @endforeach

        </tbody>

    </table>

    <h3>
        Grand Total:
        Rp {{ number_format($transaction->grand_total) }}
    </h3>

</body>
</html>