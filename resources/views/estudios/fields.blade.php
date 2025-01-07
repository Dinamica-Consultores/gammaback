<!-- Razon Social Field -->
<div class="form-group col-sm-6">
    {!! Form::label('razon_social', 'Razon Social:') !!}
    {!! Form::text('razon_social', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255]) !!}
</div>

<!-- Per Cont Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('per_cont_name', 'Persona de Contacto:') !!}
    {!! Form::text('per_cont_name', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255]) !!}
</div>


<!-- Per Cont Phone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('per_cont_phone', 'Numero de Contacto:') !!}
    {!! Form::text('per_cont_phone', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255,'name'=>'per_cont_phone']) !!}
</div>


<div class="form-group col-sm-6">
{!! Form::label('cantida_empresa_max', 'Cantidad de Companias Max (Dejar Vacio para infinito):') !!}
    {!! Form::number('cantida_empresa_max', null, ['class' => 'form-control']) !!}
</div>

<!-- Campo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('campo', 'Descripcion:') !!}
    {!! Form::text('campo', null, ['class' => 'form-control']) !!}
</div>
<div class="form-group col-sm-6">
    {!! Form::label('es_empresa', 'Es Empresa:') !!}
    {!! Form::checkbox('es_empresa', '1') !!}
</div>
@push('page_scripts')
<script>
    
$(document).ready(function(){
    const input = document.querySelector("#per_cont_phone");
  window.intlTelInput(input, {
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/utils.js",
    countrySearch:false,
    initialCountry: "UY",
  nationalMode: true,
  strictMode: true,
  onlyCountries:['UY']
  });
})

</script>
@endpush