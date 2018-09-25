{def $module = module_params().module_name
     $function = module_params().function_name
     $param = cond( is_set( module_params().parameters.FactoryIdentifier ), module_params().parameters.FactoryIdentifier, false() )}
{def $current_module = concat( $module, '/', $function, '/', $param )}
{def $active_dashboards = fetch(consiglio, active_dashboards)}
{def $available_factories = ezini('AvailableFactories', 'Identifiers', 'editorialstuff.ini')}

<div class="nav-main container">
    <div class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="container">
            <div class="row">

                <div class="col-lg-2">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-navbar">
                        <span class="sr-only">Mostra navigazione</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="brand center-block text-center" href={'/'|ezurl}>
                        <img src="{'logo.png'|ezimage(no)}" alt="{ezini( 'SiteSettings', 'GlobalSiteName', 'site.ini' )}" height="45" />
                    </a>
                </div>

                <div class="col-lg-9">
                    <div class="collapse navbar-collapse" id="main-navbar">
                        <ul class="nav navbar-nav">
                            {if fetch( 'user', 'has_access_to', hash( module, 'consiglio', function, 'use' ))}
                            <li class="menu-item{if $current_module|eq('consiglio/dashboard/')} current{/if}">
                            	<a href="{'consiglio/dashboard'|ezurl(no)}"><b>Bacheca</b></a>
                            </li>
                            {/if}

                            {if and(is_set($active_dashboards['areacollaborativa']), fetch( 'user', 'has_access_to', hash( module, 'consiglio', function, 'collaboration' )))}
                            <li class="menu-item{if $current_module|eq('consiglio/collaboration/')} current{/if}">
                            	<a href="{'consiglio/collaboration'|ezurl(no)}"><b>Area collaborativa</b></a>
                            </li>
                            {/if}

                            {if fetch( 'user', 'has_access_to', hash( module, 'consiglio', function, 'admin' ))}
	                            <li class="dropdown menu-item{if or(
	                            	$current_module|eq('editorialstuff/dashboard/seduta'),
	                            	$current_module|eq('editorialstuff/dashboard/audizione'),
	                            	$current_module|eq('editorialstuff/dashboard/designazione'),
	                            	$current_module|eq('editorialstuff/dashboard/parere'),
	                            	$current_module|eq('editorialstuff/dashboard/bozze_verbali_cda'),
                            	)} current{/if}">
	                                <a data-toggle="dropdown" href="#" class="dropdown-toggle"><b>Attivit&agrave;</b> <i class="fa fa-chevron-down"></i></a>
	                                <ul class="nav dropdown-menu">
	                                    {if is_set($active_dashboards['seduta'])}
	                                    <li><a href="{'editorialstuff/dashboard/seduta'|ezurl(no)}">Sedute</a></li>
	                                    {/if}
	                                    {if is_set($active_dashboards['audizione'])}
	                                    <li><a href="{'editorialstuff/dashboard/audizione'|ezurl(no)}">Audizioni</a></li>
	                                    {/if}
	                                    {if is_set($active_dashboards['designazione'])}
	                                    <li><a href="{'editorialstuff/dashboard/designazione'|ezurl(no)}">Designazioni</a></li>
	                                    {/if}
	                                    {if is_set($active_dashboards['parere'])}
	                                    <li><a href="{'editorialstuff/dashboard/parere'|ezurl(no)}">Pareri</a></li>
	                                    {/if}
	                                    {if is_set($active_dashboards['proposta'])}
	                                    <li><a href="{'editorialstuff/dashboard/proposta'|ezurl(no)}">Proposte ordine del giorno</a></li>
	                                    {/if}
	                                    {if $available_factories|contains('bozze_verbali_cda')}
	                                    <li><a href="{'editorialstuff/dashboard/bozze_verbali_cda/'|ezurl(no)}">Bozze verbali</a></li>
	                                    {/if}
	                                </ul>
	                            </li>
	                            <li class="dropdown menu-item{if or($current_module|eq('editorialstuff/dashboard/areacollaborativa'),$current_module|eq('editorialstuff/dashboard/materia'),$current_module|eq('editorialstuff/dashboard/politico'),$current_module|eq('editorialstuff/dashboard/tecnico'),$current_module|eq('editorialstuff/dashboard/invitato'),$current_module|eq('editorialstuff/dashboard/referentelocale'),$current_module|eq('editorialstuff/dashboard/responsabilearea'),$current_module|eq('editorialstuff/dashboard/cda_evento'),$current_module|eq('editorialstuff/dashboard/cda_documento'))} current{/if}">
	                                <a data-toggle="dropdown" href="#" class="dropdown-toggle"><b>Gestione</b> <i class="fa fa-chevron-down"></i></a>
	                                <ul class="nav dropdown-menu">
	                                    {if is_set($active_dashboards['materia'])}
	                                    <li><a href="{'editorialstuff/dashboard/materia'|ezurl(no)}">Materie</a></li>
	                                    {/if}
	                                    {if is_set($active_dashboards['politico'])}
	                                    <li><a href="{'editorialstuff/dashboard/politico'|ezurl(no)}">Politici</a></li>
	                                    {/if}
	                                    {if is_set($active_dashboards['organo'])}
	                                    <li><a href="{'editorialstuff/dashboard/organo'|ezurl(no)}">Organi sociali</a></li>
	                                    {/if}
	                                    {if is_set($active_dashboards['tecnico'])}
	                                    <li><a href="{'editorialstuff/dashboard/tecnico'|ezurl(no)}">Tecnici</a></li>
	                                    {/if}
	                                    {if is_set($active_dashboards['invitato'])}
	                                    <li><a href="{'editorialstuff/dashboard/invitato'|ezurl(no)}">Invitati</a></li>
	                                    {/if}
	                                    <li><a href="{'consiglio/gettoni'|ezurl(no)}">Gettoni di presenza</a></li>
	                                    {if is_set($active_dashboards['referentelocale'])}
	                                    <li><a href="{'editorialstuff/dashboard/referentelocale'|ezurl(no)}">Referenti locali</a></li>
	                                    {/if}
	                                    {if is_set($active_dashboards['areacollaborativa'])}
	                                    <li><a href="{'editorialstuff/dashboard/areacollaborativa'|ezurl(no)}">Aree collaborative</a></li>	                                    
	                                    {/if}
	                                    {if is_set($active_dashboards['responsabilearea'])}
	                                    <li><a href="{'editorialstuff/dashboard/responsabilearea'|ezurl(no)}">Responsabili d'area</a></li>	                                    
	                                    {/if}
	                                    {if is_set($active_dashboards['cda_evento'])}
	                                    <li><a href="{'editorialstuff/dashboard/cda_evento'|ezurl(no)}">Eventi</a></li>	                                    
	                                    {/if}
	                                    {if is_set($active_dashboards['cda_documento'])}
	                                    <li><a href="{'editorialstuff/dashboard/cda_documento'|ezurl(no)}">Documenti</a></li>	                                    
	                                    {/if}
	                                </ul>
	                            </li>
                            {elseif fetch( 'user', 'has_access_to', hash( module, 'consiglio', function, 'use' ))}
	                            {if is_set($active_dashboards['seduta'])}
	                            <li class="menu-item{if $current_module|eq('editorialstuff/dashboard/seduta')} current{/if}">
	                                <a href="{'editorialstuff/dashboard/seduta'|ezurl(no)}"><b>Archivio sedute</b></a>
	                            </li>
	                            {/if}
	                            {if is_set($active_dashboards['audizione'])}
	                            <li class="menu-item{if $current_module|eq('editorialstuff/dashboard/audizione')} current{/if}">
	                                <a href="{'editorialstuff/dashboard/audizione'|ezurl(no)}"><b>Archivio audizioni</b></a>
	                            </li>
	                            {/if}
	                            {if is_set($active_dashboards['parere'])}
	                            <li class="menu-item{if $current_module|eq('editorialstuff/dashboard/parere')} current{/if}">
	                                <a href="{'editorialstuff/dashboard/parere'|ezurl(no)}"><b>Archivio pareri</b></a>
	                            </li>
	                            {/if}
	                            {if $available_factories|contains('bozze_verbali_cda')}
	                            <li class="menu-item{if $current_module|eq('editorialstuff/dashboard/bozze_verbali_cda')} current{/if}">
	                            	<a href="{'editorialstuff/dashboard/bozze_verbali_cda/'|ezurl(no)}"><b>Bozze verbali</b></a>
	                            </li>
	                            {/if}
                            {/if}
                        </ul>

						{if $pagedata.is_edit|not()}
                        <form class="navbar-form col-md-4 pull-right" role="search" method="get" action="{'/content/search'|ezurl( 'no' )}" id="site-wide-search" style="{if is_set( $pagedata.persistent_variable.hide_header_searchbox )}visibility: hidden;{/if}max-width: 200px;">
						  <div class="input-group">
						      <input type="text" class="form-control"  name="SearchText" id="site-wide-search-field" placeholder="{'Search'|i18n('design/ocbootstrap/pagelayout')}" name="srch-term" id="srch-term">
						      <div class="input-group-btn">
						        <button class="btn btn-primary" type="submit" ><i class="glyphicon glyphicon-search"></i></button>
						      </div>
						      {if eq( $ui_context, 'browse' )}
						        <input name="Mode" type="hidden" value="browse" />
						      {else}
						      	{def $openpa_consiglio_root = fetch(content,object,hash('remote_id', 'openpa_consiglio'))}
						      	{if $openpa_consiglio_root}
						      		<input type="hidden" name="SubTreeArray" value="{$openpa_consiglio_root.main_node_id}">
					      		{/if}
					      		{undef $openpa_consiglio_root}
						      {/if}						    
						  </div>
						</form>
						{/if}

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
