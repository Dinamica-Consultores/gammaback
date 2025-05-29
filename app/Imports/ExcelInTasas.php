<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use App\Imports\ImportPlantilla;
class ExcelInTasas implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
          'TASAS'=>new ImportPlantilla()
        ];
    }
    public function onUnknownSheet($sheetName)
{
    // E.g. you can log that a sheet was not found.
    info("Sheet {$sheetName} was skipped");
}
}