<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\DataUser;
use App\Models\Role;
use App\Models\User;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'entorno', 'dataUser'])->orderBy('id', 'DESC')
        ->paginate(27);

        return view('adm/users', [
            'users' => $users
        ]);
    }

    public function show(Request $request)
    {
        $user_data_request = $request->input('search_user');

        // Consulta única con relaciones
        $users = User::with(['role', 'entorno', 'dataUser']) // Incluye las relaciones necesarias
            ->when($user_data_request, function ($query) use ($user_data_request) {
                $query->where('name', 'like', '%' . $user_data_request . '%');
            })
            ->orderBy('id', 'DESC')
            ->paginate();

        return view('adm/users', [
            'users' => $users,
            'search_user' => $user_data_request,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'user_new_status' => 'nullable|integer',
            'environment_assign' => 'nullable|integer',
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id,
        ]);

        try {
            if ($request->filled('user_new_status')) {
                $user->update([
                    'is_active' => $request->input('user_new_status')
                ]);
            }

            if ($request->filled('environment_assign')) {
                DataUser::where('id_user', $user->id)
                    ->update(['environment_assignment_id' => $request->input('environment_assign')]);
            }

            if ($request->filled('username')) {
                $user->update([
                    'username' => $request->input('username')
                ]);
            }
            return redirect()->back()->with('success', 'Información de usuario actualizada');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar los datos' . $e->getMessage());
        }
    }

    public function getDigitizer(Request $request)
    {
        $user_name = $request->input('user');

        $digitizers = User::where('is_active', true)
            ->where('role_id', 1)->where('environment_id', 0)
            ->where(function ($query) use ($user_name) {
                $query->where("name", "like", "%{$user_name}%")
                    ->orWhere("last_name", "like", "%{$user_name}%");
            })->get();

        return response()->json([
            'success' => true,
            'data' => $digitizers
        ]);
    }

    public function acceptTyc()
    {
        $userId = Auth::id();
        $userTyc = User::where('id', $userId)->first();

        $update = $userTyc->update([
            'terms_accepted' => true
        ]);

        if ($update) {
            return back();
        }
    }

    public function environmentUsers(Request $request)
    {
        $data = [
            'user' => $request->search_user,
            'role' => $request->search_role,
            'environment' => Auth::user()->environment_id,
            'month_hours' => $request->search_month,
            'current_year' => date('Y'),
            'current_month' => date('m')
        ];
        
        $query = User::with([
            'dataUser',
            'role',
            'userHours' => function ($query) use ($data) {
                $query->where('number_month', $data['month_hours'] ?? $data['current_month'])
                    ->where('year', $data['current_year']);
            }
        ])
        ->when($data['month_hours'], function ($query) use ($data) {
            $query->whereHas('userHours', function ($query) use ($data) {
                $query->where('number_month', $data['month_hours'])
                    ->where('year', $data['current_year']);
            });
        })
        ->when($data['user'], function ($query, $user) {
            $query->where(function ($query) use ($user) {
                $query->where('name', 'LIKE', "%$user%")
                    ->orWhere('last_name', 'LIKE', "%$user%");
            });
        })
        ->when($data['role'], function ($query, $role) {
            $query->where('role_id', $role);
        })
        ->where('environment_id', $data['environment'])
        ->orderBy('id', 'DESC');
        
        $users = $query->get();
        $roles = Role::all();
        
        return view('env/manage-users', [
            'roles' => $roles,
            'usersInfo' => $users
        ]);
    }

    public function filter ($environment_id)
    {
        try {
            $environmentUsers = User::with('role', 'entorno')
                        ->whereIn('role_id', [1, 7, 10, 11, 12])
                        ->where('is_active', true)
                        ->whereNot('id', Auth::id())
                        ->where('environment_id', $environment_id)
                        ->get();

            if(!$environmentUsers){
                return response()->json(['error' => 'No se encontraron usuarios con estos criterios de búsqueda.']);
            }

            return response()->json(['success' => true, 'data' => $environmentUsers]);
        }
        catch(\Exception $e) {
            Log::error('Error al filtrar usuarios por entorno: '. $e->getMessage());
            return response()->json(['error' => 'Error al filtrar usuarios.']);
        }
    }

    public function export(Request $request){
        try{
            $environment_id = $request->environment_id;

            $users = User::with([
                'subnet:id,name',
                'role:id,name',
                'entorno',
                'dataUser'
            ]);
            
            if($environment_id != ''){
                $users->where('environment_id', $environment_id);
            }
            
            $usersTest = User::withCount('dataUser')->get();

            $users = $users->get();

            if(count($users) == 0){
                return response()->json(['error' => 'No se encontraron usuarios en este entorno.']);
            }

            $header_titles = [
                'Nombre',
                'Correo',
                'Perfil',
                'Entorno',
                'Subred',
                'Documento',
                'Teléfono',
                'Dirección',
                'RH',
                'Contrato',
                'Fecha de nacimiento',
                'EPS',
                'AFP',
                'ARL',
                'Caja compensación',
                'Fecha registro',
                'Estado'
            ];

            $csv_file = new StreamedResponse(function () use($users, $header_titles){
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, $header_titles, ';');

                foreach ($users as $user) {
                    fputcsv($handle, [
                        properNouns($user->name, $user->last_name),
                        $user->email ?? '',
                        $user->role->name ?? '',
                        $user->entorno->entorno ?? '',
                        $user->subnet->name ?? '',
                        $user->dataUser->document ?? '',
                        $user->dataUser->phone ?? '',
                        $user->dataUser->address ?? '',
                        $user->dataUser->rh ?? '',
                        $user->dataUser->contract ?? '',
                        $user->dataUser->birthdate ?? '',
                        $user->dataUser->eps ?? '',
                        $user->dataUser->afp ?? '',
                        $user->dataUser->arl ?? '',
                        $user->dataUser->caja ?? '',
                        $user->created_at->format('d/m/Y') ?? '',
                        $user->is_active ? 'Activo' : 'Inactivo'
                    ], ';');
                }

                fclose($handle);
            });

            $csv_file->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $csv_file->headers->set('Content-Disposition', 'attachment; filename="usuarios.csv"');

            return $csv_file;

        }catch(\Exception $e){
            Log::error('Error al exportar a Excel: '. $e->getMessage());
            return response()->json(['error' => 'Error al exportar los usuarios a Excel.']);
        }
    }
}