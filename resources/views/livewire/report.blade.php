<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Producto</th>
            <th>Proveedor</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->fecha }}</td>
                <td>{{ $record->product->nombre }}</td>
                <td>{{ $record->product->provider->razon_social }}</td>
                <td>{{ $record->cantidad }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
