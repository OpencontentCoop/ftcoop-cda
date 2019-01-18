{def $registro_presenze = $seduta.registro_presenze.hash_user_id}
{def $presenti = array()}
{def $assenti = array()}
{def $componenti_non_consiglieri_presenti = array()}
{def $componenti_non_consiglieri_id_list = $seduta.organo.componenti_non_consiglieri_id_list}
{foreach $seduta.partecipanti as $politico}
{if $seduta.current_state.identifier|eq( 'closed' )}
    {if $seduta.percentuale_presenza[$politico.object.id]|gt(0)}
        {if $componenti_non_consiglieri_id_list|contains($politico.object.id)}
            {set $componenti_non_consiglieri_presenti = $componenti_non_consiglieri_presenti|append($politico.object.name)}
        {else}
            {set $presenti = $presenti|append($politico.object.name)}
        {/if}
    {/if}
{else}
    {if and(is_set($registro_presenze[$politico.object.id]), $registro_presenze[$politico.object.id]|eq(1))}
        {if $componenti_non_consiglieri_id_list|contains($politico.object.id)}
            {set $componenti_non_consiglieri_presenti = $componenti_non_consiglieri_presenti|append($politico.object.name)}
        {else}
            {set $presenti = $presenti|append($politico.object.name)}
        {/if}
    {/if}
{/if}

{if $seduta.current_state.identifier|eq( 'closed' )}
    {if $seduta.percentuale_presenza[$politico.object.id]|eq(0)}
        {if $componenti_non_consiglieri_id_list|contains($politico.object.id)|not()}
            {set $assenti = $assenti|append($politico.object.name)}
        {/if}
    {/if}
{else}
    {if or(is_set($registro_presenze[$politico.object.id])|not,and(is_set($registro_presenze[$politico.object.id]), $registro_presenze[$politico.object.id]|eq(0)))}
        {if $componenti_non_consiglieri_id_list|contains($politico.object.id)|not()}
            {set $assenti = $assenti|append($politico.object.name)}
        {/if}
    {/if}
{/if}
{/foreach}

{if count($presenti)|gt(0)}
<p><u>Partecipano alla riunione i consiglieri:</u> <br />
{foreach $presenti as $politico}{$politico|trim()|wash()}{delimiter}, {/delimiter}{/foreach}
</p>
{/if}

{if count($assenti)|gt(0)}
<p><u>Hanno giustificato la propria assenza i consiglieri:</u><br />
{foreach $assenti as $politico}{$politico|trim()|wash()}{delimiter}, {/delimiter}{/foreach}
</p>
{/if}

{if count($componenti_non_consiglieri_presenti)|gt(0)}
<p><u>Per il Collegio Sindacale partecipano alla riunione:</u><br />
{foreach $componenti_non_consiglieri_presenti as $politico}{$politico|trim()|wash()}{delimiter}, {/delimiter}{/foreach}
</p>
{/if}

<p>Assiste alla riunione {if $seduta.object|has_attribute('segretario_verbalizzante')}{fetch(content,object,hash(object_id, $seduta.object|attribute('segretario_verbalizzante').content.relation_list[0].contentobject_id)).name|wash()}{/if} che funge da Segretario verbalizzante.</p>

{undef $registro_presenze $presenti $assenti $componenti_non_consiglieri_presenti $componenti_non_consiglieri_id_list}