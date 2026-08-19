<?php

use Mockery\Undefined;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

//--------------------------------------------------------------------------
//-----------------------------GENERAL FUNCTIONS----------------------------
//--------------------------------------------------------------------------

if (!function_exists('getTableTheme')) {
    function getTableTheme($theme)
    {
        $tableClass = '';
        if ($theme == 1) {
            $tableClass = "table-dark";
        }

        return $tableClass;
    }
}

if (!function_exists('getBgTheme')) {
    function getBgTheme($theme)
    {
        $bgClass = '';
        if ($theme == 1) {
            $bgClass = "bg-dark text-white";
        } else {
            $bgClass = "text-primary";
        }

        return $bgClass;
    }
}

if (!function_exists('getTextTheme')) {
    function getTextTheme($theme)
    {
        $textColor = '';
        if ($theme == 1) {
            $textColor = "text-white";
        } else {
            $textColor = "text-primary";
        }

        return $textColor;
    }
}

if (!function_exists('getListMonthsOptions')) {
    function getListMonthsOptions($fieldNameRequest = null)
    {
        $months = [
            '01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre',
        ];

        $options = "<option value = ''>Seleccione...</option>";

        foreach ($months as $key => $value) {
            $selected = '';

            if ($fieldNameRequest && request($fieldNameRequest) == intval($key)) {
                $selected = 'selected';
            }

            $key = intval($key);

            $options .= "<option value=\"$key\" $selected>$value</option>";
        }

        return $options;
    }
}

if (!function_exists('getTextMonthFromNumber')) {
    function getTextMonthFromNumber($month)
    {
        $months = [
            '1' => 'Enero',
            '2' => 'Febrero',
            '3' => 'Marzo',
            '4' => 'Abril',
            '5' => 'Mayo',
            '6' => 'Junio',
            '7' => 'Julio',
            '8' => 'Agosto',
            '9' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre',
        ];

        return $months[$month];
    }
}

if (!function_exists('getAdicionalHours')) {
    function getAdicionalHours($overtimes, $hour_value)
    {
        $total = 0;
        if (count($overtimes) > 0) {
            $total = $overtimes[0]->total_over_times * $hour_value;
        }
        return $total;
    }
}

if (!function_exists('getLastDayFromDate')) {
    function getLastDayFromDate($date)
    {
        if (!$date || !strtotime($date)) {
            return null;
        }

        $dateTime = new DateTime($date);
        $dateTime->modify('last day of this month');

        return $dateTime->format('d/m/Y');
    }
}

if (!function_exists('getCurrentTextMonth')) {
    function getCurrentTextMonth($month = '')
    {
        $currentMonth = $month ?: date('F');
        $months = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre'
        ];

        if (!array_key_exists($currentMonth, $months)) {
            return null;
        }

        return strtolower($months[$currentMonth]);
    }
}

if (!function_exists('getYearOptions')) {
    function getYearOptions($fieldNameRequest = null)
    {
        $options = "<option value = ''>Seleccione...</option>";
        $currentYear = date('Y');
        $startYear = $currentYear - 4;

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $selected = ($fieldNameRequest && request($fieldNameRequest) == $year) || (!request($fieldNameRequest) && $year == $currentYear) ? 'selected' : '';
            $options .= "<option value=\"$year\" $selected>$year</option>";
        }

        return $options;
    }
}

if (!function_exists('getTextDay')) {
    function getTextDay($day = '')
    {
        $currentDay = $day ?: date('l');
        $days = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo'
        ];

        if (!array_key_exists($currentDay, $days)) {
            return null;
        }

        return $days[$currentDay];
    }
}

if (!function_exists('getCurrentYear()')) {
    function getCurrentYear()
    {
        $currentYear = date('Y');
        return $currentYear;
    }
}

if (!function_exists('dateFormatN')) {
    function dateFormatN($date)
    {
        $formattedDate = Carbon::parse($date)->format('M d, Y');
        return $formattedDate;
    }
}

if (!function_exists('formatDMY')) {
    function formatDMY($date)
    {
        $formattedDate = Carbon::parse($date)->format('d-m-Y');
        return $formattedDate;
    }
}

if (!function_exists('getStatusNameFile')) {
    function getStatusNameFile($status)
    {
        $statusNames = [
            100 => 'Cerrado',
            10 => 'Rechazado',
            15 => 'Corrección punteo',
            1 => 'Pendiente aprobación',
            2 => 'Sellado',
            3 => 'En proceso de entrega a GESI',
            11 => 'Recibido por GESI',
            12 => 'Entregado al digitador',
            4 => 'Digitado',
            13 => 'Entregado al técnico',
            5 => 'Devuelto entorno',
            14 => 'Recibido por entorno',
            6 => 'Entregado profesional',
            7 => 'Entregado facilitador'
        ];

        return $statusNames[$status] ?? 'Estado desconocido';
    }
}

