<!-- User Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('user_id', 'User Id:') !!}
    {!! Form::select('user_id', [], null, ['class' => 'form-control custom-select']) !!}
</div>

<!-- Opcion Field -->
<div class="form-group col-sm-6">
    {!! Form::label('opcion', 'Opcion:') !!}
    {!! Form::text('opcion', null, ['class' => 'form-control', 'required']) !!}
</div>