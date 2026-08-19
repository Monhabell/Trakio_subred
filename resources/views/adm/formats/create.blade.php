@extends('layouts.adm.navigation')

@section('main')
<div class="align-items-center justify-content-between mb-4">
    <h1>Agregar formato</h1>
</div>

<div class="bg-dark card-body rounded-3 py-3">
    <form action="{{ route('formats.add') }}" method="POST">
        @csrf
        @include('adm.formats._form')
    </form>
</div>
@endsection