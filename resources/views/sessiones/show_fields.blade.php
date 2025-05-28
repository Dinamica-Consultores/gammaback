<!-- User Id Field -->
<div class="col-sm-12">
    {!! Form::label('user_id', 'User Id:') !!}
    <p>{{ $sessiones->user_id }}</p>
</div>

<!-- Opcion Field -->
<div class="col-sm-12">
    {!! Form::label('opcion', 'Opcion:') !!}
    <p>{{ $sessiones->opcion }}</p>
</div>

<!-- Created At Field -->
<div class="col-sm-12">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $sessiones->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="col-sm-12">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $sessiones->updated_at }}</p>
</div>

