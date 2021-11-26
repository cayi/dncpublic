<h1> {{$modo}} Formato DNC</h1>
@if(count($errors)>0)
    <div class="alert alert-danger" role="alert">
        <ul>
            @foreach($errors->all() as $error)
                <li> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
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
<label class="d-inline" for="area"> Area:</label>
<input size="60" type="text" class="d-inline" class="form-control" name="area" id="area" 
    value="{{ $dncs->area }}">
<br>
<label class="d-inline" for="grado_est"> Grado de Estudios: </label>
<input size="60" type="text" class="d-inline" class="form-control" name="grado_est" id="grado_est" 
    value="{{ $dncs->grado_est }}">
<br>
<label class="d-inline" for="correo"> Correo ELectrónico: </label>
<input size="40" type="text" class="d-inline" class="form-control" name="correo" id="correo" 
    value="{{ $dncs->correo }}">
<br>
<label class="d-inline" for="telefono"> Teléfono </label>
<input size="15" type="text" class="d-inline" class="form-control" name="telefono" id="telefono" 
    value="{{ $dncs->telefono }}">
<br>
<label class="d-inline" for="funciones"> Funciones: </label>
<div>
    <textarea class="d-inline" name="funciones" id="funciones" cols="80" rows="3">
        {{ $dncs->funciones }}
    </textarea>
</div>
<br>
<label class="d-inline" for="word_int"> Word Intermedio: </label>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="word_int_tablas" id="word_int_tablas" value="1" >
    Tablas en Word.
</label>
<br>
<label class="d-inline" for="word_ava"> Word Avanzado: </label>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="word_ava_correspondencia" id="word_ava_correspondencia" value="1" >
    Combinación de Correspondencia.
</label>
<br>
<label class="d-inline" for="excel_int"> Excel Intermedio: </label>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="excel_int_graficos" id="excel_int_graficos" value="1" >
    Gráficos en Excel.
