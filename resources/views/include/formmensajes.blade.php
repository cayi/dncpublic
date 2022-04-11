@if(Session::has('mensaje'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{Session::get('mensaje')}}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>    
    @if(Session::has('num_emp'))
        <?php         
            $dncs->num_emp = Session::get('num_emp');        
        ?>
    @endif
    @if(Session::has('dep_o_ent'))
        <?php
            $dncs->dep_o_ent = Session::get('dep_o_ent');
        ?>
    @endif  
@endif
