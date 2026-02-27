@extends('layouts.admin')

@section('title', 'Edit Advertisement')
@section('page_title', 'Edit Advertisement')

@section('content')
    <x-official.form title="Edit Advertisement Details">
        <form action="{{ route('admin.notifications.update', $notification) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @php($method = 'PUT')
            @include('admin.notifications._form', ['notification' => $notification, 'method' => $method])
        </form>
    </x-official.form>
@endsection
