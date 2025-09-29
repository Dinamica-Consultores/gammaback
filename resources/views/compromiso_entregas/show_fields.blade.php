
<div class="col-sm-12">
    {!! Form::label('fecha_reunion', 'Fecha Reunion:') !!}
    <p>{{ $compromisoEntrega->fecha_reunion->format('d/m/Y')  }}</p>
</div>
<!-- Fecha Entrega Field -->
<div class="col-sm-12">
    {!! Form::label('fecha_entrega', 'Fecha Entrega:') !!}
    <p>{{ $compromisoEntrega->fecha_entrega->format('d/m/Y')  }}</p>
</div>
<!-- Descripcion Entrega Field -->
<div class="col-sm-12">
    {!! Form::label('descripcion_entrega', 'Descripcion Entrega:') !!}
    <p>{{ $compromisoEntrega->descripcion_entrega }}</p>
</div>


<!-- Usuario Field -->
<div class="col-sm-12">
    {!! Form::label('usuario', 'Usuario:') !!}
    <p>{{ $compromisoEntrega->user->email }}</p>
</div>

<!-- Id Company Field -->
<div class="col-sm-12">
    {!! Form::label('id_company', 'Compañia:') !!}
    <p>{{ $compromisoEntrega->company->razon_social }}</p>
</div>
<!-- Fecha Entregado Field -->
<div class="col-sm-12">
    {!! Form::label('fecha_entregado', 'Fecha Entregado:') !!}
    <p>{{ $compromisoEntrega->fecha_entregado?->format('d/m/Y')  }}</p>
</div>
<div class="col-sm-12">
    {!! Form::label('descripcion_entregado', 'Descripcion Entregado:') !!}
    <p>{{ $compromisoEntrega->descripcion_entregado }}</p>
</div>
<!-- Usuario Field -->
<div class="col-sm-12">
    {!! Form::label('usuario', 'Usuario Entregado:') !!}
    <p>{{ $compromisoEntrega->userEntrega?->email }}</p>
</div>

