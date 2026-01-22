<?php

namespace App\Http\Controllers;

use App\Http\Requests\Createestudios_usuariosRequest;
use App\Http\Requests\Updateestudios_usuariosRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\estudios_usuariosRepository;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\usuario_grupoeconomico;
use App\Models\estudios_usuarios;
use App\Models\User;
use App\Models\estudios;
use Flash;

class estudios_usuariosController extends AppBaseController
{
    /** @var estudios_usuariosRepository $estudiosUsuariosRepository*/
    private $estudiosUsuariosRepository;

    public function __construct(estudios_usuariosRepository $estudiosUsuariosRepo)
    {
        $this->estudiosUsuariosRepository = $estudiosUsuariosRepo;
    }

    /**
     * Display a listing of the estudios_usuarios.
     */
    public function index(Request $request)
    {
        $estudiosUsuarios = estudios_usuarios::with('User')->with('estudios')->paginate(10);
        return view('estudios_usuarios.index')
            ->with('estudiosUsuarios', $estudiosUsuarios);
    }

    /**
     * Show the form for creating a new estudios_usuarios.
     */
    public function create()
    {
$Usuarios = User::select(
        'users.id',
        DB::raw("CONCAT(users.name, ' ', users.surname, ' (', users.email, ')') as full_label")
    )
    ->leftJoin('estudios_usuarios', 'estudios_usuarios.id_users', '=', 'users.id')
    ->whereNull('estudios_usuarios.id')
    ->pluck('full_label', 'id');
        $estudios=estudios::Select('estudios.*')->pluck('razon_social', 'id');

        return view('estudios_usuarios.create')->with('usuarios',$Usuarios)->with('estudios',$estudios);
    }

    /**
     * Store a newly created estudios_usuarios in storage.
     */
    public function store(Createestudios_usuariosRequest $request)
    {
        $input = $request->all();

        $estudiosUsuarios = $this->estudiosUsuariosRepository->create($input);

        Flash::success('Estudios Usuarios saved successfully.');

        return redirect(route('estudios_usuarios.index'));
    }

    /**
     * Display the specified estudios_usuarios.
     */
    public function show($id)
    {
        $estudiosUsuarios = $this->estudiosUsuariosRepository->find($id);

        if (empty($estudiosUsuarios)) {
            Flash::error('Estudios Usuarios not found');

            return redirect(route('estudios_usuarios.index'));
        }

        return view('estudios_usuarios.show')->with('estudiosUsuarios', $estudiosUsuarios);
    }

    /**
     * Show the form for editing the specified estudios_usuarios.
     */
    public function edit($id)
    {
        $estudiosUsuarios = $this->estudiosUsuariosRepository->find($id);

        if (empty($estudiosUsuarios)) {
            Flash::error('Estudios Usuarios not found');

            return redirect(route('estudios_usuarios.index'));
        }

        return view('estudios_usuarios.edit')->with('estudiosUsuarios', $estudiosUsuarios);
    }

    /**
     * Update the specified estudios_usuarios in storage.
     */
    public function update($id, Updateestudios_usuariosRequest $request)
    {
        $estudiosUsuarios = $this->estudiosUsuariosRepository->find($id);

        if (empty($estudiosUsuarios)) {
            Flash::error('Estudios Usuarios not found');

            return redirect(route('estudios_usuarios.index'));
        }

        $estudiosUsuarios = $this->estudiosUsuariosRepository->update($request->all(), $id);

        Flash::success('Estudios Usuarios updated successfully.');

        return redirect(route('estudios_usuarios.index'));
    }

    /**
     * Remove the specified estudios_usuarios from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $estudiosUsuarios = $this->estudiosUsuariosRepository->find($id);

        if (empty($estudiosUsuarios)) {
            Flash::error('Estudios Usuarios not found');

            return redirect(route('estudios_usuarios.index'));
        }

        $this->estudiosUsuariosRepository->delete($id);

        usuario_grupoeconomico::where('id_users', $estudiosUsuarios->id_users)->delete();
        Flash::success('Estudios Usuarios deleted successfully.');

        return redirect(route('estudios_usuarios.index'));
    }
}
