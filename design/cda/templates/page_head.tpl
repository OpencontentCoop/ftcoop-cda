{default enable_help=true() enable_link=true() canonical_link=true()}

    <title>{$site.title|wash}</title>

    {if and(is_set($#Header:extra_data),is_array($#Header:extra_data))}
      {section name=ExtraData loop=$#Header:extra_data}
      {$:item}
      {/section}
    {/if}

    {* check if we need a http-equiv refresh *}
    {if $site.redirect}
    <meta http-equiv="Refresh" content="{$site.redirect.timer}; URL={$site.redirect.location}" />

    {/if}
    {foreach $site.http_equiv as $key => $item}
        <meta name="{$key|wash}" content="{$item|wash}" />

    {/foreach}
    {foreach $site.meta as $key => $item}
    {if is_set( $module_result.content_info.persistent_variable[$key] )}
        <meta name="{$key|wash}" content="{$module_result.content_info.persistent_variable[$key]|wash}" />
    {else}
        <meta name="{$key|wash}" content="{$item|wash}" />
    {/if}

    {/foreach}

    <meta name="MSSmartTagsPreventParsing" content="TRUE" />
    <meta name="generator" content="eZ Publish" />

{if $canonical_link}
    {include uri="design:canonical_link.tpl"}
{/if}

{if $enable_link}
    {include uri="design:link.tpl" enable_help=$enable_help enable_link=$enable_link}
{/if}

{/default}
