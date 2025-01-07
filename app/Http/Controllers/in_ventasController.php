<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createin_ventasRequest;
use App\Http\Requests\Updatein_ventasRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\excelscompany;
use App\Repositories\in_ventasRepository;
use Illuminate\Http\Request;
use App\Models\in_ventas;
use Flash;

class in_ventasController extends AppBaseController
{
    /** @var in_ventasRepository $inVentasRepository*/
    private $inVentasRepository;

    public function __construct(in_ventasRepository $inVentasRepo)
    {
        $this->inVentasRepository = $inVentasRepo;
    }

    /**
     * Display a listing of the in_ventas.
     */
    public function index(Request $request)
    {
        $inVentas = in_ventas::Select('in_ventas.*')
        ->join('excelscompanies','excelscompanies.id','in_ventas.id_excel')
        
->join('companies','companies.id','excelscompanies.id_company')
->where('companies.id_estudio',auth()->user()->getIdEstudios())
->paginate(10);
        return view('in_ventas.index')
            ->with('inVenta', $inVentas);
    }

    /**
     * Show the form for creating a new in_ventas.
     */
    public function create()
    {
        $excels = excelscompany::Select('excelscompanies.*')
           
->join('companies','companies.id','excelscompanies.id_company')
->where('companies.id_estudio',auth()->user()->getIdEstudios())
 ->pluck('version', 'id');
        return view('in_ventas.create')->with('excels',$excels);
    }

    /**
     * Store a newly created in_ventas in storage.
     */
    public function store(Createin_ventasRequest $request)
    {
        $input = $request->all();

        $inVentas = $this->inVentasRepository->create($input);

        Flash::success('In Ventas saved successfully.');

        return redirect(route('inVentas.index'));
    }

    /**
     * Display the specified in_ventas.
     */
    public function show($id)
    {
        $inVentas = $this->inVentasRepository->find($id);

        if (empty($inVentas)) {
            Flash::error('In Ventas not found');

            return redirect(route('inVentas.index'));
        }

        return view('in_ventas.show')->with('inVentas', $inVentas);
    }

    /**
     * Show the form for editing the specified in_ventas.
     */
    public function edit($id)
    {
        $inVentas = $this->inVentasRepository->find($id);

        if (empty($inVentas)) {
            Flash::error('In Ventas not found');

            return redirect(route('inVentas.index'));
        }
        $excels = excelscompany::Select('excelscompanies.*')
           
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
         ->pluck('version', 'id');
        return view('in_ventas.edit')->with('inVentas', $inVentas)->with('excels',$excels);
    }

    /**
     * Update the specified in_ventas in storage.
     */
    public function update($id, Updatein_ventasRequest $request)
    {
        $inVentas = $this->inVentasRepository->find($id);

        if (empty($inVentas)) {
            Flash::error('In Ventas not found');

            return redirect(route('inVentas.index'));
        }

        $inVentas = $this->inVentasRepository->update($request->all(), $id);

        Flash::success('In Ventas updated successfully.');

        return redirect(route('inVentas.index'));
    }

    /**
     * Remove the specified in_ventas from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $inVentas = $this->inVentasRepository->find($id);

        if (empty($inVentas)) {
            Flash::error('In Ventas not found');

            return redirect(route('inVentas.index'));
        }

        $this->inVentasRepository->delete($id);

        Flash::success('In Ventas deleted successfully.');

        return redirect(route('inVentas.index'));
    }
}
