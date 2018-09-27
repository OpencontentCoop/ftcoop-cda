{def $registro_presenze = $seduta.registro_presenze.hash_user_id}
{def $presenti = array()}
{def $assenti = array()}
{foreach $seduta.partecipanti as $politico}
{if $seduta.current_state.identifier|eq( 'closed' )}
    {if $seduta.percentuale_presenza[$politico.object.id]|gt(0)}
        {set $presenti = $presenti|append($politico.object.name)}
    {/if}
{else}
    {if and(is_set($registro_presenze[$politico.object.id]), $registro_presenze[$politico.object.id]|eq(1))}
        {set $presenti = $presenti|append($politico.object.name)}
    {/if}
{/if}

{if $seduta.current_state.identifier|eq( 'closed' )}
    {if $seduta.percentuale_presenza[$politico.object.id]|eq(0)}
        {set $assenti = $assenti|append($politico.object.name)}
    {/if}
{else}
    {if or(is_set($registro_presenze[$politico.object.id])|not,and(is_set($registro_presenze[$politico.object.id]), $registro_presenze[$politico.object.id]|eq(0)))}
        {set $assenti = $assenti|append($politico.object.name)}
    {/if}
{/if}
{/foreach}

<p>Partecipano alla riunione i consiglieri: </p>
{foreach $presenti as $politico}{$politico|wash()}{delimiter}, {/delimiter}{/foreach}

<p>Hanno giustificato la propria assenza i consiglieri:</p>
{foreach $assenti as $politico}{$politico|wash()}{delimiter}, {/delimiter}{/foreach}

<p>Per il Collegio Sindacale partecipano alla riunione:</p>

<p>Assiste alla riunione {if $seduta.object|has_attribute('segretario_verbalizzante')}{fetch(content,object,hash(object_id, $seduta.object|attribute('segretario_verbalizzante').content.relation_list[0].contentobject_id)).name|wash()}{/if} che funge da Segretario verbalizzante.</p>

{undef $registro_presenze $presenti $assenti}