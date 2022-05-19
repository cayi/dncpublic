<h3> Favor de confirmar si sus datos son correctos</h3>
@include('include.formerrors')
<div class="form-group">
<input type="hidden" id="fk_id_plantillas"  name= "fk_id_plantillas"    value="{{ $dncs->fk_id_plantillas }}">
<input type="hidden" id="fk_cve_periodo"    name= "fk_cve_periodo" value="{{ $dncs->fk_cve_periodo }}">
<input type="hidden" id="num_emp"           name= "num_emp" value="{{ $dncs->num_emp }}">
<input type="hidden" id="nombre_completo"   name= "nombre_completo" value="{{ $dncs->nombre_completo }}">
<input type="hidden" id="dep_o_ent"         name= "dep_o_ent" value="{{ $dncs->dep_o_ent }}">
<input type="hidden" id="unidad_admva"      name= "unidad_admva" value="{{ $dncs->unidad_admva }}">
<input type="hidden" id="confirmar"         name= "confirmar" value="1">

<label  class="d-inline" for="num_emp"> Número de Empleado: {{ $dncs->num_emp }}</label>
<br>
<label  class="d-inline" for="nombre_completo"> 
    Nombre del Empleado: 
    {{ $dncs->nombre_completo }} </label>
<br>
<label class="d-inline" for="dep_o_ent"> Dependencia o Entidad: {{ $dncs->dep_o_ent }} </label>
<br>
<label class="d-inline" for="unidad_admva"> Unidad Administrativa: {{ $dncs->unidad_admva }}</label>
<br>
<input type="submit" class="btn btn-success" value="Confirmar">&nbsp&nbsp
<a href="{{ url('/admin/search') }}" class="btn btn-primary"  > Regresar </a>
</div>
@include('include.jsactiva')