@extends('layouts.appadmin')
@section('content')
<div class="container">
    @if($success)
          <div class="alert alert-success alert-block">
               <button type="button" class="close" data-dismiss="alert">×</button>
               <strong>{{ $success }}</strong>
          </div>
     @endif
    <div>Administración del Sistema: </div>
    <div>{{$usuario}} </div>
    <div>{{$email}}</div> 
    <div>Verson 1.0.1, fecha 19/04/2022</div> 
</div>
@endsection
