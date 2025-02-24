<!DOCTYPE html>
<html lang="es">
<cabeza>
    <meta juego de caracteres="UTF-8">
    <title>Usuario Generado Nuevo - GAMMA</title>

</cabeza>

<body class="mantener-transición barra lateral-mini diseño-fijo">
    </br>
    <p>Se ha creado un usuario nuevo para GAMMA nivel {{$dato['mensaje']}}</p>
</br> 
<p style="font-weigth:bold;">Utilice este link para Ingresar</p>
</br>
    <a href="{{$dato['href']}}" >Ir a la Web </a>
    </br>
   
    <p style="font-weigth:bold;">Este es el correo para ingresar: {{$dato['usuario']}}</p>
    </br>
    <p style="font-weigth:bold;">Este es su contrasena nueva: {{$dato['clave']}} </p>
</br>
    <p>¡Gracias por elegir GAMMA!</p>
</cuerpo>
</html>