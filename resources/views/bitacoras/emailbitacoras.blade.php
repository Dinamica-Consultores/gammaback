<!DOCTYPE html>
<html lang="es">
<cabeza>
    <meta juego de caracteres="UTF-8">
    <title>Bitacora Red Comercial - {{$dato['nombre']}}</title>

</cabeza>

<body class="mantener-transición barra lateral-mini diseño-fijo" >
    </br>



    <p style="text-align:center;font-weight:bold;"> Titulo: {{$dato['titulo']}}</p>
</br> 

@foreach($dato['hitos'] as $hitos)
<p > <label style="font-weight:bold;">{{$hitos['numero']}} • Titulo:</label>  {{$hitos['titulo']}}</p>
<p > <label style="font-weight:bold;">Observaciones: </label> {{$hitos['descripcion']}}<p>
</br> 

@endforeach

    <p style="font-weight:bold;">¡Gracias por seguir confiando en nosotros!</p>
</br>
<p>Saludos,</p>

    <p>
    Equipo de Gestión Empresarial.</p>
</cuerpo>
</html>