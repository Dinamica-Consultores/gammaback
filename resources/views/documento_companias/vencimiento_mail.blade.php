<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALERTA DE VENCIMIENTO DE DOCUMENTO - GAMMA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            text-align: center;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        .alerta-header {
            text-align: center;
            background-color: #ff0000; /* Rojo de alerta */
            color: white;
            padding: 15px;
            font-size: 1.2em;
            font-weight: bold;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .highlight-danger {
            font-weight: bold;
            color: #ff0000; /* Rojo */
        }
        .details-box {
            background-color: #fff3cd; /* Amarillo suave para la alerta */
            border: 1px solid #ffc107; /* Borde amarillo */
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #0000ff; /* Azul para el botón */
            color: white !important;
            text-decoration: none;
            text-align: center;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 25px;
        }
    </style>
</head>

<body>
    <div class="container">
        
        <div class="alerta-header">
            ALERTA: VENCIMIENTO DE DOCUMENTO
        </div>
        
        <p>Estimado/a <span class="highlight-danger">{{ $dato['datosCompniaNombre'] }}</span>,</p>

        <p>Le escribimos para informarle que el siguiente documento asociado a su cuenta está **próximo a vencer** o **ya ha expirado**.</p>
        
        <div class="details-box">
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Documento:</span> {{ $dato['nombre_documento'] }}</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Tipo Documento:</span> {{ $dato['datosTipoDocumento'] }}</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Fecha de Vencimiento:</span> <span class="highlight-danger">{{ \Carbon\Carbon::parse( $dato['fecha_vencimiento'])->format('d/m/Y') }}</span></p>
        </div>

        <p style="font-weight: bold;">👉 Por favor, gestione la renovación antes de esta fecha para evitar interrupciones en el servicio.</p>

        <p style="margin-top: 30px;">
        Si el documento ya ha sido renovado, por favor, ignore este mensaje. Si necesita ayuda, no dude en contactarnos.
        </p>

        <p style="font-weight:bold; margin-top: 20px;">Gracias por su atención inmediata.</p>
        
        <p style="margin-top: 30px;">Saludos cordiales,</p>

        <p>Equipo de Gestión Empresarial.</p>

    </div>
</body>
</html>