<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="excelscompanies-table">
            <thead>
            <tr>
                <th>Archivo Subida Path</th>
                <th>Version</th>
                <th>Fecha</th>
                <th>Compania excel Subida</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($excelscompanies as $excelscompany)
                <tr>
                    <td>{{ $excelscompany->path }}</td>
                    <td>{{ $excelscompany->version }}</td>
                    <td>{{ $excelscompany->date }}</td>
                    <td>{{ $excelscompany->company->razon_social }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['excelscompanies.destroy', $excelscompany->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                           <a  id="abrirModalJQuery"
                           data-company-id="{{ $excelscompany->company->id }}"
                               class='btn btn-default btn-xs'>
                               <i class="far fa-clock"></i>
                            </a>
                            <a href="{{ route('excelscompanies.show', [$excelscompany->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>

                            {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $excelscompanies])
        </div>
    </div>


</div>
    
<div class="modal fade" id="miModalJQuery" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="miModalLabel">Compromiso de Entrega</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="botonGuardarDatos">Guardar Compromiso</button>
      </div>
    </div>
  </div>
</div>
@push('third_party_scripts')
@auth
<script>
    const authUserId = @json(Auth::user()->id);
</script>
@endauth
<script>
    
  $(document).ready(function() {
    $('#botonGuardarDatos').on('click', function() {
          if($("#descripcion_entregado_input").val()){
            $.ajax({
        url: '{{ route("compromiso.registro") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id_usuario: authUserId,
            id_compromiso:$("#compriso_id").val(),
            compromiso:$("#descripcion_entregado_input").val()
        },
        success: function(respuesta) {

const myModalInstance = bootstrap.Modal.getInstance(document.getElementById('miModalJQuery'));
if (myModalInstance) {
    myModalInstance.hide();
}
            alert("Registrado Correctamente")
        },
        error: function(xhr, status, error) {
            const myModalInstance = bootstrap.Modal.getInstance(document.getElementById('miModalJQuery'));

if (myModalInstance) {
    myModalInstance.hide();
}
        }
        })


          }else{
            alert("Debes ingresar un valor en la descripcion");
          }
    })

        $('#abrirModalJQuery').on('click', function() {
            var companyID = $(this).data('company-id');
            $.ajax({
        url: '/getAllCompromisoEntregar', 
        type: 'GET',
        dataType: 'json',
        data: {
            _token: '{{ csrf_token() }}',
            company_id: companyID,
            user_id: authUserId 
        },
                    success: function(data) {
                        if(data.fecha_reunion){
                            console.log(data)
                            const fechaReunion = new Date(data.fecha_reunion);
    const fechaEntrega = new Date(data.fecha_entrega);
    const formatoFecha = (date) => {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    };
    const fechaReunionFormateada = formatoFecha(fechaReunion);
    const fechaEntregaFormateada = formatoFecha(fechaEntrega);
                            $('#botonGuardarDatos').show()
                            $('#miModalJQuery .modal-body').html(`
                            <h4>Detalles del primer Compromiso</h4>
                            <p><strong>Fecha Reunion:</strong> ${fechaReunionFormateada}</p>
                            <p><strong>Fecha de Entrega:</strong> ${fechaEntregaFormateada}</p>
                            <p><strong>Responsable:</strong> ${data.user.email}</p>
                            <p><strong>Descripcion Entrega:</strong> ${data.descripcion_entrega}</p>
                            <div class="form-group">
        <label for="descripcion_entrega_input">Descripción de Entregado:</label>
        <input type="text" 
               class="form-control" 
               id="descripcion_entregado_input" >
        <input type="hidden" 
               class="form-control" 
               id="compriso_id" value="${data.id}" >
    </div>
    <p class="text-muted mt-3">
        *la Fecha se tomara la actual cuando le de Guardar
    </p>
                            `);
                            $(this).data('id-compromiso',data.id);
                        }else{
                            $('#botonGuardarDatos').hide()
                            $('#miModalJQuery .modal-body').html(`
                            <h4>Detalles del Compromiso</h4>
                            <p>No tienes ningun compromiso</p>
                            `);
                        }
                     
                        var myModal = new bootstrap.Modal(document.getElementById('miModalJQuery'));
                        myModal.show();
                    },
                
                error: function(xhr, status, error) {
                    alert('No se pudieron cargar los datos de la empresa.');
                }
            });
        });
    });
    </script>
@endpush

