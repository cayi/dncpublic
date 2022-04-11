@extends('layouts.appcatalogos')
@section('content')
<div class="container">
<form action="{{ url('/admin/Dncs/search') }}" 
    method="post" enctype="multipart/form-data">
    @csrf
    {{ method_field('POST')}}
    @include('include.formmensajes')
<br>
<div class="form-group">
<label  class="d-inline" for="num_emp"> Número de Empleado: </label>
<input onInput="jsemp_dep()" size="10" type="text"  class="d-inline" class="form-control" name="num_emp" id="num_emp" 
    value="{{ $dncs->num_emp }}">
<br>
<label  class="d-inline" for="dependencia"> Dependencia: </label>
<select  class="d-inline" lenght="40" class="form-control" name="dependencia" id="dependencia">
     @foreach( $dependencias as $dep)
     <option size="40" value="{{ $dep->dependencia }}" 
       <?php   
           if (isset($dncs->dep_o_ent)) {
               $dncs->dep_o_ent = trim( $dncs->dep_o_ent); }
            else {               
               $dncs->dep_o_ent = old('dep_o_ent');
            }
            if( $dep->dependencia == $dncs->dep_o_ent) 
                echo 'selected="selected"'
        ?>                     
       > 
       {{ $dep->dependencia }}
     </option>
     @endforeach
</select>
<br>
<br>
<input type="submit" class="btn btn-success" value="Buscar">
<br>
</form>
</div>
@endsection