if (!function_exists('formatDateInput')) {
    function formatDateInput($date)
    {
        $formattedDate = Carbon::parse($date)->format('Y-m-d');
        return $formattedDate;
    }
}

if (!function_exists('getStatusFileClass')) {
    function getStatusFileClass($status)
    {
        $classes = [
            10 => 'status-rejected',
            15 => 'status-fix-punteo',
            1  => 'status-pending',
            2  => 'status-sealed',
            3  => 'status-delivered',
            11 => 'status-received-gesi',
            12 => 'status-delivered-digitizer',
            4  => 'status-typed',
            5  => 'status-returned',
            13 => 'status-returned-process',
            6  => 'status-professional',
            7  => 'status-facilitator',
            14 => 'status-received-environment'
        ];

        return $classes[$status] ?? 'status-unknown';
    }
}

if (!function_exists('getDay')) {
    function getDay($date)
    {
        $day = Carbon::parse($date)->format('d');
        return $day;
    }
}

if (!function_exists('getMonth')) {
    function getMonth($date)
    {
        $month = Carbon::parse($date)->format('m');
        return $month;
    }
}


if (!function_exists('getYear')) {
    function getYear($date)
    {
        $year = Carbon::parse($date)->format('Y');
        return $year;
    }
}

if (!function_exists('calculateElapsedMinutes')) {
    function calculateElapsedMinutes($date)
    {
        $initDate = Carbon::parse($date);
        $elapsedMinutes = $initDate->diffInMinutes(Carbon::now());

        return $elapsedMinutes;
    }
}

if (!function_exists('sumValuesArray')) {
    function sumValuesArray($array)
    {
        $total = 0;
        foreach ($array as $value) {
            $total += $value;
        }
        return $total;
    }
}

//--------------------------------------------------------------------------
//------------------------------FUNCTIONS USERS-----------------------------
//--------------------------------------------------------------------------

if (!function_exists('getUrlImgProfile')) {
    function getUrlImgProfile($userId, $data_users)
    {
        $urlImg = 'img/undraw_profile_2.svg';
        foreach ($data_users as $data_user) {
            if ($userId == $data_user->id_user) {
                $urlImg = 'img/img_perfil/' . $userId . '/' . $data_user->url_img;
                break;
            }
        }
        return $urlImg;
    }
}

if (!function_exists('getDocumentUser')) {
    function getDocumentUser($userId, $data_users)
    {
        foreach ($data_users as $data_user) {
            if ($userId == $data_user->id_user) {
                return $data_user->document;
            }
        }
        return new Undefined;
    }
}

if (!function_exists('getInfoUser')) {
    function getInfoUser($userId, $data_users)
    {
        foreach ($data_users as $data_user) {
            if ($userId == $data_user->id_user) {
                return $data_user;
            }
        }
        return null;
    }
}

if (!function_exists('nameAndLastName')) {
    function nameAndLastName($name, $last_name)
    {
        $name = explode(' ', $name);
        $last_name = explode(' ', $last_name);
        return $name[0] . ' ' . $last_name[0];
    }
}

