@extends('layouts.adm.navigation')

@section('main')
<div class="align-items-center justify-content-between mb-4">
    <h1>Editar formato</h1>
</div>

<div class="bg-dark card-body py-3 rounded-3">
    <form action="{{ route('formats.update', $format->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('adm.formats._form')
    </form>
</div>
@endsection