<h1> {{$modo}} Formato DNC</h1>
@include('include.formerrors')
<br>
<div class="form-group">
<label  class="d-inline" for="fk_cve_periodo"> Periodo: </label>
<select  class="d-inline" lenght="40" class="form-control" name="fk_cve_periodo" id="fk_cve_periodo">
     @foreach( $periodos as $periodo)
     <option size="40" value="{{ $periodo->cve_periodo }}" 
       <?php   
           if (isset($dncs->fk_cve_periodo)) {
               $dncs->fk_cve_periodo = trim( $dncs->fk_cve_periodo); }
            else {               
               $dncs->fk_cve_periodo = old('cve_periodo');
            }                
            if( $periodo->cve_periodo == $dncs->fk_cve_periodo) 
                echo 'selected="selected"'
        ?>                     
       > 
       {{ $periodo->descripcion }}
     </option>
     @endforeach
</select>
<br>
<label  class="d-inline" for="num_emp"> Número de Empleado: </label>
<input size="10" type="text"  class="d-inline" class="form-control" name="num_emp" id="num_emp" 
    value="{{ $dncs->num_emp }}">
<br>
<label  class="d-inline" for="nombre_completo"> Nombre Completo del Empleado (primero los apellidos): </label>
<input size="40" type="text"  class="d-inline" class="form-control"
    name="nombre_completo" id="nombre_completo" 
    value="{{ $dncs->nombre_completo }}">
<br>
<label class="d-inline" for="dep_o_ent"> Dependencia o Entidad: </label>
<input size="60" type="text" class="d-inline" class="form-control" name="dep_o_ent" id="dep_o_ent" 
    value="{{ $dncs->dep_o_ent }}">
<br>
<label class="d-inline" for="unidad_admva"> Unidad Administrativa: </label>
<input size="60" type="text" class="d-inline" class="form-control" 
    name="unidad_admva" id="unidad_admva" 
    value="{{ $dncs->unidad_admva }}">
@include('include.dnc_cursos')
<label for="activo"> Activo </label>
<input onInput="jsactiva();" type="checkbox" id="activa" name="activa" 
    value="{{ $dncs->activo }}"
    <?php
        if ($dncs->activo)   echo " checked " ;
    ?>
>
<br>
@include('include.grabarbtn')
<a href="{{ url('/admin/Dncs') }}" class="btn btn-primary"  > Regresar </a>
<br>
<input type="hidden" id="activao" name= "activao" value="{{ $dncs->activo }}">
<input type="hidden" id="fk_id_plantillas" name= "fk_id_plantillas" value="{{ $dncs->fk_id_plantillas }}">
</div>
@include('include.jsactiva')