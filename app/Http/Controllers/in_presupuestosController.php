<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createin_presupuestosRequest;
use App\Http\Requests\Updatein_presupuestosRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\in_presupuestos;
use App\Repositories\in_presupuestosRepository;
use App\Models\excelscompany;
use Illuminate\Http\Request;
use Flash;

class in_presupuestosController extends AppBaseController
{
    /** @var in_presupuestosRepository $inPresupuestosRepository*/
    private $inPresupuestosRepository;

    public function __construct(in_presupuestosRepository $inPresupuestosRepo)
    {
        $this->inPresupuestosRepository = $inPresupuestosRepo;
    }

    /**
     * Display a listing of the in_presupuestos.
     */
    public function index(Request $request)
    {
        $inPresupuestos = in_presupuestos::Select('in_presupuestos.*')
            ->join('excelscompanies','excelscompanies.id','in_presupuestos.id_excel')
          
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
         ->paginate(10);

        return view('in_presupuestos.index')
            ->with('inPresupuesto', $inPresupuestos);
    }

    /**
     * Show the form for creating a new in_presupuestos.
     */
    public function create()
    {
        $excels = excelscompany::Select('excelscompanies.*')
            
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
        ->pluck('version', 'id');
        return view('in_presupuestos.create')->with('excels',$excels);
    }

    /**
     * Store a newly created in_presupuestos in storage.
     */
    public function store(Createin_presupuestosRequest $request)
    {
        $input = $request->all();

        $inPresupuestos = $this->inPresupuestosRepository->create($input);

        Flash::success('In Presupuestos saved successfully.');

        return redirect(route('inPresupuestos.index'));
    }

    /**
     * Display the specified in_presupuestos.
     */
    public function show($id)
    {
        $inPresupuestos = $this->inPresupuestosRepository->find($id);

        if (empty($inPresupuestos)) {
            Flash::error('In Presupuestos not found');

            return redirect(route('inPresupuestos.index'));
        }

        return view('in_presupuestos.show')->with('inPresupuestos', $inPresupuestos);
    }

    /**
     * Show the form for editing the specified in_presupuestos.
     */
    public function edit($id)
    {
        $inPresupuestos = $this->inPresupuestosRepository->find($id);

        if (empty($inPresupuestos)) {
            Flash::error('In Presupuestos not found');

            return redirect(route('inPresupuestos.index'));
        }
        $excels = excelscompany::Select('excelscompanies.*')
            
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
        ->pluck('version', 'id');
        return view('in_presupuestos.edit')->with('inPresupuestos', $inPresupuestos)->with('excels',$excels);
    }

    /**
     * Update the specified in_presupuestos in storage.
     */
    public function update($id, Updatein_presupuestosRequest $request)
    {
        $inPresupuestos = $this->inPresupuestosRepository->find($id);

        if (empty($inPresupuestos)) {
            Flash::error('In Presupuestos not found');

            return redirect(route('inPresupuestos.index'));
        }

        $inPresupuestos = $this->inPresupuestosRepository->update($request->all(), $id);

        Flash::success('In Presupuestos updated successfully.');

        return redirect(route('inPresupuestos.index'));
    }

    /**
     * Remove the specified in_presupuestos from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $inPresupuestos = $this->inPresupuestosRepository->find($id);

        if (empty($inPresupuestos)) {
            Flash::error('In Presupuestos not found');

            return redirect(route('inPresupuestos.index'));
        }

        $this->inPresupuestosRepository->delete($id);

        Flash::success('In Presupuestos deleted successfully.');

        return redirect(route('inPresupuestos.index'));
    }
}
