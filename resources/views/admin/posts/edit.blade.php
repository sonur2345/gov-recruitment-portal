@extends('layouts.admin')

@section('title', 'Edit Post')
@section('page_title', 'Edit Post')

@section('content')
    <x-official.form title="Edit Post Details">
        <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="space-y-4">
            @php($method = 'PUT')
            @include('admin.posts._form', ['post' => $post, 'method' => $method])
        </form>
    </x-official.form>
@endsection
