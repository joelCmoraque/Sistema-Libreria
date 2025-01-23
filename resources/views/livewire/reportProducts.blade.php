<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <style>
        @page {
            margin-top: 0;
            margin-bottom: 0;
            margin-left: 0;
            margin-right: 0;
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
        }
    
        td, th {
            overflow: hidden;
            text-overflow: ellipsis;
            word-wrap: break-word;
        }
    </style>
</head>

<body>
    <div class="mt-4 ml-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img class="rounded" src="{{ public_path('images/logo2.png') }}" alt="" width="120px"
                height="120px">
            <span class="btn btn-primary" style="font-weight: bold; font-size: 18px; margin-left: 70px">
                REPORTE DE PRODUCTOS:
                {{ $dateTime }}
            </span>     
                      
        </div>
    </div>

    <div class="card-body mt-n4">
        <div class="table-responsive">
            {{-- Agrupar productos por categoría --}}
           
                <table class="table table-striped bg-white mt-n2" style="box-shadow: 5px 5px 3px gray;">
                    <thead class="thead bg-primary text-light" style="font-size: 8px; font-weight: bold;">
                        <tr>
                            <th class="text-center">Código</th>
                            <th class="text-center">Categoría</th>
                            <th class="text-center">Proveedor</th>
                            <th class="text-center">Depósito</th>
                            <th class="text-center">Marca</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Descripción</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Unidad de medida</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 9px">
                        @foreach ($records as $product)
                            <tr class="text-center">
                                <td>{{ $product->codigo_unico }}</td>
                                <td>{{ $product->category->nombre }}</td>
                                <td>{{ $product->provider->razon_social }}</td>
                                <td>{{ $product->deposit->nombre }}</td>
                                <td>{{ $product->brand->nombre }}</td>
                                <td>{{ $product->nombre }}</td>
                                <td>{{ $product->descripcion }}</td>
                                <td>{{ $product->precio_actual }} bs</td>
                                <td>{{ $product->stock_actual }}</td>
                                <td>{{ $product->unidad_medida }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
           
        </div>
    </div>

</body>

</html>
