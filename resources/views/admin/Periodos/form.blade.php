<h1> {{$modo}} Periodo</h1>
@include('include.formerrors')
<br>
<div class="form-group">
<label for="periodo"> Periodo </label>
<input type="text" class="form-control" name="cve_periodo" id="cve_periodo" 
    value="{{ $periodo->cve_periodo }}">
<br>
<input type="text" class="form-control" name="descripcion" id="descripcion" 
    value="{{ $periodo->descripcion }}">
<br>
<input type="date" class="form-control" name="fecha_ini" id="fecha_ini" 
    value="{{ \Carbon\Carbon::createFromDate($periodo->fecha_ini)->format('Y-m-d') }}">
<br>
<input type="date" class="form-control" name="fecha_fin" id="fecha_fin" 
    value="{{ \Carbon\Carbon::createFromDate($periodo->fecha_fin)->format('Y-m-d') }}">
<br>
<label for="activo"> Activo </label>
<input onInput="jsactiva();" type="checkbox" id="activa" name="activa" value="{{ $periodo->activo }}"
<?php
    if ($periodo->activo) echo " checked "
?>
> 
<br>
@include('include.grabarbtn')
<a href="{{ url('/admin/Periodos') }}" class="btn btn-primary"  > Regresar </a>
<br>
<input type="hidden" id="activao" name= "activao" value="{{ $periodo->activo }}">
</div>
@include('include.jsactiva')