</label>
<br>
<?php for ($i = 1; $i <= 30; $i++) {     echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">    
    <input type="checkbox" class="form-check-input "
    name="excel_int_formulas" id="excel_int_formulas" value="1" >
    Formulas Básicas en Excel.
</label>
<br>
<label class="d-inline" for="excel_ava"> Excel Avanzado: </label>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="excel_ava_herramientas" id="excel_ava_herramientas" value="1" >
    Herramientas de visualización de datos.
</label>
<br>
<?php for ($i = 1; $i <= 29; $i++) {     echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="excel_ava_herramientas" id="excel_ava_herramientas" value="1" >
    Funciones y herramientas avanzadas.
</label>
<br>
<label class="d-inline" for="power_point"> Power Point: </label>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="power_point_cualidades" id="power_point_cualidades" value="1" >
    La cualidades de la Presentaciones.
</label>
<br>
<label class="d-inline" ffor="nuevas_tec"> Nuevas Tecnologías: </label>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="nuevas_tec_competencia" id="nuevas_tec_competencia" value="1" >
    Competencia comunicativa a través de la competencia digital.
</label>
<br>
<?php for ($i = 1; $i <= 36; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="nuevas_tec_zoom" id="nuevas_tec_zoom" value="1" >
    Nuevas tecnologías (zoom).
</label>
<br>
<label for="acc_institucionales"> ACCIONES INSTITUCIONALES: </label>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_etica" id="acc_institucionales_etica" value="1" >
    Ética e Integridad en el Servicio Público.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_valores" id="acc_institucionales_valores" value="1" >
    Valores Gubernamentales.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_responsabilidades" id="acc_institucionales_responsabilidades" value="1" >
    Responsabilidades Administrativas de las personas servidoras públicas.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_metodologia" id="acc_institucionales_metodologia" value="1" >
    Metodología de las 5´s.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_identificacion" id="acc_institucionales_identificacion" value="1" >
    Identificación Institucional.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_violencia" id="acc_institucionales_violencia" value="1" >
    Violencia ver, conocer y reconocer.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_seguridad" id="acc_institucionales_seguridad" value="1" >
    Seguridad con perspectiva de género.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_norma" id="acc_institucionales_norma" value="1" >
    Norma 025.Igualdad laboral.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_induccion" id="acc_institucionales_induccion" value="1" >
    Inducción al Servicio Público.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_actualizacion" id="acc_institucionales_actualizacion" value="1" >
    Actualización en Gestión archivística, un camino hacia la transparencia y rendición de cuentas.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_documentos" id="acc_institucionales_documentos" value="1" >
    Documentos Administrativos.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_politicas" id="acc_institucionales_politicas" value="1" >
    Políticas públicas y el Ciudadano.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_correcto" id="acc_institucionales_correcto" value="1" >
    Correcto manejo de Información.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_protocolos" id="acc_institucionales_protocolos" value="1" >
    Protocolos de Atención y Servicio.
</label>
<br>
<?php for ($i = 1; $i <= 56; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_institucionales_vocacion" id="acc_institucionales_vocacion" value="1" >
    Vocación de Servicio.
</label>
<br>
<label for="acc_des_humano">ACCIONES DE DESARROLLO HUMANO:</label>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_des_humano_solucion" id="acc_des_humano_solucion" value="1" >
    Solución de conflictos.
</label>
<br>
<?php for ($i = 1; $i <= 72; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_des_humano_como" id="acc_des_humano_como" value="1" >
    Como afrontar las dificultades laborales.
</label>
<br>
<?php for ($i = 1; $i <= 72; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_des_humano_comunicacion" id="acc_des_humano_comunicacion" value="1" >
    Comunicación consciente.
</label>
<br>
<?php for ($i = 1; $i <= 72; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_des_humano_importancia" id="acc_des_humano_importancia" value="1" >
    La importancia de aceptarse a sí mismos.
</label>
<br>
<?php for ($i = 1; $i <= 72; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_des_humano_inteligencia" id="acc_des_humano_inteligencia" value="1" >
    Inteligencia emocional.
</label>
<br>
<label for="acc_administrativas">ACCIONES ADMINISTRATIVAS:</label>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_administrativas_actualizacion" id="acc_administrativas_actualizacion" value="1" >
    Actualización de procedimientos, Mejora Continua.    
</label>
<br>
<?php for ($i = 1; $i <= 57; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_administrativas_cumplimiento" id="acc_administrativas_cumplimiento" value="1" >
    Cumplimiento de objetivos y metas Institucionales.
</label>
<br>
<?php for ($i = 1; $i <= 57; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_administrativas_administracion" id="acc_administrativas_administracion" value="1" >
    Administración efectiva del tiempo.
</label>
<br>
<?php for ($i = 1; $i <= 57; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_administrativas_clima" id="acc_administrativas_clima" value="1" >
    Clima laboral.
</label>
<br>
<?php for ($i = 1; $i <= 57; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_administrativas_modernizacion" id="acc_administrativas_modernizacion" value="1" >
    Modernización administrativa y diseño organizacional.
</label>
<br>
<?php for ($i = 1; $i <= 57; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_administrativas_recursos" id="acc_administrativas_recursos" value="1" >
    Administración de recursos humanos.
</label>
<br>
<?php for ($i = 1; $i <= 57; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_administrativas_materiales" id="acc_administrativas_materiales" value="1" >
    Administración de recursos materiales.
</label>
<br>
<?php for ($i = 1; $i <= 57; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_administrativas_sistema" id="acc_administrativas_sistema" value="1" >
    Sistema de calidad.
</label>
<br>
<?php for ($i = 1; $i <= 57; $i++) { echo '&nbsp'; } ?>
<label class="form-check-inline  fitem  ">
    <input type="checkbox" class="form-check-input "
    name="acc_administrativas_otro" id="acc_administrativas_otro" value="1" >
    Otro.
</label>
<br>
<label for="otro">En caso de otro, favor de describir cual:</label>
<input size="80" type="text"  class="d-inline" class="form-control"
    name="otro_curso" id="otro_curso"
    value="{{ $dncs->otro_curso }}">
<br>
<label for="interes_instructor"> ¿Te interesaría participar como instructor interno en CECAP? </label>
<select  class="d-inline" lenght="2" name="interes_instructor" id="interes_instructor">
     <option>No</option>
     <option>Si</option>
 </select>
<br>
<label for="tema"> Si Tu Respuesta fue Si, ¿Impartiendo que tema? :</label>
<input class="d-inline" size="70" type="text" class="form-control" name="tema" id="tema"
    value="{{ $dncs->tema }}">
<br>
<label for="activo"> Activo </label>
<input onInput="jsactiva();" type="checkbox" id="activa" name="activa" 
    value="{{ $dncs->activo }}"
<?php
    if ($dncs->activo) echo " checked "
?>
> 
<br>
<input type="submit" class="btn btn-success" value="{{$modo}} Datos">
<a href="{{ url('/admin/Dncs') }}" class="btn btn-primary"  > Regresar </a>
<br>
<input type="hidden" id="activao" name= "activao" value="{{ $dncs->activo }}">
</div>
@include('include.jsactiva')