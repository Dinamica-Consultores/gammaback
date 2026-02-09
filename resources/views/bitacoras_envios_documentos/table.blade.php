<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="bitacoras_envios_documentos-table">
            <thead>
            <tr>
                <th>Compañia</th>
                <th>Tipo Documento</th>
                <th>Fecha del Envio del Correo</th>
                <th>Correos</th>
                <th>Texto</th>
            </tr>
            </thead>
            <tbody>
            @php
    use Carbon\Carbon;
@endphp
            @foreach($bitacorasEnviosDocumentos as $bitacorasEnviosDocumento)
                <tr>
                  
                <td>{{ $bitacorasEnviosDocumento->documento_companias?->company?->razon_social }}</td>
                <td>{{ $bitacorasEnviosDocumento->documento_companias?->tipo_documento?->nombre }}</td>
                    <td>{{Carbon::parse( $bitacorasEnviosDocumento->fecha_envion)->format('d/m/Y')  }}</td>
                    <td>{{ $bitacorasEnviosDocumento->correos }}</td>
                    <td>{{ $bitacorasEnviosDocumento->texto_data }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $bitacorasEnviosDocumentos])
        </div>
    </div>
</div>
