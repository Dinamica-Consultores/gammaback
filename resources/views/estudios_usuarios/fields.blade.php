<!-- Id Users Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_users', 'Usuarios:') !!}
    {!! Form::select('id_users', $usuarios, null, ['class' => 'form-control custom-select']) !!}
</div>

<!-- Id Estudios Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_estudios', 'Estudio:') !!}
    {!! Form::select('id_estudios', $estudios, null, ['class' => 'form-control custom-select']) !!}
</div>