if (!function_exists('properNouns')) {
    function properNouns($names, $last_name = '')
    {
        $separate_names = explode(" ", $names);
        $separate_last_names = explode(" ", $last_name);

        $full_name = '';

        foreach ($separate_names as $name) {
            $full_name .= mb_convert_case(mb_strtolower($name, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') . ' ';
        }

        foreach ($separate_last_names as $lname) {
            $full_name .= mb_convert_case(mb_strtolower($lname, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') . ' ';
        }

        return trim($full_name);
    }
}

if (!function_exists('getTotalOvertime')) {
    function getTotalOvertime($overtimes)
    {
        $total_overtime = 0;
        foreach ($overtimes as $overtime) {
            $total_overtime += $overtime->hours;
        }
        return $total_overtime;
    }
}

if (!function_exists('getHoursMonth')) {
    function getHoursMonth($hours)
    {
        $total = 184;
        if (count($hours) > 0) {
            number_format($hours[0]->hours_per_month, 1);
        }

        return $total;
    }
}

if (!function_exists('splitByHyphen')) {
    function splitByHyphen($string)
    {
        $parts = explode('-', $string);
        return $parts;
    }
}

//--------------------------------------------------------------------------
//----------------------------FUNCTIONS PACKAGES----------------------------
//--------------------------------------------------------------------------

function percentageCompletePackage($quantityCount, $receptions_count)
{
    if ($quantityCount === 0) {
        return 0;
    }

    $percentage = ($quantityCount * 100) / $receptions_count;

    if ($percentage >= 100) {
        return 100;
    }

    return number_format($percentage, 1);
}

function getClassStatusPackages($package)
{
    $statusClasses = [
        0 => 'text-white',
        1 => 'text-danger',
        2 => 'text-warning',
        3 => 'text-success',
        4 => 'text-info',
        5 => 'text-primary',
        6 => 'text-dark'
    ];

    $classStatus = $statusClasses[$package->status];
    return $classStatus;
}

function setNumberPackage($last_package, $environment_id)
{
    if ($last_package == null) {
        $package_number = 1;
    } else {
        $package_number = substr($last_package, 1) + 1;
    }

    if ($package_number === '') {
        $package_number = 1;
    }

    return $environment_id . $package_number;
}

function separateNumberPackage($package)
{
    $number_package = substr($package, 1);
    $environment_id = substr($package, 0, 1);
    return $environment_id . '-' . $number_package;
}

function countPackageStatus($statusCounts, $status)
{
    $count = 0;

    foreach ($statusCounts as $statusCount) {
        if ($statusCount->status == $status) {
            $count = $statusCount->count;
            break;
        }
    }
    return $count;
}

//--------------------------------------------------------------------------
//--------------------------FUNCTIONS TRACEABILITY--------------------------
//--------------------------------------------------------------------------

function getIcontraceabilityEnv($typed_by)
{
    $iconClass = 'fa-circle-xmark text-danger';

    if ($typed_by !== null) {
        $iconClass = 'fa-circle-check text-success';
    }

    return $iconClass;
}

//--------------------------------------------------------------------------
//--------------------------FUNCTIONS CORRECTIONS---------------------------
//--------------------------------------------------------------------------

function getCorrectionsType($type_id)
{
    $types = [
        1 => 'Eliminar ficha',
        2 => 'Eliminar página',
        3 => 'Cambiar número',
        4 => 'Cambiar fecha',
    ];

    $type = $types[$type_id];
    return $type;
}

function getIconCorrection($type_id)
{
    $icons = [
        1 => 'fa-regular fa-square-minus',
        2 => 'trash-alt',
        3 => 'pencil-alt',
        4 => 'calendar-day',
    ];

    $iconClass = $icons[$type_id];
    return $iconClass;
}

//--------------------------------------------------------------------------
//-------------------------------FUNCTIONS ACTS-----------------------------
//--------------------------------------------------------------------------

if (!function_exists('getTotalFromArray')) {
    function getTotalFromArray($array)
    {
        $total = 0;
        foreach ($array as $number) {
            $total += $number;
        }
        return $total;
    }
}

if (!function_exists('getTotalFromObject')) {
    function getTotalFromObject($object)
    {
        $total = 0;
        foreach ($object as $item) {
            $total += $item->total;
        }
        return $total;
    }
}

if (!function_exists('getFileNameAct')) {
    function getFileNameAct($type, $month)
    {
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        if (!isset($months[$month])) {
            return null;
        }

        $typeConfig = [
            'reception_acts' => ['documentName' => 'Acta de entrega '],
            'report_activities' => ['documentName' => 'Informe de actividades '],
            'prorroga' => ['documentName' => 'Prórroga '],
            'planilla' => ['documentName' => 'Planilla '],
            'secop' => ['documentName' => 'SECOP ']
        ];

        if (!isset($typeConfig[$type])) {
            return null;
        }

        $documentName = $typeConfig[$type]['documentName'];

        return $documentName . $months[$month];
    }
}

if (!function_exists('getFileEnvironmentAct')) {
    function getFileEnvironmentAct($filePath, $type)
    {

        $separateFilePath = explode("/", $filePath);

        if ($type === 'reception_acts') {
            $fileNameSeparate = explode(".", $separateFilePath[count($separateFilePath) - 1]);
            $environment = $fileNameSeparate[0];
        }

        if ($type === 'report-weekly') {
            $environment = $separateFilePath[3];
        }

        return $environment;
    }
}

if (!function_exists('getEnvironmentName')) {
    function getEnvironmentName($environmentId)
    {
        $environments = [
            '1' => 'Hogar',
            '2' => 'Laboral',
            '3' => 'Educativo',
            '4' => 'Comunitario',
            '6' => 'Institucional'
        ];

        $envName = $environments[$environmentId];

        return $envName;
    }
}

if (!function_exists('getLocalidadName')) {
    function getLocalidadName($localidadId)
    {
        $localidades = [
            '1' => 'Usaquén',
            '2' => 'Chapinero',
            '8' => 'Santa Fe',
            '9' => 'San Cristóbal',
            '10' => 'Usme',
            '11' => 'Tunjuelito',
            '12' => 'Bosa',
            '13' => 'Kennedy',
            '14' => 'Fontibón',
            '3' => 'Engativá',
            '4' => 'Suba',
            '5' => 'Barrios Unidos',
            '6' => 'Teusaquillo',
            '15' => 'Mártires',
            '16' => 'Antonio Nariño',
            '7' => 'Puente Aranda',
            '17' => 'Candelaria',
            '18' => 'Rafael Uribe Uribe',
            '19' => 'Ciudad Bolívar',
            '20' => 'Sumapaz',
        ];

        return $localidades[$localidadId] ?? 'Desconocido';
    }
}

if (!function_exists('getLocalidadesName')) {
    function getLocalidadesName($localidadId)
    {
        $localidades = [
            '1' => 'Usaquén',
            '2' => 'Chapinero',
            '8' => 'Santa Fe',
            '9' => 'San Cristóbal',
            '10' => 'Usme',
            '11' => 'Tunjuelito',
            '12' => 'Bosa',
            '13' => 'Kennedy',
            '14' => 'Fontibón',
            '3' => 'Engativá',
            '4' => 'Suba',
            '5' => 'Barrios Unidos',
            '6' => 'Teusaquillo',
            '15' => 'Mártires',
            '16' => 'Antonio Nariño',
            '7' => 'Puente Aranda',
            '17' => 'Candelaria',
            '18' => 'Rafael Uribe Uribe',
            '19' => 'Ciudad Bolívar',
            '20' => 'Sumapaz',
        ];

        if (is_array($localidadId)) {
            return collect($localidadId)
                ->map(fn($id) => $localidades[$id] ?? 'Desconocido')
                ->implode(', ');
        }

        return $localidades[$localidadId] ?? 'Desconocido';
    }
}

if (!function_exists('getIconEnvironment')) {
    function getIconEnvironment($environment)
    {
        $icons = [
            'Hogar' => 'fa-solid fa-house',
            'Laboral' => 'fa-solid fa-briefcase',
            'Educativo' => 'fa-solid fa-graduation-cap',
            'Comunitario' => 'fa-solid fa-people-group',
            'Institucional' => 'fa-solid fa-building-columns'
        ];

        $icon = $icons[$environment];

        return $icon;
    }
}

if (!function_exists('getIconEnvironmentAct')) {
    function getIconEnvironmentAct($filePath, $type)
    {
        $environment = getFileEnvironmentAct($filePath, $type);
        $icon = getIconEnvironment($environment);
        return $icon;
    }
}

if (!function_exists('getIconRole')) {
    function getIconRole($role)
    {
        $icons = [
            '1' => 'keyboard',
            '2' => 'heart-pulse',
            '3' => 'stethoscope',
            '4' => 'notes-medical',
            '5' => 'apple-whole',
            '6' => 'brain',
            '7' => 'envelopes-bulk',
            '8' => 'laptop-medical',
            '9' => 'users',
            '10' => 'gear',
            '11' => 'user-tie',
            '12' => 'laptop-code',
            '13' => 'tooth',
            '14' => 'paintbrush', //Profesional en artes
            '15' => 'bullhorn', //Comunicador social
            '16' => 'volleyball', //Educador físico
            '17' => 'leaf', //Administador ambiental
            '18' => 'seedling', //Profesional en ciencias ambientales
            '19' => 'paw', //Médico veterinario
            '20' => 'flask-vial', //Ingeniero químico
            '21' => 'pen', //Pedagogo
            '22' => 'address-book', //Gestor
            '23' => 'palette' //Técnico en artes
        ];

        $icon = $icons[$role];

        return $icon;
    }
}

if (!function_exists('getLocalidadName')) {
    function getLocalidadName($id)
    {
        $localidades = [
            1 => 'Usaquén',
            2 => 'Chapinero',
            3 => 'Santa Fe',
            4 => 'San Cristóbal',
            5 => 'Usme',
            6 => 'Tunjuelito',
            7 => 'Bosa',
            8 => 'Kennedy',
            9 => 'Fontibón',
            10 => 'Engativá',
            11 => 'Suba',
            12 => 'Barrios Unidos',
            13 => 'Teusaquillo',
            14 => 'Mártires',
            15 => 'Antonio Nariño',
            16 => 'Puente Aranda',
            17 => 'Candelaria',
            18 => 'Rafael Uribe Uribe',
            19 => 'Ciudad Bolívar',
            20 => 'Sumapaz',
        ];

        return $localidades[$id] ?? 'Desconocido';
    }
}
