@if(isset(Auth::user()->name ))
                 <!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Inicio</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('users.index') }}" class="nav-link {{ Request::is('users*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user"></i>
        <p>Usuarios </p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('tipo_cambios_globals.index') }}" class="nav-link {{ Request::is('tipo_cambios_globals*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-dollar-sign"></i>
        <p>Tipo Cambios Globales</p>
    </a>
</li>
@if(Auth::user()->getContainestudios())
<li class="nav-item">
    <a href="{{ route('companies.index') }}" class="nav-link {{ Request::is('companies*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-building"></i>
        <p>Compañías</p>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('excelscompanies.index') }}" class="nav-link {{ Request::is('excelscompanies*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-upload"></i>
        <p>Carga de Archivos</p>
    </a>
</li>

<li class="nav-item menu-is-opening ">
    <a href="#" class="nav-link">
            <i class="nav-icon fas  fa-plus"></i>
            <p>
            SETUPS
            <i class="right fas fa-angle-down"></i>
        </p>
    </a>
    <ul class="nav nav-treeview" style="display: none;">
    <li class="nav-item">
    <a href="{{ route('sucursales.index') }}" class="nav-link {{ Request::is('sucursales*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-building"></i>
        <p>Sucursales</p>
    </a>
        </li>


        <li class="nav-item">
            <a href="{{ route('tipo_cambios.index') }}" class="nav-link {{ (Request::is('tipo_cambios')||Request::is('tipo_cambios/*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-hand-holding-usd"></i>
                <p>Tipo Cambios</p>
            </a>
        </li>

            <li class="nav-item">
            <a href="{{ route('setup_analises.index') }}" class="nav-link {{ Request::is('setup_analises*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Analisis</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('categorizacion_cts_balances.index') }}" class="nav-link {{ Request::is('categorizacion_cts_balances*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-book"></i>
                <p>Categorización Balance</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('clasificacion_cuenta_resuls.index') }}" class="nav-link {{ Request::is('clasificacion_cuenta_resuls*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-book"></i>
                <p>Categorización Resultado</p>
            </a>
        </li>
    </ul>
    
</li>
<li class="nav-item menu-is-opening ">
    <a href="#" class="nav-link">
            <i class="nav-icon fas  fa-plus"></i>
            <p>
            Datos
            <i class="right fas fa-angle-down"></i>
        </p>
    </a>
    <ul class="nav nav-treeview" style="display: none;">
    
<li class="nav-item">
    <a href="{{ route('in_balances.index') }}" class="nav-link {{ Request::is('in_balances*') ? 'active' : '' }}">
        <i class="nav-icon 	fas fa-file-upload"></i>
        <p>In Balances</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('in_presupuestos.index') }}" class="nav-link {{ Request::is('in_presupuestos*') ? 'active' : '' }}">
        <i class="nav-icon 	fas fa-file-upload"></i>
        <p>In Presupuestos</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('in_resultados.index') }}" class="nav-link {{ Request::is('in_resultados*') ? 'active' : '' }}">
        <i class="nav-icon 	fas fa-file-upload"></i>
        <p>In Resultados</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('in_ventas.index') }}" class="nav-link {{ Request::is('in_ventas*') ? 'active' : '' }}">
        <i class="nav-icon 	fas fa-file-upload"></i>
        <p>In Ventas</p>
    </a>
</li>

    </ul>
    
</li>
<li class="nav-item menu-is-opening ">
    <a href="#" class="nav-link">
            <i class="nav-icon fas  fa-plus"></i>
            <p>
            Red Comercial
            <i class="right fas fa-angle-down"></i>
        </p>
    </a>

    <ul class="nav nav-treeview" style="display: none;">
    <li class="nav-item">
    <a href="{{ route('grupo_economicos.index') }}" class="nav-link  {{ (Request::is('grupo_economicos*') && !Request::is('grupo_economicos_empresas*'))? 'active' : '' }} ">
        <i class="nav-icon fas fa-home"></i>
        <p>Red Comercial</p>
    </a>
</li>


<li class="nav-item">
    <a href="{{ route('grupo_economicos_empresas.index') }}" class="nav-link {{ Request::is('grupo_economicos_empresas*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Red Comercial y Empresas</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('usuario_grupoeconomicos.index') }}" class="nav-link {{ Request::is('usuario_grupoeconomicos*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Usuario y Red Comercial</p>
    </a>
</li>
    </ul>

</li>


@endif

@if(Auth::user()->level_user===0)
<li class="nav-item menu-is-opening ">
    <a href="#" class="nav-link">
            <i class="nav-icon fas  fa-plus"></i>
            <p>
         Administracion
            <i class="right fas fa-angle-down"></i>
        </p>
    </a>
    <ul class="nav nav-treeview" style="display: none;">
    <li class="nav-item">
    <a href="{{ route('estudios.index') }}" class="nav-link {{ (Request::is('estudios*') && !Request::is('estudios_usuarios*'))? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Estudios</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('sessiones.index') }}" class="nav-link {{ Request::is('sessiones*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Telemetria de usos</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('estudios_usuarios.index') }}" class="nav-link {{ Request::is('estudios_usuarios*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Estudios Usuarios</p>
    </a>
</li>
    </ul>
</li>

@endif


@endif


