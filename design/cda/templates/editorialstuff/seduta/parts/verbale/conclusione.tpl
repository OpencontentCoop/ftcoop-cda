<p>Il Presidente, esaurita la trattazione dell’ordine del giorno ed accertato che nessun altro Consigliere chieda la parola, alle ore {$seduta.data_ora_fine|datetime( 'custom', '%h:%i' )} dichiara chiusa la riunione.</p>
<p>Letto, approvato e sottoscritto.</p>

<table border="0" width="100%">
	<tr>
		<td>
			<p>				
				{if $seduta.object|has_attribute('segretario_verbalizzante')}
					{def $segretario = fetch(content,object,hash(object_id, $seduta.object|attribute('segretario_verbalizzante').content.relation_list[0].contentobject_id))}					
					F.to Il segretario verbalizzante<br />
					{$segretario.name|wash()}
				{/if}
				
			</p>
		</td>
		<td>
			<p> 				
				{if $seduta.object|has_attribute('firmatario')}
					{def $presidente = fetch(content,object,hash(object_id, $seduta.object|attribute('firmatario').content.relation_list[0].contentobject_id))}
					F.to 
					{if $presidente|has_attribute('pre_firma')}
						{$presidente|attribute('pre_firma').content|wash()}
					{else}
						Il Presidente
					{/if}
					<br />
					{$presidente.name|wash()}
				{/if}
			</p>
		</td>
	</tr>
</table>