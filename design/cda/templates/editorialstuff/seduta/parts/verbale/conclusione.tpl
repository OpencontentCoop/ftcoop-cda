<p>Il Presidente, esaurita la trattazione dell’ordine del giorno ed accertato che nessun altro Consigliere chieda la parola, alle ore {$seduta.data_ora_fine|datetime( 'custom', '%h:%i' )} dichiara chiusa la riunione.</p>
<p>Letto, approvato e sottoscritto.</p>

<p>F.to il segretario verbalizzante<br />
{if $seduta.object|has_attribute('segretario_verbalizzante')}{fetch(content,object,hash(object_id, $seduta.object|attribute('segretario_verbalizzante').content.relation_list[0].contentobject_id)).name|wash()}{/if}
</p>

<p> F.to il Presidente<br />
{if $seduta.object|has_attribute('firmatario')}{fetch(content,object,hash(object_id, $seduta.object|attribute('firmatario').content.relation_list[0].contentobject_id)).name|wash()}{/if}
</p>