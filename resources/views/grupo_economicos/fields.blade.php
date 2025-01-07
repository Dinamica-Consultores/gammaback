<!-- Nombre Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombre', 'Nombre:') !!}
    {!! Form::text('nombre', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255]) !!}
</div>
<div class="form-group col-sm-12">
    {!! Form::label('id_moneda', 'Tipo de Moneda:') !!}
    {!! Form::select('id_moneda', ['0'=>'Pesos Uruguayo','1'=>'Dolares Americanos'], null, ['class' => 'form-control custom-select','required']) !!}
</div>