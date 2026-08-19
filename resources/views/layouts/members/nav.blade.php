<!DOCTYPE html>
<html lang="es">

<head>
    <title>Trakio</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="lang" content="es">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/logo_temp.png') }}"/>

    <script>
        (function () {
            const savedTheme = localStorage.getItem('gesi-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Custom stylessheets -->
    @vite([
        'resources/css/members/shell.css',
        'resources/css/members/main-members.css',
        'resources/css/members/tool-control.css',
    ])

    @if(Request::is('env/traceability'))
        @vite(['resources/css/env/components.css', 'resources/css/global.css', 'resources/css/env/traceability.css'])
    @endif
</head>

<body id="page-top" class="mem-body">

    <div class="mem-shell">

        @if (session('success'))
            <div class="alert alert-success py-1 flex-center mt-3 position-fixed top-0 start-50 translate-middle-x" role="alert" style="z-index: 1080;">
                <i class="fa-solid fa-circle-check fa-bounce"></i>
                <span class="ms-2">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-1 flex-center mt-3 position-absolute end-50" role="alert" style="z-index: 1080;">
                <i class="fa-solid fa-circle-xmark fa-beat"></i>
                <span class="ms-2">{{ session('error') }}</span>
            </div>
        @endif

        {{-- ── NAVBAR ── --}}
        <nav class="mem-nav" id="memNav">
            <a class="mem-nav-brand" href="{{ route('dashboard.productivity.data') }}">
                <img src="{{ asset('img/logo_temp.png') }}" alt="Trakio">
                Trak<span>io</span>
            </a>

            <button class="mem-nav-toggle" id="memNavToggle" type="button" aria-label="Abrir menú">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="mem-nav-links" id="memNavLinks">
                <a class="mem-nav-link {{ Request::is('api/dashboard/productivity') || Request::is('home') ? 'is-active' : '' }}" href="{{ route('dashboard.productivity.data') }}">
                    <i class="fas fa-tachometer-alt"></i> Inicio
                </a>

                <a class="mem-nav-link {{ Request::is('env/traceability') ? 'is-active' : '' }}" href="{{ route('env.traceability') }}">
                    <i class="fa-solid fa-magnifying-glass"></i> Observaciones fichas
                </a>

                <a class="mem-nav-link {{ Request::is('solicitud-consecutivos') ? 'is-active' : '' }}" href="{{ route('toolscontrol.index') }}">
                    <i class="fa-solid fa-folder-open"></i> Consecutivos
                </a>

                <a class="mem-nav-link {{ Request::is('report-activities/*') ? 'is-active' : '' }}" href="{{ route('report.activities.show') }}">
                    <i class="fa-solid fa-file-contract"></i> Generar informe
                </a>

                <a class="mem-nav-link {{ Request::is('documents/*') ? 'is-active' : '' }}" href="{{ route('documents.index') }}">
                    <i class="fa-solid fa-folder-open"></i> Cargar documentos
                </a>

                <button class="theme-btn" id="themeToggle">
                    <span class="theme-icon" id="themeIcon">🌙</span>
                    <span id="themeLabel">Modo oscuro</span>
                </button>
            </div>

            <div class="mem-nav-actions">
                <div class="mem-nav-dropdown">
                    <div class="mem-nav-bell" data-mem-dropdown id="alertsDropdown">
                        <i id="bell" class="fas fa-bell"></i>
                        <span class="badge-counter" id="counter-notifications" hidden></span>
                    </div>
                    <div class="mem-nav-dropdown-menu scrollable-container" style="right:0; left:auto;" aria-labelledby="alertsDropdown"></div>
                </div>

                <div class="mem-nav-dropdown">
                    <input type="hidden" value="" id="user_id">
                    <div class="mem-nav-user" data-mem-dropdown id="userDropdown">
                        <div class="mem-nav-user-info">
                            <span class="mem-nav-user-name">{{ $user->name }} {{ $user->last_name }}</span>
                            <span class="mem-nav-user-role">{{ $user->role->name }} / Subred {{ $user->subnet->name }}</span>
                        </div>
                        <img class="img-profile" src="{{ $data_user && $data_user->url_img ? asset('img/img_perfil/'.$data_user->id_user.'/'.$data_user->url_img) : asset('img/undraw_profile.svg') }}">
                    </div>
                    <div class="mem-nav-dropdown-menu" style="right:0; left:auto;" aria-labelledby="userDropdown">
                        <a class="mem-nav-dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user fa-sm fa-fw me-2"></i> Perfil
                        </a>

                        <form action="{{ route('logout') }}" class="d-none" method="POST" id="form-logout">
                            @csrf
                        </form>

                        <a class="mem-nav-dropdown-item" href="#" id="btn-logout">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw me-2"></i> Cerrar sesión
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        <!-- End of Navbar -->

        <!-- Begin Page Content -->
        <div class="mem-main" id="main-container">
            @yield('main')
        </div>

        {{-- MODAL DATA USERS --}}
        @include('modals/dataUser')

        @if (!$user->terms_accepted)
            {{-- MODAL TYC  --}}
            @include('modals/tyc')
        @endif


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
                    $age = floor($birthdate->diffInYears($currentDate)); // Calcula la edad en años
                    } else {
                    $age = 'Desconocida'; // Edad desconocida si la fecha no está presente
                    }
                    @endphp


                    <div class="containerBirthday p-2 shadow-sm">
                        <div class="row m-1 p-0">
                            @php
                                $imgPath = 'img/img_perfil/' . $data['id_user']['id'] . '/' . $data['id_user']['url_img'];
                                $defaultAvatar = $datosCumple['sex'] == "Masculino"
                                    ? asset('img/avatar-masculino.png')
                                    : asset('img/avatar-femenino.png');
                            @endphp

                            <div class="col-2 p-0">
                                <img id="img_Perfil_birthday"
                                    class="img_Perfil_birthday"
                                    src="{{ asset($imgPath) }}"
                                    onerror="this.onerror=null; this.src='{{ $defaultAvatar }}';">
                            </div>

                            <div class="col-6 p-0 pl-3">
                                <h6 class="CumTitle">Hoy cumple {{$datosCumple['sex'] == "Masculino" ? $age : ''}} años</h6>
                                <h6 class="NameCum">{{ $data['id_user']['name'] }} {{ $data['id_user']['last_name'] }}</h6>
                            </div>
                            <div class="col-4 d-flex justify-content-center py-3" role="group" aria-label="Basic example">
                                <form id="likes-form-{{ $data['id_user']['id'] }}" action="{{ route('likes-birthday') }}"
                                    method="post">
                                    @csrf
                                    <input type="text" name="UserBirthdays" value="{{ $data['id_user']['id'] }}" hidden>
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
                                        src="{{ asset('img/img_perfil/' . $user['id'] .'/'. $user['image_url']) }}" alt="User Image"
                                        style="width:20px;height:20px; border-radius:50%; right:{{ $offset }}px;">
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif

        <x-rating-modal />

    </div>{{-- /mem-shell --}}

    @routes

    <!-- Bootstrap core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"></script>

    <!-- Core plugin JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js" integrity="sha512-ahmSZKApTDNd3gVuqL5TQ3MBTj8tL5p2tYV05Xxzcfu6/ecvt1A0j6tfudSGBVuteSoTRMqMljbfdU0g2eDNUA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.4.0/dist/axios.min.js"></script>
    <script src="https://kit.fontawesome.com/41bcea2ae3.js" crossorigin="anonymous"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js" integrity="sha512-Z8CqofpIcnJN80feS2uccz+pXWgZzeKxDsDNMD/dJ6997/LSRY+W4NmEt9acwR+Gt9OHN0kkI1CTianCwoqcjQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Moment js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script>
        function updatebuttom (id) {
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
                       // Actualiza las imágenes de usuarios
                       updateLikedUsers(data.likedUsers ,id);
                   } else {
                       alert('Error updating likes');
                   }
               });
       };

       function updateLikedUsers(likedUsers, id) {
           var imagesContainer = document.getElementById('liked-users-images-' + id);
           imagesContainer.innerHTML = ''; // Limpiar el contenedor de imágenes

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
    </script>


    <script>
        function toggleIcons() {
            var iconos = document.querySelectorAll('.icono');
            iconos.forEach(function(icono) {
                icono.classList.toggle('mostrar');
            });
        }
    </script>

    <script>
        // Toggle del menú en mobile
        document.getElementById('memNavToggle').addEventListener('click', function () {
            document.getElementById('memNavLinks').classList.toggle('open');
        });

        // Dropdowns del navbar (alertas, usuario)
        document.querySelectorAll('[data-mem-dropdown]').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var wrapper = trigger.closest('.mem-nav-dropdown');
                var wasOpen = wrapper.classList.contains('open');
                document.querySelectorAll('.mem-nav-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
                if (!wasOpen) wrapper.classList.add('open');
            });
        });

        document.addEventListener('click', function (e) {
            document.querySelectorAll('.mem-nav-dropdown.open').forEach(function (d) {
                if (!d.contains(e.target)) d.classList.remove('open');
            });
        });

        document.getElementById('btn-logout').addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('form-logout').submit();
        });
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

    <!-- Custom scripts -->
    @vite([
        'resources/js/documents/main-docs.js',
        'resources/js/members/main-members.js',
        'resources/js/assets/profile.js',
    ]);
</body>
</html>
