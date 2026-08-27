<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEspacioFiscalRequest;
use App\Http\Requests\UpdateEspacioFiscalRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\EspacioFiscalRepository;
use Illuminate\Http\Request;
use Flash;

class EspacioFiscalController extends AppBaseController
{
    /** @var EspacioFiscalRepository $espacioFiscalRepository*/
    private $espacioFiscalRepository;

    public function __construct(EspacioFiscalRepository $espacioFiscalRepo)
    {
        $this->espacioFiscalRepository = $espacioFiscalRepo;
    }

    /**
     * Display a listing of the EspacioFiscal.
     */
    public function index(Request $request)
    {
        $espacioFiscals = $this->espacioFiscalRepository->paginate(10);

        return view('espacio_fiscals.index')
            ->with('espacioFiscals', $espacioFiscals);
    }

    /**
     * Show the form for creating a new EspacioFiscal.
     */
    public function create()
    {
        return view('espacio_fiscals.create');
    }

    /**
     * Store a newly created EspacioFiscal in storage.
     */
    public function store(CreateEspacioFiscalRequest $request)
    {
        $input = $request->all();

        $espacioFiscal = $this->espacioFiscalRepository->create($input);

        Flash::success('Espacio Fiscal saved successfully.');

        return redirect(route('espacioFiscals.index'));
    }

    /**
     * Display the specified EspacioFiscal.
     */
    public function show($id)
    {
        $espacioFiscal = $this->espacioFiscalRepository->find($id);

        if (empty($espacioFiscal)) {
            Flash::error('Espacio Fiscal not found');

            return redirect(route('espacioFiscals.index'));
        }

        return view('espacio_fiscals.show')->with('espacioFiscal', $espacioFiscal);
    }

    /**
     * Show the form for editing the specified EspacioFiscal.
     */
    public function edit($id)
    {
        $espacioFiscal = $this->espacioFiscalRepository->find($id);

        if (empty($espacioFiscal)) {
            Flash::error('Espacio Fiscal not found');

            return redirect(route('espacioFiscals.index'));
        }

        return view('espacio_fiscals.edit')->with('espacioFiscal', $espacioFiscal);
    }

    /**
     * Update the specified EspacioFiscal in storage.
     */
    public function update($id, UpdateEspacioFiscalRequest $request)
    {
        $espacioFiscal = $this->espacioFiscalRepository->find($id);

        if (empty($espacioFiscal)) {
            Flash::error('Espacio Fiscal not found');

            return redirect(route('espacioFiscals.index'));
        }

        $espacioFiscal = $this->espacioFiscalRepository->update($request->all(), $id);

        Flash::success('Espacio Fiscal updated successfully.');

        return redirect(route('espacioFiscals.index'));
    }

    /**
     * Remove the specified EspacioFiscal from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $espacioFiscal = $this->espacioFiscalRepository->find($id);

        if (empty($espacioFiscal)) {
            Flash::error('Espacio Fiscal not found');

            return redirect(route('espacioFiscals.index'));
        }

        $this->espacioFiscalRepository->delete($id);

        Flash::success('Espacio Fiscal deleted successfully.');

        return redirect(route('espacioFiscals.index'));
    }
}
