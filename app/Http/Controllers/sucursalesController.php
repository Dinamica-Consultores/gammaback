<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatesucursalesRequest;
use App\Http\Requests\UpdatesucursalesRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\sucursalesRepository;
use App\Models\excelscompany;
use Illuminate\Http\Request;
use App\Models\sucursales;
use Flash;

class sucursalesController extends AppBaseController
{
    /** @var sucursalesRepository $sucursalesRepository*/
    private $sucursalesRepository;

    public function __construct(sucursalesRepository $sucursalesRepo)
    {
        $this->sucursalesRepository = $sucursalesRepo;
    }

    /**
     * Display a listing of the sucursales.
     */
    public function index(Request $request)
    {
        $sucursales = sucursales::Select('sucursales.*')
        ->join('excelscompanies','excelscompanies.id','sucursales.id_excel')
      
    ->join('companies','companies.id','excelscompanies.id_company')
    ->where('companies.id_estudio',auth()->user()->getIdEstudios())
     ->paginate(10);
        return view('sucursales.index')
            ->with('sucursale', $sucursales);
    }

    /**
     * Show the form for creating a new sucursales.
     */
    public function create()
    {
        $excels = excelscompany::Select('excelscompanies.*')
            
    ->join('companies','companies.id','excelscompanies.id_company')
    ->where('companies.id_estudio',auth()->user()->getIdEstudios())
    ->pluck('version', 'id');
        return view('sucursales.create')->with('excels',$excels);
    }

    /**
     * Store a newly created sucursales in storage.
     */
    public function store(CreatesucursalesRequest $request)
    {
        $input = $request->all();

        $sucursales = $this->sucursalesRepository->create($input);

        Flash::success('Sucursales saved successfully.');

        return redirect(route('sucursales.index'));
    }

    /**
     * Display the specified sucursales.
     */
    public function show($id)
    {
        $sucursales = $this->sucursalesRepository->find($id);

        if (empty($sucursales)) {
            Flash::error('Sucursales not found');

            return redirect(route('sucursales.index'));
        }

        return view('sucursales.show')->with('sucursales', $sucursales);
    }

    /**
     * Show the form for editing the specified sucursales.
     */
    public function edit($id)
    {
        $sucursales = $this->sucursalesRepository->find($id);

        if (empty($sucursales)) {
            Flash::error('Sucursales not found');

            return redirect(route('sucursales.index'));
        }
        $excels = excelscompany::Select('excelscompanies.*')
            
    ->join('companies','companies.id','excelscompanies.id_company')
    ->where('companies.id_estudio',auth()->user()->getIdEstudios())
    ->pluck('version', 'id');
        return view('sucursales.edit')->with('sucursales', $sucursales)->with('excels',$excels);
    }

    /**
     * Update the specified sucursales in storage.
     */
    public function update($id, UpdatesucursalesRequest $request)
    {
        $sucursales = $this->sucursalesRepository->find($id);

        if (empty($sucursales)) {
            Flash::error('Sucursales not found');

            return redirect(route('sucursales.index'));
        }

        $sucursales = $this->sucursalesRepository->update($request->all(), $id);

        Flash::success('Sucursales updated successfully.');

        return redirect(route('sucursales.index'));
    }

    /**
     * Remove the specified sucursales from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $sucursales = $this->sucursalesRepository->find($id);

        if (empty($sucursales)) {
            Flash::error('Sucursales not found');

            return redirect(route('sucursales.index'));
        }

        $this->sucursalesRepository->delete($id);

        Flash::success('Sucursales deleted successfully.');

        return redirect(route('sucursales.index'));
    }
}
