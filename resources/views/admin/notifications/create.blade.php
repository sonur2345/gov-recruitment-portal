@extends('layouts.admin')

@section('title', 'Create Advertisement')
@section('page_title', 'Create Advertisement')

@section('content')
    <x-official.form title="Advertisement Details">
        <form action="{{ route('admin.notifications.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @include('admin.notifications._form')
        </form>
    </x-official.form>
@endsection
