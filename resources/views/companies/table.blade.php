<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="companies-table">
            <thead>
            <tr>
                <th>Razon Social</th>
                <th>Nombre de la Persona de Contacto</th>
                <th>Correo de la Persona de Contacto</th>
                <th>Telefono de la Persona de Contacto</th>
                @if(Auth::user()->is_super  && Auth::user()->is_client)

<th>Cantidad Cliente a ingresar MAX</th>
@endif
                <th>Logo</th>
                <th>Campo Facturacion</th>
          
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($companies as $company)
                <tr>
                    <td>{{ $company->razon_social }}</td>
                    <td>{{ $company->per_cont_name }}</td>
                    <td>{{ $company->per_cont_email }}</td>
                    <td>{{ $company->per_cont_phone }}</td>
                    <td><img src="{{ asset('storage/'.$company->logo) }}" style="width:50px;height:50px;"/></td>
                    <td>{{ $company->campo }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['companies.destroy', $company->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('companies.show', [$company->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('companies.edit', [$company->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-edit"></i>
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
            @include('adminlte-templates::common.paginate', ['records' => $companies])
        </div>
    </div>
</div>
