<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createcategorizacion_cts_balanceRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\categorizacion_cts_balance;
use App\Models\excelscompany;
use App\Repositories\categorizacion_cts_balanceRepository;
use Illuminate\Http\Request;
use Flash;

class categorizacion_cts_balanceController extends AppBaseController
{
    /** @var categorizacion_cts_balanceRepository $categorizacionCtsBalanceRepository*/
    private $categorizacionCtsBalanceRepository;

    public function __construct(categorizacion_cts_balanceRepository $categorizacionCtsBalanceRepo)
    {
        $this->categorizacionCtsBalanceRepository = $categorizacionCtsBalanceRepo;
    }

    /**
     * Display a listing of the categorizacion_cts_balance.
     */
    public function index(Request $request)
    {
        $categorizacionCtsBalances = categorizacion_cts_balance::Select('categorizacion_cts_balances.*')
            ->join('excelscompanies','excelscompanies.id','categorizacion_cts_balances.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
            ->paginate(10);

        return view('categorizacion_cts_balances.index')
            ->with('categorizacionCtsBalances', $categorizacionCtsBalances);
    }

    /**
     * Show the form for creating a new categorizacion_cts_balance.
     */
    public function create()
    {
        $excels = excelscompany::Select('excelscompanies.*')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
            ->paginate(10);
        return view('categorizacion_cts_balances.create')->with('excels',$excels);
    }

    /**
     * Store a newly created categorizacion_cts_balance in storage.
     */
    public function store(Createcategorizacion_cts_balanceRequest $request)
    {
        $input = $request->all();

        $categorizacionCtsBalance = $this->categorizacionCtsBalanceRepository->create($input);

        Flash::success('Categorizacion Balance Guardado Correctamente.');

        return redirect(route('categorizacionCtsBalances.index'));
    }

    /**
     * Display the specified categorizacion_cts_balance.
     */
    public function show($id)
    {
        $categorizacionCtsBalance = $this->categorizacionCtsBalanceRepository->find($id);

        if (empty($categorizacionCtsBalance)) {
            Flash::error('Categorizacion Balance No encontrado');

            return redirect(route('categorizacionCtsBalances.index'));
        }

        return view('categorizacion_cts_balances.show')->with('categorizacionCtsBalance', $categorizacionCtsBalance);
    }

    /**
     * Show the form for editing the specified categorizacion_cts_balance.
     */
    public function edit($id)
    {
        $categorizacionCtsBalance = $this->categorizacionCtsBalanceRepository->find($id);

        if (empty($categorizacionCtsBalance)) {
            Flash::error('Categorizacion Balance No encontrado');

            return redirect(route('categorizacionCtsBalances.index'));
        }
        $excels = excelscompany::Select('excelscompanies.*')
            ->join('companies','companies.id','excelscompanies.id_company')
            ->where('companies.id_estudio',auth()->user()->getIdEstudios())
                ->paginate(10);
        return view('categorizacion_cts_balances.edit')->with('categorizacionCtsBalance', $categorizacionCtsBalance)->with('excels',$excels);
    }

    /**
     * Update the specified categorizacion_cts_balance in storage.
     */
    public function update($id, Updatecategorizacion_cts_balanceRequest $request)
    {
        $categorizacionCtsBalance = $this->categorizacionCtsBalanceRepository->find($id);

        if (empty($categorizacionCtsBalance)) {
            Flash::error('Categorizacion Balance No encontrado');

            return redirect(route('categorizacionCtsBalances.index'));
        }

        $categorizacionCtsBalance = $this->categorizacionCtsBalanceRepository->update($request->all(), $id);

        Flash::success('Categorizacion Balance Actualizado.');

        return redirect(route('categorizacionCtsBalances.index'));
    }

    /**
     * Remove the specified categorizacion_cts_balance from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $categorizacionCtsBalance = $this->categorizacionCtsBalanceRepository->find($id);

        if (empty($categorizacionCtsBalance)) {
            Flash::error('Categorizacion Balance No encontrado');

            return redirect(route('categorizacionCtsBalances.index'));
        }

        $this->categorizacionCtsBalanceRepository->delete($id);

        Flash::success('Categorizacion Balance Eliminado.');

        return redirect(route('categorizacionCtsBalances.index'));
    }
}
