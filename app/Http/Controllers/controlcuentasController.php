<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatecontrolcuentasRequest;
use App\Http\Requests\UpdatecontrolcuentasRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\controlcuentasRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Flash;
use App\Models\controlcuentas;

class controlcuentasController extends AppBaseController
{
    /** @var controlcuentasRepository $controlcuentasRepository*/
    private $controlcuentasRepository;

    public function __construct(controlcuentasRepository $controlcuentasRepo)
    {
        $this->controlcuentasRepository = $controlcuentasRepo;
    }

    /**
     * Display a listing of the controlcuentas.
     */
    public function index(Request $request)
    {
        $controlcuentas = controlcuentas::Select(DB::raw('COUNT(controlcuentas.id) as cantidad,companies.razon_social as name,companies.id'))
        ->join('excelscompanies','excelscompanies.id','controlcuentas.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
        ->groupByRaw('companies.razon_social,companies.id')
        ->paginate(10);
        return view('controlcuentas.index')
            ->with('controlcuentas', $controlcuentas);
    }

    /**
     * Show the form for creating a new controlcuentas.
     */
    public function create()
    {
        return view('controlcuentas.create');
    }

    /**
     * Store a newly created controlcuentas in storage.
     */
    public function store(CreatecontrolcuentasRequest $request)
    {
        $input = $request->all();

        $controlcuentas = $this->controlcuentasRepository->create($input);

        Flash::success('Controlcuentas saved successfully.');

        return redirect(route('controlcuentas.index'));
    }

    /**
     * Display the specified controlcuentas.
     */
    public function show($id)
    {
        $controlcuentas = controlcuentas::Select(DB::raw('controlcuentas.cuenta,controlcuentas.tipo'))
        ->join('excelscompanies','excelscompanies.id','controlcuentas.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id',$id)
        ->paginate(10);

        return view('controlcuentas.tables.index')->with('controlcuentas', $controlcuentas);
    }

    /**
     * Show the form for editing the specified controlcuentas.
     */
    public function edit($id)
    {
        $controlcuentas = $this->controlcuentasRepository->find($id);

        if (empty($controlcuentas)) {
            Flash::error('Controlcuentas not found');

            return redirect(route('controlcuentas.index'));
        }

        return view('controlcuentas.edit')->with('controlcuentas', $controlcuentas);
    }

    /**
     * Update the specified controlcuentas in storage.
     */
    public function update($id, UpdatecontrolcuentasRequest $request)
    {
        $controlcuentas = $this->controlcuentasRepository->find($id);

        if (empty($controlcuentas)) {
            Flash::error('Controlcuentas not found');

            return redirect(route('controlcuentas.index'));
        }

        $controlcuentas = $this->controlcuentasRepository->update($request->all(), $id);

        Flash::success('Controlcuentas updated successfully.');

        return redirect(route('controlcuentas.index'));
    }

    /**
     * Remove the specified controlcuentas from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $controlcuentas = $this->controlcuentasRepository->find($id);

        if (empty($controlcuentas)) {
            Flash::error('Controlcuentas not found');

            return redirect(route('controlcuentas.index'));
        }

        $this->controlcuentasRepository->delete($id);

        Flash::success('Controlcuentas deleted successfully.');

        return redirect(route('controlcuentas.index'));
    }
}
