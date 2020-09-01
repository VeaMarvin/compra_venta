 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de venta</title>
    <style>
        body {
        /*position: relative;*/
        width: 12.26cm;  
        margin-top: 1cm;
        margin-left: 0.5cm;
        /*color: #555555;*/
        /*background: #FFFFFF; */
        /*font-family: SourceSansPro;*/
        }

        #encabezado{
        font-family: Arial, sans-serif; 
        font-size: 12px;
        line-height: 17px;
        }

        #fecha{
        margin-top: 2.24cm;
        margin-left: 5.36cm;
        text-align: left;
        }

        #nombre{
        margin-left: 1.72cm;
        text-align: left;
        }

        #direccion{
        margin-left: 2.05cm;
        text-align: left;
        }

        #nit{
        margin-left: 8.18cm;
        text-align: left;
        }

        #facarticulo{
        font-family: Arial, sans-serif; 
        font-size: 12px;

        margin-top: 0.9cm;
        margin-left: 2.05cm;
        width: 12.12cm;
        line-height: 20px;

        border: none;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 15px;
        }
        
    </style>
 </head>
 <body>
     @foreach ($venta as $item)
        <header>
            <div>
                <table id="encabezado">
                    <tbody>
                        <tr>
                            <td>
                                <div id="fecha">
                                    {{ $dia }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ $mes }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ $anio }}
                                </div>
                            </td>
                        </tr>
                        <tr >
                            <td><div id="nombre">Sr(a). {{ $item->nombre }}</div></td>
                        </tr>
                        <tr>
                            <td><div id="direccion">{{ $item->direccion }}</div></td>
                        </tr>
                        <tr>
                            <td><div id="nit">{{ $item->numero_documento }}</div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </header>
    @endforeach
    <section>
        <div>
            <table id="facarticulo">
                <tbody>
                    @foreach ($detalles as $item)
                        <tr>    
                            <td style="text-align:center; width: 1.14cm;"> {{ $item->cantidad }} </td>
                            <td style="text-align:left; width: 7.8cm;"> {{ $item->nombre }} </td>
                            <td style="text-align:right; width: 1.7cm;"> {{ $item->cantidad * $item->precio }} </td>
                        </tr>
                    @endforeach
                    {!! $filas !!}
                    @foreach ($venta as $item)
                    @if($item->descuento > 0)
                        <tr>    
                            <td> Descuento </td>
                            <td style="text-align:center;">  </td>
                            <td style="text-align:right;"> {{ $item->precio }} </td>
                            <td style="text-align:right;"> - {{ $item->descuento }} </td>
                        </tr>
                    @endif
                    @endforeach
                </tbody>
                <tfoot>
                    @foreach ($venta as $item)
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="text-align:right;"> <b>{{ $item->total }}</b> </td>
                    </tr>
                    @endforeach
                </tfoot>
            </table>
        </div>
    </section>
 </body>
 </html>