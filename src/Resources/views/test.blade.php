@extends('adminlte.layouts.sdr')

@section('page_title', session('saas_title').' '.__('backup.backup_and_data_sync'))

@section('body_class', 'login-page')

@section('css')


    <link rel="stylesheet" href="{{ asset('resources/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">


    <link rel="stylesheet" href="{{ asset('resources/iCheck/all.css')}}"/>

@endsection


@section('content')
    @if(session('error'))
        <div class="box-body">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa-solid fa-ban"></i>{{session('error')}}</p>
            </div>
        </div>
    @endif
    @if(session('success'))
        <div class="box-body">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa-solid fa-check"></i>{{session('success')}}</p>
            </div>
        </div>
    @endif
    @if(session('warning'))
        <div class="box-body">
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa-solid fa-warning"></i>{{session('warning')}}</p>
            </div>
        </div>
    @endif

   {{--  @if( $errors->any() and empty(session('modalErrors')) )
        <div class="box-body">

            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                @foreach( $errors->all() as $error )
                    <p><i class="icon fa-solid fa-ban"></i>{{$error}}</p>
                @endforeach
            </div>
        </div>

    @endif
 --}}

    <section class="content-header">
        <h1>
            Sample controller
        </h1>
    </section>

    <section class="content">


    <div class="row">
        <x-maintenance::sample/>

    </div>


    </section>





@endsection


@section('script')

@endsection
