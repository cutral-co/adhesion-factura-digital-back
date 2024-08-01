<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adhesión a Factura digital</title>

    <style>
        p {
            font-size: 16px;
        }
    </style>
</head>

<body>
    <table style="width: 100%; max-width: 650px; margin: 0 auto; padding: 20px;">
        <tr>
            <td style="padding-bottom: 20px;">
                <h1 style="text-align: center">Adhesión a Factura digital</h1>
                <p>
                    Su Adhesión a Factura Digital quedó confirmada y se ha generado una cuenta personal en la plataforma
                    municipal: <b>Cutral Co Digital</b>.
                </p>

                <p>Usted podrá acceder ingresando a <a href="{{$link_client}}" target="_blank">{{$link_client}}</a>,
                    utilizando su numero de CUIT, <b>{{$person->cuit}}</b> con la siguente contraseña:
                </p>

                <div
                    style="font-size: 32px; text-align:center; padding: 40px 0; background: #FCCA6B; border-radius: 5px;">
                    <b>{{$password}}</b>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
