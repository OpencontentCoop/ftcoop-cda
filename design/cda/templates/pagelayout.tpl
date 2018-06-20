<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<!--[if lt IE 9 ]><html class="unsupported-ie ie" lang="{$site.http_equiv.Content-language|wash}"><![endif]-->
<!--[if IE 9 ]><html class="ie ie9" lang="{$site.http_equiv.Content-language|wash}"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="{$site.http_equiv.Content-language|wash}"><!--<![endif]-->
<head>
{def $user_hash = concat( $current_user.role_id_list|implode( ',' ), ',', $current_user.limited_assignment_value_list|implode( ',' ) )}

{if is_set( $extra_cache_key )|not}
    {def $extra_cache_key = ''}
{/if}

{cache-block expiry=86400 keys=array( $module_result.uri, $current_user.contentobject_id, $extra_cache_key )}

{def $pagedata = ezpagedata()}
{def $current_node_id = $pagedata.node_id}
{def $module_params = module_params()}
{def $show_path = cond(and($pagedata.show_path, $pagedata.is_edit|not), true(), false())}
{if $module_params.module_name|eq('user')}
  {set $show_path = false()}
{/if}

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<META name="robots" content="NOINDEX,NOFOLLOW" />  

{include uri='design:page_head.tpl'}
{include uri='design:page_head_style.tpl'}
{include uri='design:page_head_script.tpl'}

</head>
<body>

<div id="page">
    {include uri='design:page_header.tpl'}    

    {if $pagedata.is_edit|not}
        {include uri='design:nav/nav-main.tpl'}
    {/if}

    {if and( $pagedata.website_toolbar, $pagedata.is_edit|not)}
      {include uri='design:page_toolbar.tpl'}
    {/if}

    {if $show_path}
        {include uri='design:breadcrumb.tpl'}      
    {/if}

    <div class="container">

{/cache-block}

      {$module_result.content}

{cache-block expiry=86400 keys=array( $module_result.uri, $user_hash, $access_type.name, $extra_cache_key )}

    </div>

</div>


{include uri='design:page_footer_script.tpl'}

{/cache-block}
{* This comment will be replaced with actual debug report (if debug is on). *}
<!--DEBUG_REPORT-->
</body>
</html>
