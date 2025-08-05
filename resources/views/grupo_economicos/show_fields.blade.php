<!-- Nombre Field -->
<div class="col-sm-12">
    {!! Form::label('nombre', 'Nombre:') !!}
    <p>{{ $grupoEconomicos->nombre }}</p>
</div>

<div class="col-sm-12">
    {!! Form::label('logo', 'Logo:') !!}
    <img src="{{ asset('storage/'.$grupoEconomicos->logo) }}" style="width:50px;height:50px;"/>

</div>