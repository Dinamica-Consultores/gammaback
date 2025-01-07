<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use App\Imports\ImportPlantilla;
class ExcelIn implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
          'SUCURSALES'=>new ImportPlantilla(),
          'CLASIFICACION CUENTAS RESULTADO'=>new ImportPlantilla(),
          'IN RESULTADOS'=>new ImportPlantilla(),
          'IN PRESUPUESTOS'=>new ImportPlantilla(),
          'TIPO DE CAMBIO'=>new ImportPlantilla(),
          'SETUP ANALISIS'=>new ImportPlantilla(),
          'IN VENTAS'=>new ImportPlantilla(),
          'CATEGORIZACION CTAS BALANCE'=>new ImportPlantilla(),
          'IN BALANCE'=>new ImportPlantilla(),
        ];
    }
    public function onUnknownSheet($sheetName)
{
    // E.g. you can log that a sheet was not found.
    info("Sheet {$sheetName} was skipped");
}
}