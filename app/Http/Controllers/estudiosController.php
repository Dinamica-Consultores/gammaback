<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateestudiosRequest;
use App\Http\Requests\UpdateestudiosRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\estudiosRepository;
use Illuminate\Http\Request;
use Flash;

class estudiosController extends AppBaseController
{
    /** @var estudiosRepository $estudiosRepository*/
    private $estudiosRepository;

    public function __construct(estudiosRepository $estudiosRepo)
    {
        $this->estudiosRepository = $estudiosRepo;
    }

    /**
     * Display a listing of the estudios.
     */
    public function index(Request $request)
    {
        $estudios = $this->estudiosRepository->paginate(10);

        return view('estudios.index')
            ->with('estudios', $estudios);
    }

    /**
     * Show the form for creating a new estudios.
     */
    public function create()
    {
        
        return view('estudios.create');
    }

    /**
     * Store a newly created estudios in storage.
     */
    public function store(CreateestudiosRequest $request)
    {
        $input = $request->all();
        if($request->hasFile('logo')){
            $path = $request->file('logo')->store('public/logo');
            $path2 = str_replace('public/', '', $path);
            $input['logo']=$path2;
        }else{
        $input['logo']='';
        }
        if(isset($input['es_empresa'])){
            $input['es_empresa']=true;
        }else{
            
            $input['es_empresa']=false;
        }
        if(isset($input['campo'])){
            $input['campo']= $input['campo'];
        }else{
            
            $input['campo']='';
        }
        $estudios = $this->estudiosRepository->create($input);

        Flash::success('Estudios saved successfully.');

        return redirect(route('estudios.index'));
    }

    /**
     * Display the specified estudios.
     */
    public function show($id)
    {
        $estudios = $this->estudiosRepository->find($id);

        if (empty($estudios)) {
            Flash::error('Estudios not found');

            return redirect(route('estudios.index'));
        }

        return view('estudios.show')->with('estudios', $estudios);
    }

    /**
     * Show the form for editing the specified estudios.
     */
    public function edit($id)
    {
        $estudios = $this->estudiosRepository->find($id);

        if (empty($estudios)) {
            Flash::error('Estudios not found');

            return redirect(route('estudios.index'));
        }

        return view('estudios.edit')->with('estudios', $estudios);
    }

    /**
     * Update the specified estudios in storage.
     */
    public function update($id, UpdateestudiosRequest $request)
    {
        $input = $request->all();
       
        if($request->hasFile('logo')){
            $path = $request->file('logo')->store('public/logo');
            $path2 = str_replace('public/', '', $path);
            $input['logo']=$path2;
        }else{
            
        $input['logo']='';
        }
        if(isset($input['es_empresa'])){
            $input['es_empresa']=true;
        }else{
            
            $input['es_empresa']=false;
        }
        $estudios = $this->estudiosRepository->update($input, $id);

        Flash::success('Estudios updated successfully.');

        return redirect(route('estudios.index'));
    }

    /**
     * Remove the specified estudios from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $estudios = $this->estudiosRepository->find($id);

        if (empty($estudios)) {
            Flash::error('Estudios not found');

            return redirect(route('estudios.index'));
        }

        $this->estudiosRepository->delete($id);

        Flash::success('Estudios deleted successfully.');

        return redirect(route('estudios.index'));
    }
}
