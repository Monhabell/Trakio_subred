<!DOCTYPE html>
<html lang="es">

<head>
    <title>Trakio</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="lang" content="es">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/logo_temp.png') }}" />

    <script>
        (function () {
            const savedTheme = localStorage.getItem('gesi-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom stylesheets -->
    @vite(['resources/css/global.css', 'resources/css/adm/main-adm.css', 'resources/css/adm/shell.css'])
</head>

<body id="page-top" class="adm-body">
    <div class="adm-shell">

        @if (Session::has('success'))
            <div class="alert alert-success py-1 flex-center mt-3 position-fixed top-0 start-50 translate-middle-x" role="alert" style="z-index: 1080;">
                <i class="fa-solid fa-circle-check fa-bounce"></i>
                <span class="ms-2">{{ Session::get('success') }}</span>
            </div>
        @endif

        @if (Session::has('error'))
            <div class="alert alert-danger py-1 flex-center mt-3 position-absolute end-50" role="alert" style="z-index: 1080;">
                <i class="fa-solid fa-circle-xmark fa-beat"></i>
                <span class="ms-2">{{ Session::get('error') }}</span>
            </div>
        @endif

        {{-- ── NAVBAR ── --}}
        <nav class="adm-nav" id="admNav">
            <a class="adm-nav-brand" href="{{ route('app.home') }}">
                <img src="{{ asset('img/logo_temp.png') }}" alt="Trakio">
                Trak<span>io</span>
            </a>

            <button class="adm-nav-toggle" id="admNavToggle" type="button" aria-label="Abrir menú">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="adm-nav-links" id="admNavLinks">
                <a class="adm-nav-link {{ Request::is('home') ? 'is-active' : '' }}" href="{{ route('app.home') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>

                {{-- ── OPERACIONES: Recepciones (submenú), Asignación (submenú), Trazabilidad ── --}}
                <div class="adm-nav-dropdown">
                    <a class="adm-nav-link {{ Request::is('receptions/*') || Request::is('packages/*') || Request::is('traceability') ? 'is-active' : '' }}" href="#" data-adm-dropdown>
                        <i class="fa-solid fa-diagram-project"></i> Operaciones <i class="fa-solid fa-chevron-down caret"></i>
                    </a>
                    <div class="adm-nav-dropdown-menu">
                        <div class="adm-nav-submenu">
                            <a class="adm-nav-dropdown-item adm-nav-submenu-trigger {{ Request::is('receptions/*') ? 'is-active' : '' }}" href="#">
                                <i class="fa-brands fa-get-pocket"></i> Recepciones <i class="fa-solid fa-chevron-right caret"></i>
                            </a>
                            <div class="adm-nav-submenu-panel">
                                <div class="adm-nav-dropdown-header">Elija un entorno</div>
                                @foreach ($environments as $environment)
                                    @if ($environment->entorno != 'Gesi')
                                        <a class="adm-nav-dropdown-item" href="{{ route('receptions.index', $environment->id) }}">{{ $environment->entorno }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="adm-nav-submenu">
                            <a class="adm-nav-dropdown-item adm-nav-submenu-trigger {{ Request::is('packages/*') ? 'is-active' : '' }}" href="#">
                                <i class="fa-solid fa-list-check"></i> Asignación <i class="fa-solid fa-chevron-right caret"></i>
                            </a>
                            <div class="adm-nav-submenu-panel">
                                <div class="adm-nav-dropdown-header">Elija un entorno</div>
                                @foreach ($environments as $environment)
                                    @if ($environment->entorno != 'Gesi')
                                        <a class="adm-nav-dropdown-item" href="{{ route('packages.index', ['environment' => $environment->entorno]) }}">{{ $environment->entorno }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <a class="adm-nav-dropdown-item {{ Request::is('traceability') ? 'is-active' : '' }}" href="{{ route('adm.traceability') }}">
                            <i class="fa-solid fa-diagram-predecessor"></i> Trazabilidad
                        </a>
                    </div>
                </div>

                {{-- ── GESTIÓN: Otros tiempos, Usuarios, Bases ── --}}
                <div class="adm-nav-dropdown">
                    <a class="adm-nav-link {{ Request::is('adm/othertimes') || Request::is('users') || Request::is('formats*') ? 'is-active' : '' }}" href="#" data-adm-dropdown>
                        <i class="fa-solid fa-gears"></i> Gestión <i class="fa-solid fa-chevron-down caret"></i>
                    </a>
                    <div class="adm-nav-dropdown-menu">
                        <a class="adm-nav-dropdown-item {{ Request::is('adm/othertimes') ? 'is-active' : '' }}" href="{{ route('adm.othertimes') }}">
                            <i class="fa-solid fa-clock"></i> Otros tiempos
                        </a>
                        <a class="adm-nav-dropdown-item {{ Request::is('users') ? 'is-active' : '' }}" href="{{ route('users.index') }}">
                            <i class="fa-solid fa-users"></i> Usuarios
                        </a>
                        <a class="adm-nav-dropdown-item {{ Request::is('formats/*') || Request::is('formats') ? 'is-active' : '' }}" href="{{ route('formats.index') }}">
                            <i class="fa-solid fa-table-list"></i> Bases
                        </a>
                    </div>
                </div>

                {{-- ── DOCUMENTOS: Actas, Gestión TH ── --}}
                <div class="adm-nav-dropdown">
                    <a class="adm-nav-link {{ Request::is('acts') || Request::is('documents/*') ? 'is-active' : '' }}" href="#" data-adm-dropdown>
                        <i class="fa-solid fa-file-word"></i> Documentos <i class="fa-solid fa-chevron-down caret"></i>
                    </a>
                    <div class="adm-nav-dropdown-menu">
                        <a class="adm-nav-dropdown-item {{ Request::is('acts') ? 'is-active' : '' }}" href="{{ route('adm.acts') }}">
                            <i class="fa-solid fa-file-word"></i> Actas
                        </a>
                        <a class="adm-nav-dropdown-item {{ Request::is('documents/*') ? 'is-active' : '' }}" href="{{ route('documents.index') }}">
                            <i class="fa-solid fa-file-word"></i> Gestión TH
                        </a>
                    </div>
                </div>

                {{-- ── HERRAMIENTAS: Odin, Mecanografía ── --}}
                <div class="adm-nav-dropdown">
                    <a class="adm-nav-link" href="#" data-adm-dropdown>
                        <i class="fa-solid fa-toolbox"></i> Herramientas <i class="fa-solid fa-chevron-down caret"></i>
                    </a>
                    <div class="adm-nav-dropdown-menu">
                        <a class="adm-nav-dropdown-item" href="{{ route('gestantes') }}">
                            <i class="fa-solid fa-baby"></i> Gestantes
                        </a>

                        <a class="adm-nav-dropdown-item" href="{{ route('tamizaje.laboral') }}">
                            <i class="fa-solid fa-briefcase-medical"></i> Tamizajes laboral
                        </a>
                        <a class="adm-nav-dropdown-item" href="{{ route('preparar.archivo') }}">
                            <i class="fa-solid fa-briefcase-medical"></i> Preparar archivos
                        </a>

                        <a class="adm-nav-dropdown-item" href="{{ route('dataLaboral') }}">
                            <i class="fa-solid fa-baby"></i> Laboral reportes
                        </a>

                        
                        <a class="adm-nav-dropdown-item" href="{{ route('guest.index') }}">
                            <i class="fa-solid fa-diagram-predecessor"></i> Odin
                        </a>
                        <a class="adm-nav-dropdown-item" href="{{ route('mecanografia') }}">
                            <i class="fa-solid fa-keyboard"></i> Mecanografía
                        </a>
                        <a class="adm-nav-dropdown-item" href="{{ route('prueba') }}">
                            <i class="fa-solid fa-keyboard"></i> Prueba Digitación
                        </a>
                        <a class="adm-nav-dropdown-item" href="{{ route('tecnicos') }}">
                            <i class="fa-solid fa-keyboard"></i> Prueba tecnicos
                        </a>
                        <a class="adm-nav-dropdown-item" href="{{ route('tv.dashboard') }}" target="_blank" rel="noopener">
                            <i class="fa-solid fa-tv"></i> Pantalla TV
                        </a>
                    </div>
                </div>

                <button class="theme-btn" id="themeToggle">
                    <span class="theme-icon" id="themeIcon">🌙</span>
                    <span id="themeLabel">Modo oscuro</span>
                </button>
            </div>

            <div class="adm-nav-actions">
                @php
                    $ratingStats = \Illuminate\Support\Facades\Cache::remember('site_rating_stats', 60, function () {
                        return [
                            'average' => \App\Models\SiteRating::avg('rating') ?? 0,
                            'count' => \App\Models\SiteRating::count(),
                        ];
                    });
                    $ratingFullStars = round($ratingStats['average']);
                @endphp
                <div class="adm-nav-rating d-none d-lg-flex">
                    <span class="stars">
                        @for ($i = 1; $i <= 5; $i++){{ $i <= $ratingFullStars ? '★' : '☆' }}@endfor
                    </span>
                    <span>{{ number_format($ratingStats['average'], 1) }}/5</span>
                </div>

                <div class="adm-nav-dropdown">
                    <div class="adm-nav-bell" data-adm-dropdown id="alertsDropdown">
                        <i class="fas fa-bell" id="bell"></i>
                        <span class="dot" id="counter-notifications"></span>
                    </div>
                    <div class="adm-nav-dropdown-menu scrollable-container notifications-panel" style="right:0; left:auto;" aria-labelledby="alertsDropdown"></div>
                </div>

                <div class="adm-nav-dropdown">
                    <div class="adm-nav-user" data-adm-dropdown id="userDropdown">
                        <input type="hidden" value="{{ $user->id }}" id="user_id">
                        <div class="adm-nav-user-info">
                            <span class="adm-nav-user-name">{{ $user->name }} {{ $user->last_name }}</span>
                            <span class="adm-nav-user-role">{{ $user->role->name }}</span>
                        </div>
                        <img src="{{ $data_user && $data_user->url_img ? asset('img/img_perfil/' . $data_user->id_user . '/' . $data_user->url_img) : asset('img/undraw_profile.svg') }}" alt="">
                    </div>
                    <div class="adm-nav-dropdown-menu" style="right:0; left:auto;" aria-labelledby="userDropdown">
                        <a class="adm-nav-dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user fa-sm fa-fw me-2"></i> Perfil
                        </a>
                        <form action="{{ route('logout') }}" class="d-none" method="POST" id="form-logout">
                            @csrf
                        </form>
                        <a class="adm-nav-dropdown-item" href="#" id="btn-logout">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw me-2"></i> Cerrar sesión
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Begin main content app -->
        <div class="adm-main" id="main-container">
            @yield('main')
        </div>

        @if (count(auth()->user()->notificacionesCumpleaños) > 0)
            <button type="button" class="btn-flotante " onclick="toggleIcons()">
                <i class="fa-solid fa-cake-candles"></i>
                <span class="position-absolute top-1 start-0 translate-middle badge rounded-pill bg-danger">
                    {{ count(auth()->user()->notificacionesCumpleaños) }}
                    <span class="visually-hidden">unread messages</span>
                </span>
            </button>

            <div class="icono shadow-sm" id="icono1">
                <div class="infoBirthday">
                    <div class="titleBirthday"></div>
                    @foreach (auth()->user()->notificacionesCumpleaños as $notification)
                        @php
                            $data = json_decode($notification->data, true);
                            $datosCumple = $data['datosCUmple'] ?? [];
                            $birthdateStr = $datosCumple['birthdate'] ?? 'No birthdate';

                            if ($birthdateStr) {
                                $birthdate = $birthdateStr ? \Carbon\Carbon::parse($birthdateStr) : null;
                                $currentDate = \Carbon\Carbon::now();
                                $age = floor($birthdate->diffInYears($currentDate));
                            } else {
                                $age = 'Desconocida';
                            }
                        @endphp

                        <div class="containerBirthday p-2 shadow-sm">
                            <div class="row m-1 p-0">
                                @php
                                    $imgPath =
                                        'img/img_perfil/' . $data['id_user']['id'] . '/' . $data['id_user']['url_img'];
                                    $defaultAvatar =
                                        $datosCumple['sex'] == 'Masculino'
                                            ? asset('img/avatar-masculino.png')
                                            : asset('img/avatar-femenino.png');
                                @endphp

                                <div class="col-2 p-0">
                                    <img id="img_Perfil_birthday" class="img_Perfil_birthday"
                                        src="{{ asset($imgPath) }}"
                                        onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';">
                                </div>

                                <div class="col-6 p-0 pl-2 pl-3">
                                    <h6 class="CumTitle">Hoy cumple
                                        {{ $datosCumple['sex'] == 'Masculino' ? $age : '' }} años</h6>
                                    <h6 class="NameCum">{{ $data['id_user']['name'] }}
                                        {{ $data['id_user']['last_name'] }}</h6>
                                </div>
                                <div class="col-4 d-flex justify-content-center py-3" role="group"
                                    aria-label="Basic example">
                                    <form id="likes-form-{{ $data['id_user']['id'] }}"
                                        action="{{ route('likes-birthday') }}" method="post">
                                        @csrf
                                        <input type="text" name="UserBirthdays"
                                            value="{{ $data['id_user']['id'] }}" hidden>
                                        <button type="button" id="like-button-{{ $data['id_user']['id'] }}"
                                            value="{{ $notification->likes }}" class="btn btn-primary btn-sm"
                                            onclick="updatebuttom({{ $data['id_user']['id'] }})">
                                            <i class="fa-solid fa-heart me-2"></i>
                                            {{ $notification->likes }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div id="liked-users-images-{{ $data['id_user']['id'] }}"
                                class="position-relative d-flex justify-content-end align-items-center">
                                @php
                                    $likedUsers = json_decode($notification->is_user_likes, true) ?: [];
                                @endphp
                                @if (!empty($likedUsers))
                                    @foreach ($likedUsers as $index => $user)
                                        @php
                                            $offset = $index * 15;
                                        @endphp
                                        <img class="position-absolute"
                                            src="{{ asset('img/img_perfil/' . $user['id'] . '/' . $user['image_url']) }}"
                                            alt="User Image"
                                            style="width:20px;height:20px; border-radius:50%; right:{{ $offset }}px;">
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @include('modals.dataUser')
        @include('layouts.chatbot.index')

        <x-rating-modal />

        @if (!Request::is('document/sign/*'))
            <x-actas-firma />
        @endif

    </div>{{-- /adm-shell --}}

    @routes
    <!-- Custom variables-->
    <script>
        const routeBaseTraceability = '{{ route('adm.traceability') }}';
        const routeReceiveReception = '{{ route('reception.receive') }}';
        const routeReceiveReceptionSisco = '{{ route('reception.receive.sisco') }}';
        const environments = @json($environments);
    </script>

    @if (Request::is('receptions/*'))
        <script>
            const environmentId = {{ $environment_id }};
        </script>
    @endif

    <!-- Bootstrap core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"></script>

    <!-- Core plugin JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"
        integrity="sha512-ahmSZKApTDNd3gVuqL5TQ3MBTj8tL5p2tYV05Xxzcfu6/ecvt1A0j6tfudSGBVuteSoTRMqMljbfdU0g2eDNUA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://kit.fontawesome.com/41bcea2ae3.js" crossorigin="anonymous"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js"
        integrity="sha512-Z8CqofpIcnJN80feS2uccz+pXWgZzeKxDsDNMD/dJ6997/LSRY+W4NmEt9acwR+Gt9OHN0kkI1CTianCwoqcjQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    {{-- Axios --}}
    <script src="https://cdn.jsdelivr.net/npm/axios@1.4.0/dist/axios.min.js"></script>

    {{-- Chart js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Moment js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <!-- Custom scripts -->
    @vite(['resources/js/adm/main-adm.js', 'resources/js/documents/main-docs.js', 'resources/js/assets/profile.js', 'resources/js/chatbot/index.js']);
    <script type="text/javascript" src="{{ asset('js/adm/app-adm.js') }}?0.3"></script>

    <script>
        // Toggle del menú en mobile
        document.getElementById('admNavToggle').addEventListener('click', function () {
            document.getElementById('admNavLinks').classList.toggle('open');
        });

        // Dropdowns del navbar (recepciones, asignación, alertas, usuario)
        document.querySelectorAll('[data-adm-dropdown]').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var wrapper = trigger.closest('.adm-nav-dropdown');
                var wasOpen = wrapper.classList.contains('open');
                document.querySelectorAll('.adm-nav-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
                if (!wasOpen) wrapper.classList.add('open');
            });
        });

        document.addEventListener('click', function (e) {
            document.querySelectorAll('.adm-nav-dropdown.open').forEach(function (d) {
                if (!d.contains(e.target)) d.classList.remove('open');
            });
        });

        // Submenú anidado (Recepciones/Asignación): no debe cerrar el menú padre ni saltar al inicio
        document.querySelectorAll('.adm-nav-submenu-trigger').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        document.getElementById('btn-logout').addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('form-logout').submit();
        });

        function updatebuttom(id) {
            var form = document.getElementById('likes-form-' + id);
            var formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': formData.get('_token'),
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message === 'Likes updated successfully') {
                        var likesButton = document.getElementById('like-button-' + id);
                        var currentLikes = parseInt(likesButton.textContent.trim());
                        likesButton.innerHTML = `<i class="fa-solid fa-heart me-2"></i>${currentLikes + 1}`;
                        updateLikedUsers(data.likedUsers, id);
                    } else {
                        alert('Error updating likes');
                    }
                });
        };

        function updateLikedUsers(likedUsers, id) {
            var imagesContainer = document.getElementById('liked-users-images-' + id);
            imagesContainer.innerHTML = '';

            likedUsers.forEach((user, index) => {
                var offset = index * 15;
                var imgElement = document.createElement('img');
                imgElement.className = 'position-absolute';
                imgElement.src = `{{ asset('img/img_perfil/') }}/${user.id}/${user.image_url}`;
                imgElement.alt = 'User Image';
                imgElement.style.width = '20px';
                imgElement.style.height = '20px';
                imgElement.style.borderRadius = '50%';
                imgElement.style.right = `${offset}px`;
                imagesContainer.appendChild(imgElement);
            });
        }

        function toggleIcons() {
            var iconos = document.querySelectorAll('.icono');
            iconos.forEach(function(icono) {
                icono.classList.toggle('mostrar');
            });
        }
    </script>

    <script>

    // ---- Toggle de tema (claro / oscuro) ----
    (function () {

        const root = document.documentElement;
        const btn = document.getElementById('themeToggle');
        const icon = document.getElementById('themeIcon');
        const label = document.getElementById('themeLabel');

        let theme = localStorage.getItem('gesi-theme') || 'dark';

        function applyTheme() {

            root.setAttribute('data-theme', theme);

            const isDark = theme === 'dark';

            icon.textContent = isDark ? '☀️' : '🌙';
            label.textContent = isDark
                ? 'Modo claro'
                : 'Modo oscuro';

            localStorage.setItem('gesi-theme', theme);
        }

        btn.addEventListener('click', () => {
            theme = theme === 'dark'
                ? 'light'
                : 'dark';

            applyTheme();
        });

        applyTheme();

    })();
</script>
</body>
</html>
