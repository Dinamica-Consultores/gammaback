<!-- Nombre Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombre', 'Nombre:') !!}
    {!! Form::text('nombre', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Id Excel Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_excel', 'Id Excel:') !!}
    {!! Form::select('id_excel',$excels, null, ['class' => 'form-control custom-select']) !!}
</div>