@extends('layouts.admin')

@section('title', 'Create Post')
@section('page_title', 'Create Post')

@section('content')
    <x-official.form title="Post & Vacancy Details">
        <form method="POST" action="{{ route('admin.posts.store') }}" class="space-y-4">
            @include('admin.posts._form')
        </form>
    </x-official.form>
@endsection
