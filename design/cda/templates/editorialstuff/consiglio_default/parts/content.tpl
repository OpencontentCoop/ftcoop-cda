{def $count_assigned_nodes = count($post.object.assigned_nodes)}
<div class="panel-body" style="background: #fff">


    <div class="row">

        {if $post.object.can_edit}
            <div class="col-xs-6 col-md-8">
                <form method="post" action="{"content/action"|ezurl(no)}" style="display: inline;">
					<div class="row panel-body">
                    <input type="hidden" name="ContentObjectLanguageCode"
                           value="{ezini( 'RegionalSettings', 'ContentObjectLocale', 'site.ini')}"/>
                    <button class="btn btn-info btn-lg" type="submit" name="EditButton">Modifica
                    </button>
                    <input type="hidden" name="HasMainAssignment" value="1"/>
                    <input type="hidden" name="ContentObjectID" value="{$post.object.id}"/>
                    <input type="hidden" name="NodeID" value="{$post.node.node_id}"/>
                    <input type="hidden" name="ContentNodeID" value="{$post.node.node_id}"/>
                    {* If a translation exists in the siteaccess' sitelanguagelist use default_language, otherwise let user select language to base translation on. *}
                    {def $avail_languages = $post.object.available_languages
                    $content_object_language_code = ''
                    $default_language = $post.object.default_language}
                    {if and( $avail_languages|count|ge( 1 ), $avail_languages|contains( $default_language ) )}
                        {set $content_object_language_code = $default_language}
                    {else}
                        {set $content_object_language_code = ''}
                    {/if}
                    <input type="hidden" name="ContentObjectLanguageCode"
                           value="{$content_object_language_code}"/>
                    <input type="hidden" name="RedirectIfDiscarded"
                           value="{concat('editorialstuff/edit/', $factory_identifier, '/',$post.object.id)}"/>
                    <input type="hidden" name="RedirectURIAfterPublish"
                           value="{concat('editorialstuff/edit/', $factory_identifier, '/',$post.object.id)}"/>

					{if and($post.object.can_remove, $count_assigned_nodes|eq(1))}
                        <button class="btn btn-danger btn-lg" type="submit" name="ActionRemove">Rimuovi</button>
                        <input type="hidden" name="RedirectURIAfterRemove" value="{concat('editorialstuff/dashboard/', $factory_identifier)}" />
                    {/if}
                    {if $post.object.class_identifier|eq('user')}
                        <a href="#" data-infederazione="{$post.object.id|wash()}" class="btn btn-info btn-lg"><i class="fa fa-spinner fa-spin"></i></a>
                    {/if}
                    </div>
                </form>
            </div>
        {/if}
        {*<div class="col-xs-6 col-md-2">
            <a class="btn btn-info btn-lg" data-toggle="modal"
               data-load-remote="{concat( 'layout/set/modal/content/view/full/', $post.object.main_node_id )|ezurl('no')}"
               data-remote-target="#preview .modal-content" href="#"
               data-target="#preview">Anteprima</a>
        </div>*}
    </div>

    <hr/>

    {if and( $post.factory_identifier|eq('politico'), $post.object.can_edit )}
    <div class="row edit-row">
        <div class="col-md-3"><strong><em>Gruppi</em></strong></div>
        <div class="col-md-9">
            <ul class="list-unstyled">
            {foreach $post.organi as $identifier => $id}
                <li style="padding: 3px 0">
                    {if $post.is_in[$identifier]}
                        <form action="{concat('editorialstuff/action/politico/', $post.object_id)|ezurl(no)}" enctype="multipart/form-data" method="post" class="form-horizontal">
                            <input type="hidden" name="ActionIdentifier" value="RemoveFromOrgano" />
                            <input type="hidden" name="ActionParameters[organo]" value="{$id}" />
                            <button type="submit" name="RemoveFromOrgano" class="btn btn-danger btn-xs">Rimuovi da {$identifier|wash()}</button>
                        </form>
                    {else}
                        <form action="{concat('editorialstuff/action/politico/', $post.object_id)|ezurl(no)}" enctype="multipart/form-data" method="post" class="form-horizontal">
                            <input type="hidden" name="ActionIdentifier" value="AddToOrgano" />
                            <input type="hidden" name="ActionParameters[organo]" value="{$id}" />
                            <button type="submit" name="AddToOrgano" class="btn btn-success btn-xs">Aggiungi a {$identifier|wash()}</button>
                        </form>
                    {/if}
                </li>
            {/foreach}
            </ul>
        </div>
    </div>
    {/if}

    <div class="row edit-row">
        <div class="col-md-3"><strong><em>Autore</em></strong></div>
        <div class="col-md-9">
            {if $post.object.owner}{$post.object.owner.name|wash()}{else}?{/if}
        </div>
    </div>

    <div class="row edit-row">
        <div class="col-md-3"><strong><em>Data di pubblicazione</em></strong></div>
        <div class="col-md-9">
            <p>{$post.object.published|l10n(shortdatetime)}</p>
            {if $post.object.current_version|gt(1)}
                <small>Ultima modifica di <a
                            href={$post.object.main_node.creator.main_node.url_alias|ezurl}>{$post.object.main_node.creator.name}</a>
                    il {$post.object.modified|l10n(shortdatetime)}</small>
            {/if}
        </div>
    </div>


    <div class="row edit-row">
        <div class="col-md-3"><strong><em>Collocazioni</em></strong></div>
        <div class="col-md-9">
            <ul class="list-unstyled">
                {foreach $post.object.assigned_nodes as $item}
                    <li style="padding: 3px 0">
                        {if and($count_assigned_nodes|gt(1), $item.can_remove)}
                        <form method="post" action="{"content/action"|ezurl(no)}" style="display: inline;">
                            <input type="hidden" name="ContentObjectID" value="{$post.object.id}"/>
                            <input type="hidden" name="NodeID" value="{$item.node_id}"/>
                            <input type="hidden" name="ContentNodeID" value="{$item.node_id}"/>
                            <button class="btn btn-danger btn-xs" type="submit" name="ActionRemove"><i class="fa fa-trash-o"></i></button>
                            <input type="hidden" name="RedirectURIAfterRemove" value="{concat('editorialstuff/dashboard/', $factory_identifier)}" />
                        </form>
                        {/if}
                        {$item.path_with_names} {if $item.node_id|eq($post.object.main_node_id)}(principale){/if}
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>

    {foreach $post.content_attributes as $identifier => $attribute}
		{if $attribute.has_content}
        <div class="row edit-row">
            <div class="col-md-3"><strong>{$attribute.contentclass_attribute_name}</strong></div>
            <div class="col-md-9">
                {attribute_view_gui attribute=$attribute image_class=medium}
            </div>
        </div>
		{/if}
    {/foreach}


</div>
{ezscript_require( array( 'ezjsc::jquery', 'ezjsc::jqueryio') )}
<script>
{literal}
    $(document).ready(function () {
        var button = $('[data-infederazione]');
        var objectId = button.data('infederazione');
        var userExists = function(objectId, onFail){
            var button = $('[data-infederazione="'+objectId+'"]');
            button.html('<i class="fa fa-spinner fa-spin"></i>');
            $.ez('infederazione::exists::' + objectId, false, function (data) {                            
                if (data.exists){
                    button.removeClass('btn-info').removeClass('btn-danger').addClass('btn-success');
                    button.text('Utenza federazione presente');
                }else{
                    button.removeClass('btn-info').addClass('btn-danger');
                    button.text('Crea utenza federazione'); 
                    if ($.isFunction(onFail)){
                        button.html('<i class="fa fa-spinner fa-spin"></i>');
                        onFail(objectId);
                    }
                }
            });
        }
        userExists(objectId);
        button.on('click', function(e){
            userExists(objectId, function(){
                $.ez('infederazione::create::' + objectId, false, function (data) {
                    userExists(objectId);
                });
            });
            e.preventDefault();
        })
    });
{/literal}
</script>
