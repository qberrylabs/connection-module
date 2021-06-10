@extends('layouts.app')

@section('content')
<div class="container">
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif

    @if ($message = Session::get('failed'))
    <div class="alert alert-danger">
    <p>{{ $message }}</p>
    </div>
    @endif
    @include('connectionmodule::connection.create')
    <div class="row">
        <!-- Left Panel  -->
        @include('include.left_panel')
        <!-- Left Panel End -->

        <!-- Middle Panel -->
        <div class="col-lg-9">
            @include('connectionmodule::connection.list_connections')

            {{-- @if (Request::is('connections/pending'))
                <!-- Recent Activity-->
                @include('connection.list_connections')
                <!-- Recent Activity End -->
            @endif

            @if (Request::is('connections/all'))

                @include('connection.all_connections')

            @endif --}}

        </div>
        <!-- Middle Panel End -->
    </div>
</div>
@endsection
