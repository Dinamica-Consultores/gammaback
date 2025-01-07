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
