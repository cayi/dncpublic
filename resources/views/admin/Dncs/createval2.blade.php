@extends('layouts.app')
@section('content')
<div class="container">
<form action="{{ url('/admin/Dncs')}}" method="post" enctype="multipart/form-data">
@csrf
@include('/admin/Dncs.formval2',['modo'=>'Crear'])
</form>
</div>
@endsection