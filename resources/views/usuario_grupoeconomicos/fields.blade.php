

@if(isset($infoEdit))
<div class="form-group col-sm-6">
    {!! Form::label('id_users', 'Email:') !!}
    {!! Form::hidden('id_users', $infoEdit['id_users']) !!}
    {{$infoEdit['email']}}
</div>
<div class="form-group col-sm-6">
    
    {!! Form::label('id_grupoeconomico', 'Red Comercial:') !!}
    {!! Form::select('id_grupoeconomico', $grupoEconomico, $infoEdit['grupoEconomico'], ['class' => 'form-control custom-select','multiple'=>'multiple','name'=>'id_grupoeconomico[]']) !!}
</div>
@else
<!-- Id Users Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_users', 'Usuarios:') !!}
    {!! Form::select('id_users', $usuarios, null, ['class' => 'form-control custom-select']) !!}
</div>
<div class="form-group col-sm-6">
    
    {!! Form::label('id_grupoeconomico', 'Red Comercial:') !!}
    {!! Form::select('id_grupoeconomico', $grupoEconomico,null, ['class' => 'form-control custom-select','multiple'=>'multiple','name'=>'id_grupoeconomico[]']) !!}
</div>
@endif
@push('page_scripts')
<script>
$(document).ready(function(){
    
    var multipleCancelButton = new Choices('#id_grupoeconomico', {
       removeItemButton: true,
       searchResultLimit:5,
       renderChoiceLimit:5
     }); 
    
    
});

</script>
@endpush