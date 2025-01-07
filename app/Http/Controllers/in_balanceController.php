<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createin_balanceRequest;
use App\Http\Requests\Updatein_balanceRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\in_balanceRepository;
use App\Models\excelscompany;
use App\Models\in_balance;
use Illuminate\Http\Request;
use Flash;

class in_balanceController extends AppBaseController
{
    /** @var in_balanceRepository $inBalanceRepository*/
    private $inBalanceRepository;

    public function __construct(in_balanceRepository $inBalanceRepo)
    {
        $this->inBalanceRepository = $inBalanceRepo;
    }

    /**
     * Display a listing of the in_balance.
     */
    public function index(Request $request)
    {
        $inBalances = in_balance::Select('in_balances.*')
        ->join('excelscompanies','excelscompanies.id','in_balances.id_excel')
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
        ->paginate(10);
        return view('in_balances.index')
            ->with('inBalances', $inBalances);
    }

    /**
     * Show the form for creating a new in_balance.
     */
    public function create()
    {
        $excels = excelscompany::Select('excelscompanies.*')
          
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
          ->pluck('version', 'id');
        return view('in_balances.create')->with('excels',$excels);
    }

    /**
     * Store a newly created in_balance in storage.
     */
    public function store(Createin_balanceRequest $request)
    {
        $input = $request->all();

        $inBalance = $this->inBalanceRepository->create($input);

        Flash::success('In Balance saved successfully.');

        return redirect(route('inBalances.index'));
    }

    /**
     * Display the specified in_balance.
     */
    public function show($id)
    {
        $inBalance = $this->inBalanceRepository->find($id);

        if (empty($inBalance)) {
            Flash::error('In Balance not found');

            return redirect(route('inBalances.index'));
        }

        return view('in_balances.show')->with('inBalance', $inBalance);
    }

    /**
     * Show the form for editing the specified in_balance.
     */
    public function edit($id)
    {
        $inBalance = $this->inBalanceRepository->find($id);

        if (empty($inBalance)) {
            Flash::error('In Balance not found');

            return redirect(route('inBalances.index'));
        }
        $excels = excelscompany::Select('excelscompanies.*')
          
        ->join('companies','companies.id','excelscompanies.id_company')
        ->where('companies.id_estudio',auth()->user()->getIdEstudios())
          ->pluck('version', 'id');
        return view('in_balances.edit')->with('inBalance', $inBalance)->with('excels',$excels);
    }

    /**
     * Update the specified in_balance in storage.
     */
    public function update($id, Updatein_balanceRequest $request)
    {
        $inBalance = $this->inBalanceRepository->find($id);

        if (empty($inBalance)) {
            Flash::error('In Balance not found');

            return redirect(route('inBalances.index'));
        }

        $inBalance = $this->inBalanceRepository->update($request->all(), $id);

        Flash::success('In Balance updated successfully.');

        return redirect(route('inBalances.index'));
    }

    /**
     * Remove the specified in_balance from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $inBalance = $this->inBalanceRepository->find($id);

        if (empty($inBalance)) {
            Flash::error('In Balance not found');

            return redirect(route('inBalances.index'));
        }

        $this->inBalanceRepository->delete($id);

        Flash::success('In Balance deleted successfully.');

        return redirect(route('inBalances.index'));
    }
}
