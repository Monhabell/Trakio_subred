<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; padding: 0; width: 100% !important; }
        .container { max-width: 600px; margin: 0 auto; }
    </style>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #ffffff; padding: 40px 0; background-color: #0d0d0d;">

    <table class="container" align="center" border="0" cellpadding="0" cellspacing="0" width="600"
        style="background-color: #1a1a1a; border-radius: 12px; overflow: hidden; border: 1px solid #333333;">

        {{-- CABECERA --}}
        <tr>
            <td style="background-color: #1a1a1a; padding: 36px 20px; text-align: center; border-bottom: 4px solid #e63946;">
                <h1 style="margin: 0; color: #ffffff; font-size: 26px; letter-spacing: 2px; text-transform: uppercase;">
                    Trakio<span style="color: #e63946;">.pro</span>
                </h1>
                <p style="color: #888888; font-size: 12px; margin-top: 8px; text-transform: uppercase; letter-spacing: 1px;">
                    Recordatorio · Entrega de Bases GESI
                </p>
            </td>
        </tr>

        {{-- CUERPO --}}
        <tr>
            <td style="padding: 36px 30px;">

                <p style="font-size: 17px; color: #ffffff; margin: 0 0 6px;">
                    Hola, <strong>{{ $userName }}</strong>
                </p>

                @if ($isGesiAdmin)
                    {{-- ══════════════════════════════════════════════════
                         ADMIN GESI — ellos reciben las entregas, no las hacen
                         ══════════════════════════════════════════════════ --}}

                    <p style="font-size: 14px; color: #cccccc; line-height: 1.7; margin: 0 0 24px;">
                        Se acerca el <strong style="color: #ffffff;">4.º día hábil del mes de
                        {{ \Carbon\Carbon::now()->addMonth()->translatedFormat('F') }}</strong>,
                        fecha en que GESI realiza la entrega oficial de todas las bases del mes de
                        <strong style="color: #ffffff;">{{ $currentMonthName }}</strong> con calidad,
                        antes de media noche.
                    </p>

                    {{-- Caja azul: fecha límite de recepción --}}
                    <table border="0" cellpadding="0" cellspacing="0" width="100%"
                        style="margin: 0 0 28px; background-color: #0f1a2b; border-left: 4px solid #388bfd; border-radius: 4px;">
                        <tr>
                            <td style="padding: 18px 20px;">
                                <p style="margin: 0 0 6px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888;">
                                    Fecha límite para recibir todo de los entornos
                                </p>
                                <p style="margin: 0; font-size: 24px; font-weight: 700; color: #388bfd; letter-spacing: 1px;">
                                    {{ $deadlineDate }}
                                </p>
                                <p style="margin: 6px 0 0; font-size: 13px; color: #cccccc;">
                                    Para que GESI pueda procesar y entregar todo con calidad antes del
                                    <strong style="color:#fff;">4.º día hábil</strong>, los entornos deben
                                    tener todo entregado a más tardar en esta fecha.
                                </p>
                            </td>
                        </tr>
                    </table>

                    @if ($totalPendingCount > 0)
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                            style="margin: 0 0 24px; background-color: #2b0f0f; border-left: 4px solid #e63946; border-radius: 4px;">
                            <tr>
                                <td style="padding: 16px 20px;">
                                    <p style="margin: 0 0 4px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888;">
                                        Fichas de {{ $currentMonthName }} aún sin entregar a GESI
                                    </p>
                                    <p style="margin: 0; font-size: 28px; font-weight: 700; color: #e63946;">
                                        {{ $totalPendingCount }}
                                    </p>
                                    <p style="margin: 6px 0 0; font-size: 13px; color: #cccccc;">
                                        Fichas con <strong style="color:#fff;">fecha de intervención del mes de {{ $currentMonthName }}</strong>
                                        en estado <em>Pendiente aprobación</em> (1) o <em>Sellado</em> (2)
                                        que aún no han sido entregadas a GESI.
                                        Se ha notificado individualmente a cada profesional con fichas en retraso mayor a 5 días.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    @endif

                    <p style="font-size: 13px; color: #888; line-height: 1.6; margin: 0;">
                        Este recordatorio fue enviado automáticamente a todos los usuarios activos
                        (excepto digitadores). Los profesionales con fichas en retraso recibieron
                        el listado de sus fichas pendientes.
                    </p>

                @else
                    {{-- ══════════════════════════════════════════════════
                         USUARIOS DE ENTORNO — profesionales y coordinadores
                         ══════════════════════════════════════════════════ --}}

                    @if ($isEnvironmentAdmin)
                        <p style="font-size: 14px; color: #cccccc; line-height: 1.7; margin: 0 0 24px;">
                            Te recordamos que el <strong style="color: #ffffff;">4.º día hábil de cada mes</strong>
                            es la fecha en que GESI realiza la entrega oficial de todas las bases del mes anterior
                            con calidad, antes de media noche. Para lograrlo,
                            <strong style="color: #ffffff;">todo lo del mes de {{ $currentMonthName }}</strong>
                            debe estar completamente registrado y en manos de GESI antes de esa fecha.
                        </p>

                        {{-- Caja roja: fecha límite (coordinadores) --}}
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                            style="margin: 0 0 28px; background-color: #2b0f0f; border-left: 4px solid #e63946; border-radius: 4px;">
                            <tr>
                                <td style="padding: 18px 20px;">
                                    <p style="margin: 0 0 6px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888;">
                                        Fecha límite de entrega
                                    </p>
                                    <p style="margin: 0; font-size: 24px; font-weight: 700; color: #e63946; letter-spacing: 1px;">
                                        {{ $deadlineDate }}
                                    </p>
                                    <p style="margin: 6px 0 0; font-size: 13px; color: #cccccc;">
                                        Todo lo del mes de <strong>{{ $currentMonthName }}</strong> debe estar
                                        entregado a GESI a más tardar en esta fecha.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    @else
                        <p style="font-size: 14px; color: #cccccc; line-height: 1.7; margin: 0 0 24px;">
                            Te recordamos que el <strong style="color: #ffffff;">último día de cada mes</strong>
                            es la fecha límite para tener completamente registrado y entregado todo lo de
                            <strong style="color: #ffffff;">{{ $currentMonthName }}</strong>.
                        </p>

                        {{-- Caja roja: fecha límite (profesionales) --}}
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                            style="margin: 0 0 28px; background-color: #2b0f0f; border-left: 4px solid #e63946; border-radius: 4px;">
                            <tr>
                                <td style="padding: 18px 20px;">
                                    <p style="margin: 0 0 6px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888;">
                                        Fecha límite de entrega
                                    </p>
                                    <p style="margin: 0; font-size: 24px; font-weight: 700; color: #e63946; letter-spacing: 1px;">
                                        {{ $professionalDeadlineDate }}
                                    </p>
                                    <p style="margin: 6px 0 0; font-size: 13px; color: #cccccc;">
                                        Todo lo del mes de <strong>{{ $currentMonthName }}</strong> debe estar
                                        registrado y entregado a más tardar en esta fecha.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    @endif

                    {{-- Aviso adicional para coordinadores de entorno --}}
                    @if ($isEnvironmentAdmin)
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                            style="margin: 0 0 28px; background-color: #0f1a2b; border-left: 4px solid #388bfd; border-radius: 4px;">
                            <tr>
                                <td style="padding: 16px 20px;">
                                    <p style="margin: 0 0 6px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888;">
                                        Aviso para coordinadores de entorno
                                    </p>
                                    <p style="margin: 0; font-size: 14px; color: #cccccc; line-height: 1.6;">
                                        Como coordinador(a) de su entorno, asegúrese de que todos los
                                        profesionales a su cargo tengan sus registros completos y entregados
                                        a GESI antes del <strong style="color: #388bfd;">{{ $deadlineDate }}</strong>.
                                        Recuerde que el <strong style="color:#fff;">4.º día hábil</strong>
                                        GESI realiza la entrega definitiva con calidad, antes de media noche.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    @endif

                    {{-- Fichas con retraso del usuario --}}
                    @if (count($delayedFichas) > 0)
                        @php $showProfCol = $isEnvironmentAdmin && !empty($delayedFichas[0]['professional']); @endphp

                        <p style="font-size: 14px; font-weight: 600; color: #ffffff; margin: 0 0 10px;">
                            <span style="color: #e63946;">&#9888;</span>
                            @if ($isEnvironmentAdmin)
                                Fichas de sus profesionales — {{ $currentMonthName }} pendientes por entregar a GESI
                            @else
                                Fichas del mes de {{ $currentMonthName }} pendientes por entregar a GESI
                            @endif
                        </p>

                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                            style="border-collapse: collapse; font-size: 12px; margin-bottom: 24px;">
                            <thead>
                                <tr style="background-color: #262626;">
                                    @if ($showProfCol)
                                        <th style="padding: 10px 12px; text-align: left; color: #e63946; border-bottom: 1px solid #333; font-weight: 600;">Profesional</th>
                                    @endif
                                    <th style="padding: 10px 12px; text-align: left; color: #e63946; border-bottom: 1px solid #333; font-weight: 600;">Ficha</th>
                                    <th style="padding: 10px 12px; text-align: left; color: #e63946; border-bottom: 1px solid #333; font-weight: 600;">Formato</th>
                                    <th style="padding: 10px 12px; text-align: left; color: #e63946; border-bottom: 1px solid #333; font-weight: 600;">F. Intervención</th>
                                    <th style="padding: 10px 12px; text-align: center; color: #e63946; border-bottom: 1px solid #333; font-weight: 600;">Retraso</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($delayedFichas as $ficha)
                                    <tr style="background-color: {{ $loop->even ? '#1e1e1e' : '#222' }};">
                                        @if ($showProfCol)
                                            <td style="padding: 9px 12px; border-bottom: 1px solid #2a2a2a; color: #aaa;">{{ $ficha['professional'] }}</td>
                                        @endif
                                        <td style="padding: 9px 12px; border-bottom: 1px solid #2a2a2a; color: #fff; font-weight: 600;">#{{ $ficha['file_number'] }}</td>
                                        <td style="padding: 9px 12px; border-bottom: 1px solid #2a2a2a; color: #aaa;">{{ $ficha['format'] }}</td>
                                        <td style="padding: 9px 12px; border-bottom: 1px solid #2a2a2a; color: #aaa;">{{ $ficha['fecha_intervencion'] }}</td>
                                        <td style="padding: 9px 12px; border-bottom: 1px solid #2a2a2a; text-align: center;">
                                            <span style="display:inline-block; padding: 2px 10px; background-color: #e63946; border-radius: 20px; color: #fff; font-weight: 700; font-size: 11px;">
                                                {{ $ficha['days_delay'] }} días
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <p style="font-size: 13px; color: #888; line-height: 1.6; margin: 0 0 20px;">
                            Estas fichas tienen fecha de intervención del mes de
                            <strong style="color:#fff;">{{ $currentMonthName }}</strong>
                            y aún no han sido entregadas a GESI (estado <strong style="color:#fff;">Pendiente aprobación</strong>).
                            Por favor regularice su situación antes del
                            <strong style="color: #fff;">{{ $isEnvironmentAdmin ? $deadlineDate : $professionalDeadlineDate }}</strong>.
                        </p>
                    @endif

                    <p style="font-size: 13px; color: #888; line-height: 1.6; margin: 0;">
                        Si ya realizó la entrega o tiene algún inconveniente, comuníquese con
                        el equipo GESI a través de los canales habituales.
                    </p>

                @endif

            </td>
        </tr>

        {{-- PIE --}}
        <tr>
            <td style="background-color: #000000; padding: 24px 30px; text-align: center; border-top: 1px solid #333333;">
                <p style="margin: 0; font-size: 11px; color: #555;">
                    Este es un mensaje automático generado por la plataforma Trakio.pro.<br>
                    © {{ date('Y') }} Todos los derechos reservados.
                </p>
            </td>
        </tr>

    </table>
</body>
</html>
