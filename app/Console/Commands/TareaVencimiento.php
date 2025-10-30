<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\documento_compania;
use Carbon\Carbon;
use Mail;
class TareaVencimiento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:vne';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar Vencimientos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       $fechaActual = Carbon::now();
       $documentosCompaniasVencimientos=documento_compania::SELECT('documento_companias.*')->with(['company', 'tipo_documento'])->join('tipo_documentos','tipo_documentos.id','documento_companias.id_tipodocumento')->whereRaw('DATE_SUB(documento_companias.fecha_de_vencimiento, INTERVAL tipo_documentos.cantidad_dias_preaviso DAY) <= ? AND documento_companias.notificacion_enviada_venicimiento= ? ', [$fechaActual,FALSE])->get();
       foreach ($documentosCompaniasVencimientos as $documento) {
        $datosCompniaNombre = $documento->company->razon_social ?? 'N/A';
        $datosCompniaCorreos = $documento->company->destinatario_documentos ?? null;
        $datosTipoDocumento = $documento->tipo_documento->nombre ?? 'N/A';
        $fecha_vencimiento = $documento->fecha_de_vencimiento;
        $nombre_documento = $documento->nombre;
        
        $dato = [
            'datosCompniaNombre' => $datosCompniaNombre,
            'datosTipoDocumento' => $datosTipoDocumento,
            'fecha_vencimiento' => $fecha_vencimiento,
            'nombre_documento' => $nombre_documento,
        ];

        if (empty($datosCompniaCorreos)) {
            $this->error("Documento ID {$documento->id}: No hay correos definidos para la compañía.");
            continue;
        }
        
        Mail::send(['html' => 'documento_companias.vencimiento_mail'], ['dato' => $dato], function($message) use ($datosCompniaCorreos) {
            $message->to($datosCompniaCorreos, '')->subject('ALERTA DE VENCIMIENTO DE DOCUMENTO - GAMMA');
            $message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
        });

        BitacorasEnviosDocumento::create([
            'id_documento_companias' => $documento->id, 
            'fecha_envio' => Carbon::now(), 
            'correos' => $datosCompniaCorreos, 
            'texto_data' => 'Aviso de Vencimiento para los correos: ' . $datosCompniaCorreos . ' con fecha vencimiento ' . $fecha_vencimiento, 
        ]);
        $documento->update(['notificacion_enviada_venicimiento' => TRUE]);

      }
      return Command::SUCCESS;
    }
}
