<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DataUserController;

//Administradores
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\CorrectionsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReceptionsController;
use App\Http\Controllers\TareaActasController;
use App\Http\Controllers\TvDashboardController;
use App\Http\Controllers\EntornoController;
use App\Http\Controllers\UsersController;

//Middlewares
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\UserDataMiddleware;
use App\Http\Middleware\RedirectIfSessionExpired;

//Entornos
use App\Http\Controllers\ActivitiesReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TraceabilityController;
use App\Http\Controllers\UserHoursController;

//Generales
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FormatController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OtherTimesController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductivityController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ReviewConsecutiveController;
use App\Http\Controllers\ToolControlController;
use App\Http\Controllers\ConsecutivoPermissionController;
use App\Http\Controllers\RatingController;

use App\Http\Controllers\OdinController;
use App\Http\Controllers\ErrorImportController;




/**
 * RECUPERACIÓN DE CONTRASEÑA
 */
require base_path('routes/forgot-password-web.php');

// Route::get('/forgot-password', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/politica-de-privacidad', function () {
    return view('politica-de-privacidad');
})->name('politica-de-privacidad');

Route::middleware(['web'])->group(function () {
    Route::middleware([RedirectIfAuthenticated::class])->group(function () {
        Route::get('/', [LoginController::class, 'showLoginForm'])->name('login.form');
        Route::get('register', [RegisterController::class, 'index'])->name('register');

        Route::get('session-expired', function () {
            return view("/session/expired");
        })->name('session.expired');

        Route::get('/auth', function () {
            return view("index");
        })->name('index.auth');
    });

    Route::middleware([RedirectIfSessionExpired::class, UserDataMiddleware::class])->group(function () {
        //DASHBOARD
        Route::controller(HomeController::class)->group(function () {
            Route::get('home', 'index')->name('app.home');
        });

        Route::get('/api/dashboard/metrics', [HomeController::class, 'getFilteredMetrics']);
        Route::get('/api/dashboard/admin-heavy', [HomeController::class, 'adminHomeHeavy'])->name('app.home.heavy');
        Route::get('/api/dashboard/productivity', [HomeController::class, 'membersHome'])->name('dashboard.productivity.data');
        Route::get('daily-goal/environment', function (Request $request) {
            $json = Storage::disk('public')->get('tiempo_dia.json');
            return response()->json(json_decode($json, true));
        });
        Route::get('/digitadores/exportar-excel', [HomeController::class, 'exportDigitadores'])
            ->name('digitadores.export');

        //TV DASHBOARD (pantalla de oficina en tiempo real)
        Route::get('/tv-dashboard', [TvDashboardController::class, 'index'])->name('tv.dashboard');
        Route::get('/api/tv-dashboard/data', [TvDashboardController::class, 'data'])->name('tv.dashboard.data');

        Route::post('/gesi/delivery-reminder/send', [HomeController::class, 'sendGesiReminder'])
            ->name('gesi.delivery-reminder.send');

        /**
         * DIGITADORES
         */
        require base_path('routes/profiles/digitizer.php');

        /**
         * ADMINISTRADORES
         */
        require base_path('routes/profiles/admin.php');

        //Perfil
        Route::controller(ProfileController::class)->group(function () {
            Route::get('profile/edit',                    'edit')->name('profile.edit');
            Route::post('profile/update/{user}',          'update')->name('profile.update');
            Route::post('profile/update-theme/{te}/{id}', 'updateTheme')->name('theme.update');
            Route::get('profile/gesi',                    'profileGesi')->name('profile.gesi');
        });

        Route::controller(DataUserController::class)->group(function () {
            Route::post('datauser/store/gesi', 'storeDataUserGesi')->name('datauser.store.gesi');
            Route::post('datauser/store/env',  'storeDataUserEnv')->name('datauser.store.env');
        });

        //Notificaciones
        Route::controller(NotificationController::class)->group(function () {
            Route::get('notifications',                  'show')->name('notifications.show');
            Route::post('notifications',                 'store')->name('notification.store');
            Route::patch('notifications/{notification}', 'markAsRead')->name('notification.markAsRead');
        });

        //Recepciones
        Route::controller(ReceptionsController::class)->group(function () {
            Route::get('entregar-fichas',     'environmentDeliver')->name('env.reception.deliver'); //Página entrega actualizaciones entornos
            Route::get('recibir-fichas',      'envReceptionShow')->name('env.reception.show');
            Route::get('devolver-profesional',      'envDevProfShow')->name('env.devprof.show'); //Mostrar fichas para recibir entornos
            //Mostrar fichas para recibir entornos
            Route::post('env/deliver/{user}', 'envUploadCsvFiles')->name('env.upload.reception'); //Cargue de fichas entornos
            Route::post('env/receptions/',     'envReceive')->name('env.receive');
            Route::post('env/receptions/return',     'envReturnProcess')->name('env.return.process'); //Recibir fichas entornos
            //Recibir fichas entornos
        });

        //Otros tiempos
        Route::controller(OtherTimesController::class)->group(function () {
            Route::patch('otherTimes/update/{otherTime}', 'update')->name('othertimes.update');
            Route::get('adm/othertimes',                  'index')->name('adm.othertimes');
            Route::post('adm/othertimes',                 'show')->name('adm.othertimes.show');
        });

        //Entornos
        Route::get('environments', [EntornoController::class, 'index'])->name('environment.index');
        Route::post('/editar-hora-dia', [EntornoController::class, 'editarHoraDia'])->name('editar.hora.dia');

        //Asignaciones
        Route::controller(AssignmentsController::class)->group(function () {
            Route::post('adm/assignments/{environment}', 'store')->name('assignments.store');
            Route::get('assignments/show',               'show')->name('assignments.show');
        });

        //Paquetes
        Route::get('packages/show/{package}', [PackagesController::class, 'show'])->name('packages.show');
        Route::get('packages/{package}/hours', [PackagesController::class, 'getTotalHours'])->name('packages.totalHours');

        //Productividad
        Route::put('productivity/update/{productivity}', [ProductivityController::class, 'update'])->name('productivity.update');

        //Usuarios
        Route::controller(UsersController::class)->group(function () {
            Route::get('users/filter/{environment_id}', 'filter')->name('users.filter');
            Route::get('users/show',                    'show')->name('user.show');
            Route::post('users/export',                 'export')->name('user.export');
            Route::get('env/users',                     'environmentUsers')->name('env.users');
            Route::post('members/tyc',                  'acceptTyc')->name('accept.tyc');
            Route::put('adm/users/{user}',              'update')->name('user.update');
        });

        //Trazabilidad
        Route::controller(TraceabilityController::class)->group(function () {
            Route::get('env/traceability',     'environments')->name('env.traceability');
            Route::post('traceability/export', 'export')->name('traceability.export');
        });


        Route::get('/review/prepare-delivery', [ReviewConsecutiveController::class, 'prepareDelivery'])->name('reviewconsecutives.prepare-delivery');
        Route::get('/api/search-fichas', [ReviewConsecutiveController::class, 'searchFichas'])->name('api.search-fichas');
        Route::get('/api/search-fichas-sisco', [ReviewConsecutiveController::class, 'searchFichasSisco'])->name('api.search-fichas-sisco');

        //Documentos
        Route::controller(DocumentController::class)->group(function () {
            Route::get('/documents/folder-content',     'getFolderContent')->name('documents.folder-content');
            Route::get('documents/view',                'show')->name('documents.index');
            Route::get('document/sign/{document}',      'edit')->name('document.sign'); //Vista para firmas
            Route::post('document/idFromPath',          'idFromPath')->name('document.idFromPath'); //Obtener id a partir de la URL
            Route::post('document/signed',              'signRequest')->name('document.sign.request'); //
            Route::post('adm/certifications/upload',    'uploadCertifications')->name('certifications.upload');
            Route::post('prorogation/upload',           'prorogationUpload')->name('prorogation.upload');
            Route::post('prorogation/download',         'prorogationDownload')->name('prorogation.download');
            Route::post('secop/upload',                 'secopUpload')->name('secop.upload');
            Route::post('secop/download',                'secopDownload')->name('secop.download');
            Route::post('insurance/upload',             'insuranceUpload')->name('insurance.upload');
            Route::post('insurance/download',           'insuranceDownload')->name('insurance.download');
            Route::post('informe/download',             'informeDownload')->name('informe.download');
            Route::post('documents/update/sign',        'updateSign')->name('document.update.sign'); //Guardar documento firmado
            Route::delete('document/delete',            'destroyFromPath')->name('document.delete');
            Route::delete('documents/destroy/{id}',     'destroy')->name('document.destroy');
            Route::patch('document/approve/{document}', 'aprove')->name('document.approve');
        });

        // guest/index
        Route::get('/guest/index', [OdinController::class, 'guestHome'])
        ->name('guest.index');


        Route::post('/odin/reset-license', [OdinController::class, 'resetLicense'])
        ->name('reset.license');


        /**
         * RUTAS PARA ENTORNOS Y MIEMBROS
         */
        Route::get('env/downloadExcel/',   [FileController::class, 'downloadExcel'])->name('env.downloadExcel'); //Descargar precargue
        Route::controller(UserHoursController::class)->group(function () {
            Route::post('env/hours/store',  'store')->name('hours.store');
        });

        Route::controller(ActivitiesReportController::class)->group(function () {
            Route::match(['get', 'post'], 'report-activities/generate',                 'reportActitivitesShow')->name('report.activities.show');
            Route::match(['get', 'post'], 'report-activities/template/edit/{template}', 'reportActitivitesEditForm')->name('report.activities.edit');
            Route::delete('report-activities/template/drop/{template}',                 'dropTemplate')->name('report.activities.delete');
            Route::post('env/activities/save',                                          'save')->name('activities.save');
            Route::match(['get', 'post'], 'activities/actions',                          'actionsReportActivities')->name('activities.actions');
            Route::post('activities/generate',                                          'reportActivitiesGenerate')->name('activities.generate');
            Route::delete('activity/delete/{activity}',                                 'delete')->name('activities.delete');
        });

        Route::controller(RoleController::class)->group(function () {
            Route::post('env/activities/role', 'show')->name('role.show');
            Route::get('env/activities',  'index')->name('activities.edit');
        });

        Route::controller(FormatController::class)->group(function () {
            Route::get('/dashboard/formats/onGesi',      'formatsCountGesi')->name('formats.count.gesi');
            Route::get('/adm/formats/{environment}/sds', 'format_sds')->name('formats.bases_sds');
            Route::get('/formats/by-environment/{id}',   'getSiscoByEnvironment')->name('formats.byEnvironment');
            Route::post('formats/{environment}',         'environmentBases')->name('environment.bases');
        });


        Route::controller(ProductivityController::class)->group(function () {
            Route::post('/dashboard/formats/observations', 'observationsPercentage')->name('observations.percentage');
        });

        Route::controller(ToolControlController::class)->group(function () {
            Route::get('/solicitud-consecutivos', 'index')->name('toolscontrol.index');
            Route::post('/consecutivos', 'asigCons')->name('consecutivos');
            Route::get('/get-formatos/{entorno_id}', 'getByEntorno');
            Route::get('/consecutivos_search', 'getReceptions')->name('consecutivos_search');
            Route::post('/get-format-names', 'getFormatNames')->name('get.format.names');
            Route::post('save_format_add', 'saveNewFormat')->name('save_format_add');
            Route::post('save_user_add', 'saveNewFUserform')->name('save_user_add');
            Route::post('update_localidad', 'updateFormat')->name('update_localidad');
            Route::post('update_lformat', 'updateFormatAdd')->name('update_lformat');
            Route::delete('/eliminar-usuario-asignado/{registroId}/{userId}', 'eliminarUsuarioAsignado')->name('eliminar.usuario.asignado');
        });

        //Permisos de consecutivos del mes anterior
        Route::controller(ConsecutivoPermissionController::class)->group(function () {
            Route::post('/consecutivo-permisos', 'store')->name('consecutivo.permission.store');
            Route::get('/consecutivo-permisos/estado', 'status')->name('consecutivo.permission.status');
        });

        Route::controller(ReviewConsecutiveController::class)->group(function () {
            Route::get('/revisar-consecutivos', 'index')->name('reviewconsecutives.index');
            Route::post('/reviewconsecutives/update-status', 'updateStatus')->name('reviewconsecutives.update-status');
            Route::post('/reviewconsecutives/update-status-gesi', 'entregarGesi')->name('reviewconsecutives.update-status-gesi');

            Route::post('/reviewconsecutives/update-status-Sisco', 'updateStatusSisco')->name('reviewconsecutives.update-status-sisco');
            Route::post('/reviewconsecutives/update-observations', 'storeSisco')->name('consecutivesSisco.store');
            Route::post('/revisar-consecutivos/editar', 'edit')->name('reviewconsecutives.edit');
            Route::post('/revisar-consecutivos/update', 'update')->name('reviewconsecutives.update');
            Route::post('/reviewconsecutives/export', 'export')->name('reviewconsecutives.export');
            Route::get('/reviewconsecutives/delay-alerts/export', 'exportDelayAlerts')->name('reviewconsecutives.delay-alerts.export');
        });

        Route::post('/configurar-tarea', [TareaActasController::class, 'configurarTarea'])->name('configurar.tarea');

        Route::post('/save-rating', [RatingController::class, 'store'])
            ->middleware('auth')
            ->name('rating.store');


        // cambiar este a un controlador para usar mas cosas

        Route::get('/validator', [OdinController::class, 'validatorOdin'])->name('validator');
        Route::post('/validator/update-tokens', [OdinController::class, 'updateTokens'])->name('validator.updateTokens');

        Route::get('/cronicos', [OdinController::class, 'cronicosOdin'])->name('cronicos');
        // vista CronicosLab 
        Route::get('/cronicos-lab', [OdinController::class, 'cronicosLab'])->name('cronicos.lab');

        // vista croonicosV2
        Route::get('/cronicos-v2', [OdinController::class, 'cronicosV2'])->name('cronicos.v2');

        Route::get('/creportesGesi', [OdinController::class, 'reportesGesi'])->name('reportesGesi');

        Route::get('/api/rules-json', [OdinController::class, 'getRules']);
        Route::post('/validator/send-to-digitizers', [ErrorImportController::class, 'commitFromValidator'])
            ->name('validator.errors.sendToDigitizers');


        Route::prefix('validator-builder')->group(function () {

            Route::get('/', [OdinController::class, 'index'])
                ->name('validator.builder.index');

            Route::get('/load/{file}', [OdinController::class, 'load'])
                ->name('validator.builder.load');

            Route::post('/save', [OdinController::class, 'save'])
                ->name('validator.builder.save');

            Route::post('/save-listas', [OdinController::class, 'saveListas'])
                ->name('validator.builder.saveListas');

            // Mercado Pago integration
            Route::get('/mp/checkout', [OdinController::class, 'mpCheckout'])->name('mp.checkout');
            Route::post('/mp/create-preference', [OdinController::class, 'createMpPreference'])->name('mp.createPreference');

            
            Route::delete('/delete/{file}', [OdinController::class, 'delete'])
            ->name('validator.builder.delete');
            });

            Route::get('prepararArchivo', [OdinController::class, 'prepararArchivo'])->name('preparar.archivo');
            
            Route::get(
                '/validator-builder',
                [OdinController::class, 'index']
                )->name('validator.builder.index');
                
                });

            Route::get('dataLaboral', [OdinController::class, 'dataLaboral'])->name('dataLaboral');

            Route::get('tamizaje-laboral', [OdinController::class, 'tamizajeLaboral'])->name('tamizajeLaboral');

        });
                
                //Route::get('/save_productivity_sds', [ProductivitySdsController::class, 'store'])->name('guardar.productividad');
                
                Route::get('/csrf-token', function () {
                    return response()->json(['token' => csrf_token()]);
                    });
                    
                    Route::get('/mecanografia', function () {
    return view('mecanografia');
    })->name('mecanografia');

    Route::get('/tv-dashboard', [TvDashboardController::class, 'index'])->name('tv.dashboard');
        Route::get('/api/tv-dashboard/data', [TvDashboardController::class, 'data'])->name('tv.dashboard.data');

    Route::get('/prueba', function () {
    return view('prueba_dig');
    })->name('prueba');
    Route::get('/tecnicos', function () {
    return view('pruebatec');
    })->name('tecnicos');

    // Envuelta explícitamente con este middleware porque un cierre de grupo
    // sobrante más arriba en este archivo deja las rutas de aquí en adelante
    // fuera de él, y estas vistas necesitan $environments/$user (compartidos
    // por UserDataMiddleware) para renderizar el layout de admin.
    Route::middleware([RedirectIfSessionExpired::class, UserDataMiddleware::class])->group(function () {
        Route::get('/gestantes', function () {
            return view('gestantes');
        })->name('gestantes');

        Route::get('/tamizaje-laboral', function () {
            return view('adm.tamizajeLaboral');
        })->name('tamizaje.laboral');
    });

    Route::post('/prueba/enviar-resultados', [\App\Http\Controllers\PruebaDigController::class, 'enviarResultados'])->name('prueba.enviar_resultados');

    Route::post('/tecnicos/enviar-resultados', [\App\Http\Controllers\PruebaTecController::class, 'enviarResultados'])->name('tecnicos.enviar_resultados');
    
    Route::get('/areas', [AreaController::class, 'cargarAreas']);
    Route::put('/areas/{nombre_area}', [AreaController::class, 'actualizarOAgregarArea']);
    Route::delete('/areas/{nombre_area}', [AreaController::class, 'eliminarArea']);
    
    
    Route::get('/verificar-ficha/{numero}/{id}', [AreaController::class, 'verificar']);
    
    
Route::post(
    '/validator-builder/mp/webhook',
    [OdinController::class, 'mpWebhook']
)->name('mp.webhook');

Route::get('/validator-builder/mp/success', [OdinController::class, 'mpSuccess'])
    ->name('mp.success');

Route::get('/validator-builder/mp/cancel', [OdinController::class, 'mpCancel'])
    ->name('mp.cancel');

