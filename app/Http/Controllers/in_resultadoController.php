<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createin_resultadoRequest;
use App\Http\Requests\Updatein_resultadoRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\in_resultado;
use App\Repositories\in_resultadoRepository;
use App\Models\excelscompany;
use Illuminate\Http\Request;
use Flash;

class in_resultadoController extends AppBaseController
{
    /** @var in_resultadoRepository $inResultadoRepository*/
    private $inResultadoRepository;

    public function __construct(in_resultadoRepository $inResultadoRepo)
    {
        $this->inResultadoRepository = $inResultadoRepo;
    }

    /**
     * Display a listing of the in_resultado.
     */
    public function index(Request $request)
    {
        $inResultados = in_resultado::Select('in_resultados.*')
                ->join('excelscompanies','excelscompanies.id','in_resultados.id_excel')
            
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
          ->paginate(10);
        return view('in_resultados.index')
            ->with('inResultados', $inResultados);
    }

    /**
     * Show the form for creating a new in_resultado.
     */
    public function create()
    {
        $excels = excelscompany::Select('excelscompanies.*')
         
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
         ->pluck('version', 'id');
        return view('in_resultados.create')->with('excels',$excels);
    }

    /**
     * Store a newly created in_resultado in storage.
     */
    public function store(Createin_resultadoRequest $request)
    {
        $input = $request->all();

        $inResultado = $this->inResultadoRepository->create($input);

        Flash::success('In Resultado saved successfully.');

        return redirect(route('inResultados.index'));
    }

    /**
     * Display the specified in_resultado.
     */
    public function show($id)
    {
        $inResultado = $this->inResultadoRepository->find($id);

        if (empty($inResultado)) {
            Flash::error('In Resultado not found');

            return redirect(route('inResultados.index'));
        }

        return view('in_resultados.show')->with('inResultado', $inResultado);
    }

    /**
     * Show the form for editing the specified in_resultado.
     */
    public function edit($id)
    {
        $inResultado = $this->inResultadoRepository->find($id);

        if (empty($inResultado)) {
            Flash::error('In Resultado not found');

            return redirect(route('inResultados.index'));
        }
        $excels = excelscompany::Select('excelscompanies.*')
         
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
         ->pluck('version', 'id');
        return view('in_resultados.edit')->with('inResultado', $inResultado)->with('excels',$excels);
    }

    /**
     * Update the specified in_resultado in storage.
     */
    public function update($id, Updatein_resultadoRequest $request)
    {
        $inResultado = $this->inResultadoRepository->find($id);

        if (empty($inResultado)) {
            Flash::error('In Resultado not found');

            return redirect(route('inResultados.index'));
        }

        $inResultado = $this->inResultadoRepository->update($request->all(), $id);

        Flash::success('In Resultado updated successfully.');

        return redirect(route('inResultados.index'));
    }

    /**
     * Remove the specified in_resultado from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $inResultado = $this->inResultadoRepository->find($id);

        if (empty($inResultado)) {
            Flash::error('In Resultado not found');

            return redirect(route('inResultados.index'));
        }

        $this->inResultadoRepository->delete($id);

        Flash::success('In Resultado deleted successfully.');

        return redirect(route('inResultados.index'));
    }
}
