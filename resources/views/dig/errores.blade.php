@extends('layouts.dig.navigation')

@section('main')
    <style>
        .nowrap-text {
            white-space: nowrap;
        }

        .fixed-width {
            width: 200px;
            /* Puedes ajustar este valor según tus necesidades */
        }
    </style>
    <section hidden>

        <style>
            .construction-container {
                text-align: center;
            }

            .construction-message {
                font-size: 2em;
                margin-bottom: 20px;
                color: #333;
            }

            .construction-icon {
                font-size: 5em;
                color: #f39c12;
            }

            .footer-message {
                margin-top: 20px;
                font-size: 1em;
                color: #666;
            }

            .table-wrapper {
                overflow-x: auto;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {

                padding: 8px;
                text-align: left;
            }
        </style>


        <div class="construction-container">
            <div class="construction-icon">🚧</div>
            <div class="construction-message">Esta página está en construcción</div>
            <div class="footer-message">Vuelve pronto para más actualizaciones</div>
        </div>

    </section>

    <div class="card_gesi  m-4 table-wrapper">
        @foreach ($sections as $section)
            <h2>{{ implode(' ', $section['title']) }}</h2>
            <table class="table table-bordered">
                <thead>
                    <tr class="table-success">
                        @foreach ($section['headers'] as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($section['data'] as $rowIndex => $row)
                        <tr>
                            @foreach ($row as $colIndex => $cell)
                                @php
                                    $cellCoordinate =
                                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                                            $colIndex + 1,
                                        ) .
                                        ($rowIndex + 1);
                                    $style =
                                        isset($styles[$cellCoordinate]) && $styles[$cellCoordinate] === 'FFFF0000'
                                            ? 'background-color: red;'
                                            : '';
                                @endphp
                                <td style="{{ $style }}">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
@endsection
