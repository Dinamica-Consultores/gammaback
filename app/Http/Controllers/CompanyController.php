<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatecompanyRequest;
use App\Http\Requests\UpdatecompanyRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\companyRepository;
use Illuminate\Http\Request;
use App\Models\company;
use App\Models\grupo_economicos_empresas;
use App\Models\estudios;
use Flash;
use Illuminate\Support\Facades\Storage;

class companyController extends AppBaseController
{
    /** @var companyRepository $companyRepository*/
    private $companyRepository;

    public function __construct(companyRepository $companyRepo)
    {
        $this->companyRepository = $companyRepo;
        
    }

    /**
     * Display a listing of the company.
     */
    public function index(Request $request)
    {
        $companies = company::SELECT('companies.*')->where('id_estudio', auth()->user()->getIdEstudios());
        if(isset($_GET['query'])){
            $companies=$companies->whereRaw('CONCAT(per_cont_name) Like ? OR razon_social Like ?',array('%'.$_GET['query'].'%','%'.$_GET['query'].'%'));
           
        }
        $companies=$companies->paginate(10);
        $companies->appends($request->all());
        return view('companies.index')
            ->with('companies', $companies);
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(CreatecompanyRequest $request)
    {   
        $estudios=estudios::find(auth()->user()->getIdEstudios());
        $companys=company::where('id_estudio',auth()->user()->getIdEstudios())->get()->count();
        if(isset($estudios)){
            if(isset($estudios->cantida_empresa_max )){
                if($estudios->cantida_empresa_max <= $companys){
                    Flash::success('As obtenido la cantidad Maxima de Empresas permitidas.');

                    return redirect(route('companies.index'));
                }
            }
        }else{
            Flash::success('No tienes estudio.');

            return redirect(route('companies.index'));
        }
        $input = $request->all();
        if($request->hasFile('logo')){
            $path = $request->file('logo')->store('public/logo');
            $path2 = str_replace('public/', '', $path);
            $input['logo']=$path2;
        }else{
            
        $input['logo']='';
        }
        $input['id_estudio']= auth()->user()->getIdEstudios();
        if(!isset($input['campo'])){
            $input['campo']='';
        }
        if(isset($input['isestadosp'])){
            $input['isestadosp']=true;
        }else{
            
            $input['isestadosp']=false;
        }
        if(isset($input['ispresupuesto'])){
            $input['ispresupuesto']=true;
        }else{
            
            $input['ispresupuesto']=false;
        }
        $company = $this->companyRepository->create($input);

        Flash::success('Compañia Guardado.');

        return redirect(route('companies.index'));
    }

    /**
     * Display the specified company.
     */
    public function show($id)
    {
        $company = $this->companyRepository->find($id);
        
        if (empty($company)) {
            Flash::error('Compañia No encontrado');

            return redirect(route('companies.index'));
        }

        return view('companies.show')->with('company', $company);
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit($id)
    {
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            Flash::error('Compañia No encontrado');

            return redirect(route('companies.index'));
        }

        return view('companies.edit')->with('company', $company);
    }

    /**
     * Update the specified company in storage.
     */
    public function update($id, UpdatecompanyRequest $request)
    {
        $input=$request->all();
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            Flash::error('Compañia No encontrado');

            return redirect(route('companies.index'));
        }

        if($request->hasFile('logo')){
            $path = $request->file('logo')->store('public/logo');
            $path2 = str_replace('public/', '', $path);
            $input['logo']=$path2;
            Storage::delete('public/'.$company->logo);
        }else{
            $input['logo']=$company->logo;
        }
        if(isset($input['isestadosp'])){
            $input['isestadosp']=true;
        }else{
            
            $input['isestadosp']=false;
        }
        if(isset($input['ispresupuesto'])){
            $input['ispresupuesto']=true;
        }else{
            
            $input['ispresupuesto']=false;
        }
        $company = $this->companyRepository->update($input, $id);

        Flash::success('Compañia Actualizado.');

        return redirect(route('companies.index'));
    }

    /**
     * Remove the specified company from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            Flash::error('Compañia No encontrado');

            return redirect(route('companies.index'));
        }

        $this->companyRepository->delete($id);

        grupo_economicos_empresas::where('id_company',$id)->delete();
        Flash::success('Compañia Borrada.');

        return redirect(route('companies.index'));
    }
}
