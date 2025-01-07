@if(isset($infoEdit))
<div class="form-group col-sm-6">
    {!! Form::label('id_grupoeconomico', 'Red Comercial:') !!}
    {!! Form::hidden('id_grupoeconomico', $infoEdit['id_grupoeconomico']) !!}
    {{$infoEdit['id_grupoeconomiconame']}}
</div>
<div class="form-group col-sm-6">
    
    {!! Form::label('id_company', 'Compañias:') !!}
    {!! Form::select('id_company', $company, $infoEdit['id_company'], ['class' => 'form-control custom-select','multiple'=>'multiple','name'=>'id_company[]']) !!}
</div>
@else
<!-- Id Users Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_grupoeconomico', 'Red Comercial:') !!}
    {!! Form::select('id_grupoeconomico', $grupoEconomico, null, ['class' => 'form-control custom-select']) !!}
</div>

<!-- Id Company Field -->
<div class="form-group col-sm-6">
    
    {!! Form::label('id_company', 'Compañias:') !!}
    {!! Form::select('id_company', $company, null, ['class' => 'form-control custom-select','multiple'=>'multiple','name'=>'id_company[]']) !!}
</div>
@endif
@push('page_scripts')
<script>
    var informacion={!! json_encode($company) !!};
    var informacion2={!! json_encode($grupoEconomico2) !!};
    var todasCompaniasSelect=[]
    <?php
    if(isset($infoEdit)){
        ?>

todasCompaniasSelect={!! json_encode($infoEdit['id_company']) !!};
        <?php
    }
    ?>
    function agregarOptionesNivel(listado,idSelect,listado2,multipleCancelButton,todasCompaniasSelect){
    multipleCancelButton.clearChoices()
    multipleCancelButton.clearStore()
        $('#id_company').find('option').remove().end();
    let valor=listado2.find((df)=>Number(df.id)===Number(idSelect))
        console.log(valor)
    if(valor){
    listado.filter((ds)=>Number(ds.id_moneda)===Number(valor.id_moneda)).forEach(element => {
        $('#id_company').append($('<option>', { 
        value: element.id,
        text : element.razon_social 
    }));
    })
    multipleCancelButton.setChoices(listado.filter((ds)=>Number(ds.id_moneda)===Number(valor.id_moneda)).map((element)=>{
        let selected=false
        if(todasCompaniasSelect.find((das)=>Number(das)===Number(element.id))){
            selected=true
        }
        return({
        value:element.id,label:element.razon_social ,selected:selected
    })}),'value',
  'label',
  false,)
    }
 
}
    
$(document).ready(function(){
    var multipleCancelButton = new Choices('#id_company', {
       removeItemButton: true,
       searchResultLimit:5,
       renderChoiceLimit:5,
     }); 
    agregarOptionesNivel(informacion,$( "#id_grupoeconomico" ).val(),informacion2,multipleCancelButton,todasCompaniasSelect)
     $( "#id_grupoeconomico" ).on( "change", function() {
     agregarOptionesNivel(informacion,$( "#id_grupoeconomico" ).val(),informacion2,multipleCancelButton,todasCompaniasSelect)
} )

});

</script>
@endpush