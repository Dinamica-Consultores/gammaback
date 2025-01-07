<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createclasificacion_cuenta_resulRequest;
use App\Http\Requests\Updateclasificacion_cuenta_resulRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\clasificacion_cuenta_resul;
use App\Repositories\clasificacion_cuenta_resulRepository;
use App\Models\excelscompany;
use Illuminate\Http\Request;
use Flash;

class clasificacion_cuenta_resulController extends AppBaseController
{
    /** @var clasificacion_cuenta_resulRepository $clasificacionCuentaResulRepository*/
    private $clasificacionCuentaResulRepository;

    public function __construct(clasificacion_cuenta_resulRepository $clasificacionCuentaResulRepo)
    {
        $this->clasificacionCuentaResulRepository = $clasificacionCuentaResulRepo;
    }

    /**
     * Display a listing of the clasificacion_cuenta_resul.
     */
    public function index(Request $request)
    {
        $clasificacionCuentaResuls = clasificacion_cuenta_resul::Select('clasificacion_cuenta_resuls.*')
            ->join('excelscompanies','excelscompanies.id','clasificacion_cuenta_resuls.id_excel')
            ->join('companies','companies.id','excelscompanies.id_company')
            ->where('companies.id_estudio',auth()->user()->getIdEstudios())
                ->paginate(10);
        return view('clasificacion_cuenta_resuls.index')
            ->with('clasificacionCuentaResuls', $clasificacionCuentaResuls);
    }

    /**
     * Show the form for creating a new clasificacion_cuenta_resul.
     */
    public function create()
    {
        $excels = excelscompany::Select('excelscompanies.*')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
        ->pluck('version', 'id');
        return view('clasificacion_cuenta_resuls.create')->with('excels',$excels);
    }

    /**
     * Store a newly created clasificacion_cuenta_resul in storage.
     */
    public function store(Createclasificacion_cuenta_resulRequest $request)
    {
        $input = $request->all();

        $clasificacionCuentaResul = $this->clasificacionCuentaResulRepository->create($input);

        Flash::success('Categorizacion resultado Guardado.');

        return redirect(route('clasificacionCuentaResuls.index'));
    }

    /**
     * Display the specified clasificacion_cuenta_resul.
     */
    public function show($id)
    {
        $clasificacionCuentaResul = $this->clasificacionCuentaResulRepository->find($id);

        if (empty($clasificacionCuentaResul)) {
            Flash::error('Categorizacion resultado No encontrado');

            return redirect(route('clasificacionCuentaResuls.index'));
        }

        return view('clasificacion_cuenta_resuls.show')->with('clasificacionCuentaResul', $clasificacionCuentaResul);
    }

    /**
     * Show the form for editing the specified clasificacion_cuenta_resul.
     */
    public function edit($id)
    {
        $clasificacionCuentaResul = $this->clasificacionCuentaResulRepository->find($id);

        if (empty($clasificacionCuentaResul)) {
            Flash::error('Categorizacion resultado No encontrado');

            return redirect(route('clasificacionCuentaResuls.index'));
        }
        $excels = excelscompany::Select('excelscompanies.*')
           
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
        ->pluck('version', 'id');
        return view('clasificacion_cuenta_resuls.edit')->with('clasificacionCuentaResul', $clasificacionCuentaResul)->with('excels',$excels);
    }

    /**
     * Update the specified clasificacion_cuenta_resul in storage.
     */
    public function update($id, Updateclasificacion_cuenta_resulRequest $request)
    {
        $clasificacionCuentaResul = $this->clasificacionCuentaResulRepository->find($id);

        if (empty($clasificacionCuentaResul)) {
            Flash::error('Categorizacion resultado No encontrado');

            return redirect(route('clasificacionCuentaResuls.index'));
        }

        $clasificacionCuentaResul = $this->clasificacionCuentaResulRepository->update($request->all(), $id);

        Flash::success('Categorizacion resultado Actualizado.');

        return redirect(route('clasificacionCuentaResuls.index'));
    }

    /**
     * Remove the specified clasificacion_cuenta_resul from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $clasificacionCuentaResul = $this->clasificacionCuentaResulRepository->find($id);

        if (empty($clasificacionCuentaResul)) {
            Flash::error('Categorizacion resultado No encontrado');

            return redirect(route('clasificacionCuentaResuls.index'));
        }

        $this->clasificacionCuentaResulRepository->delete($id);

        Flash::success('Categorizacion resultado Borrado.');

        return redirect(route('clasificacionCuentaResuls.index'));
    }
}
