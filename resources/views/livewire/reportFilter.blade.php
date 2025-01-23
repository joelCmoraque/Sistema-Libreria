<!DOCTYPE html>
<html>
<head>


    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 100px;
            margin-right: 20px;
        }
        .header h1 {
            margin: 0;
            align-items: center;
            text-align: center;
            color: #0044cc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center; /* Centrar el contenido */
        }
        th {
            background-color: #2365eb;
            color: white;
        }

        .text-left {
    text-align: left !important; /* Alinea el texto a la izquierda */
}
    </style>
</head>
<body>

<div class="header">
    <img src="{{ ('images/logo2.png') }}" alt="Logo">
    <h1>Reporte de Entrada de Productos  {{ $dateTime }}</h1>
</div>



<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>Fecha</th>
            <th>Producto</th>
            <th>Marca</th>
            <th>Costo unitario</th>
            <th>Cantidad</th>
           
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->id }}</td>
                <td>{{ \Carbon\Carbon::parse($record->fecha_entrada)->format('Y-m-d') }}</td>
                <td class="text-left">{{ $record->product->nombre }}</td>
                <td class="text-left">{{ $record->product->brand->nombre }}</td>
                <td>{{ $record->costo_unitario }}</td>
                <td>{{ $record->cantidad }}</td>
                
            </tr>
        @endforeach
    </tbody>
  
</table>

</body>
</html>
