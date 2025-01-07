<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createsetup_analisisRequest;
use App\Http\Requests\Updatesetup_analisisRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\excelscompany;
use App\Models\setup_analisis;
use App\Repositories\setup_analisisRepository;
use Illuminate\Http\Request;
use Flash;

class setup_analisisController extends AppBaseController
{
    /** @var setup_analisisRepository $setupAnalisisRepository*/
    private $setupAnalisisRepository;

    public function __construct(setup_analisisRepository $setupAnalisisRepo)
    {
        $this->setupAnalisisRepository = $setupAnalisisRepo;
    }

    /**
     * Display a listing of the setup_analisis.
     */
    public function index(Request $request)
    {
        $setupAnalises = setup_analisis::Select('setup_analises.*')
        ->join('excelscompanies','excelscompanies.id','setup_analises.id_excel')
       
    ->join('companies','companies.id','excelscompanies.id_company')
    ->where('companies.id_estudio',auth()->user()->getIdEstudios())
     ->paginate(10);

        return view('setup_analises.index')
            ->with('setupAnalises', $setupAnalises);
    }

    /**
     * Show the form for creating a new setup_analisis.
     */
    public function create()
    {
        $excels = excelscompany::Select('excelscompanies.*')
           
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
        ->pluck('version', 'id');
        return view('setup_analises.create')->with('excels',$excels);
    }

    /**
     * Store a newly created setup_analisis in storage.
     */
    public function store(Createsetup_analisisRequest $request)
    {
        $input = $request->all();

        $setupAnalisis = $this->setupAnalisisRepository->create($input);

        Flash::success('Setup Analisis saved successfully.');

        return redirect(route('setupAnalises.index'));
    }

    /**
     * Display the specified setup_analisis.
     */
    public function show($id)
    {
        $setupAnalisis = $this->setupAnalisisRepository->find($id);

        if (empty($setupAnalisis)) {
            Flash::error('Setup Analisis not found');

            return redirect(route('setupAnalises.index'));
        }

        return view('setup_analises.show')->with('setupAnalisis', $setupAnalisis);
    }

    /**
     * Show the form for editing the specified setup_analisis.
     */
    public function edit($id)
    {
        $setupAnalisis = $this->setupAnalisisRepository->find($id);

        if (empty($setupAnalisis)) {
            Flash::error('Setup Analisis not found');

            return redirect(route('setupAnalises.index'));
        }
        $excels = excelscompany::Select('excelscompanies.*')
           
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
        ->pluck('version', 'id');
        return view('setup_analises.edit')->with('setupAnalisis', $setupAnalisis)->with('excels',$excels);
    }

    /**
     * Update the specified setup_analisis in storage.
     */
    public function update($id, Updatesetup_analisisRequest $request)
    {
        $setupAnalisis = $this->setupAnalisisRepository->find($id);

        if (empty($setupAnalisis)) {
            Flash::error('Setup Analisis not found');

            return redirect(route('setupAnalises.index'));
        }

        $setupAnalisis = $this->setupAnalisisRepository->update($request->all(), $id);

        Flash::success('Setup Analisis updated successfully.');

        return redirect(route('setupAnalises.index'));
    }

    /**
     * Remove the specified setup_analisis from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $setupAnalisis = $this->setupAnalisisRepository->find($id);

        if (empty($setupAnalisis)) {
            Flash::error('Setup Analisis not found');

            return redirect(route('setupAnalises.index'));
        }

        $this->setupAnalisisRepository->delete($id);

        Flash::success('Setup Analisis deleted successfully.');

        return redirect(route('setupAnalises.index'));
    }
}
