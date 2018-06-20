{def $currentUser=fetch( 'user', 'current_user' )}
{def $trackVisit = true()}

<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                {*<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>*}
                <h4 class="modal-title">Attenzione{if $currentUser.is_logged_in}, {$currentUser.contentobject.name}{/if}</h4>
            </div>
            <div class="modal-body">
                <p>
                    I contenuti delle riunioni del Consiglio di amministrazione sono <strong>strettamente riservati.</strong>
                </p>
                <p>
                    La loro comunicazione o divulgazione a terzi viola il <strong>dovere di fedeltà</strong>
                    cui sono tenuti gli amministratori ed espone alle relative conseguenze.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Ho capito</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    
	var postId = "{$post.object.id}";
	var factoryId = "{$post.factory_identifier}";
	{if and( $currentUser.is_logged_in, $trackVisit )}
	  var currentUser = {ldelim}"name":"{$currentUser.contentobject.name}", "id":{$currentUser.contentobject.id}{rdelim};
	{else}
	  var currentUser = null;
	{/if}
	
	{literal}
    $(document).ready(function(){
        
		var timeLog = {
		  start: null,
		  end: null,
		  init: function(){
			this.start = new Date().getTime();
		  },
		  sendResults: function(){
			this.end = new Date().getTime();
			var data = [
			  {name:'Post',value:postId},
			  {name:'Factory',value:factoryId},
			  {name:'User',value:currentUser.id},
			  {name:'Start',value:this.start/1000},
			  {name:'End',value:this.end/1000}
			];
			$.ajax({
				type: "POST",
				async : false,
				url: '/ezjscore/call/bvcda::logVisit',
				data: data,
				success: function (response) {console.log(response);}
			});
		  }
		};
	
		$(window).bind('contextmenu', false);        
        if (location.protocol != 'http:'){
          window.location = 'http://www.gdf.gov.it/reparti-del-corpo/territorio/trentino-alto-adige/trento/comando-regionale-trentino-alto-adige';
        }
        String.prototype.repeat = function( num ){
          return new Array( num + 1 ).join( this );
        }
		if (currentUser != null) {
		  $('.watermark').html( ('<span>'+currentUser.name+'</span>').repeat( 10000 ) );
		  $('#myModal').modal('show');		  
		  $(window).on('beforeunload', function(){timeLog.sendResults();});		  		  
		  timeLog.init();
		}
		
    });
    {/literal}
</script>


<div class="panel-body" style="background: #fff;position: relative">
  <div class="row">

	  {if $post.object.can_edit}
		<div class="col-xs-6 col-md-2">
			<form method="post" action="{"content/action"|ezurl(no)}" style="display: inline-block;height: 30px;">
				<input type="hidden" name="ContentObjectLanguageCode"
					   value="{ezini( 'RegionalSettings', 'ContentObjectLocale', 'site.ini')}"/>
				<button class="btn btn-info btn-lg" type="submit" name="EditButton" style="z-index:1;position:absolute;">Modifica
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
			</form>
		</div>
	  {/if}        
  </div>

  <hr/>

  <style>
  {literal}
  
	  .main-container{
		  position: relative;
	  }
  
	  .main-container{
		  -webkit-touch-callout: none;
		  -webkit-user-select: none;
		  -khtml-user-select: none;
		  -moz-user-select: none;
		  -ms-user-select: none;
		  user-select: none;
	  }
  
	  @media print{
		  .main-container{
			  display: none;
		  }
	  }
  
	  .overlay{
		  position: absolute;
		  top: 0;
		  width: 100%;
		  height: 100%;
		  color: #ccc;
		  overflow: hidden;
	  }
  
	  .watermark{
		  color: rgba(0,0,0,0.08);
		  font-size: 24px;
		  font-weight: bold;
  
	  }
	  .watermark span{
		  display: inline-block;
		  margin: 1em 0.2em;
		  transform: rotate(30deg);
	  }
  
  {/literal}
  </style>


  <div class="main-container">
	<div class="abstract">
	  {attribute_view_gui attribute=$post.content_attributes.abstract}
	</div>
	<div class="description">
	  {attribute_view_gui attribute=$post.content_attributes.body}
	</div>
  </div>
  
  <div class="overlay">
	<div class="watermark"></div>
  </div>

</div>

{if $post.content_attributes.allegati.has_content}
	<div class="allegati" style="padding: 20px 0;">
		{attribute_view_gui attribute=$post.content_attributes.allegati}

	</div>
{/if}
