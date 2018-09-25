<p>Partecipano alla riunione i consiglieri: </p>

<p>Hanno giustificato la propria assenza i consiglieri:</p>

<p>Per il Collegio Sindacale partecipano alla riunione:</p>

<p>Assiste alla riunione {if $seduta.object|has_attribute('segretario_verbalizzante')}{fetch(content,object,hash(object_id, $seduta.object|attribute('segretario_verbalizzante').content.relation_list[0].contentobject_id)).name|wash()}{/if} che funge da Segretario verbalizzante.</p>