{def $homepage = fetch(content, node, hash('node_id', ezini('NodeSettings', 'RootNode', 'content.ini')))}
<div{if $homepage|has_attribute('background_image')} style="margin-bottom:40px;padding-bottom:30px;background: url({$homepage|attribute('background_image').content.original.full_path|ezroot(no)}) no-repeat center center;background-size: cover;"{/if}>
  {if $homepage|has_attribute('welcome_text')}
    <div class='row' style="margin-bottom:20px;">
      <div class='col-sm-12'>
        <div class="jumbotron" style="opacity: 0.8">
          {attribute_view_gui attribute=$homepage|attribute('welcome_text')}
        </div>
      </div>
    </div>
  {/if}

  <div class='row'>
    <div class='col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4'>
      <div class="panel panel-default" style="opacity: 0.9;padding: 20px;border-radius: 10px">
        <h3 class='text-center title' style="margin-bottom:30px;">Accedi al sistema</h3>
        
        
        <a class='btn btn-lg btn-primary center-block' href="{'b2clogin'|ezurl(no)}">Accedi con utente Federazione</a>

        <hr class='hr-normal'>
        <p class="text-center"><a role="button" data-toggle="collapse" href="#standardLoginForm" aria-expanded="false" aria-controls="standardLoginForm">
          <small>Accedi con nome utente e password</small>
        </a></p>

        <div class="collapse" id="standardLoginForm">

          {if $User:warning.bad_login}
          <div class="alert alert-danger">
          <p><strong>{"Could not login"|i18n("design/ocbootstrap/user/login")}</strong></p>
          <p>{"A valid username and password is required to login."|i18n("design/ocbootstrap/user/login")}</p>
          </div>
          {/if}
          
          {if $site_access.allowed|not}
          <div class="alert alert-danger">
          <p><strong>{"Access not allowed"|i18n("design/ocbootstrap/user/login")}</strong></p>
          <p>{"You are not allowed to access %1."|i18n("design/ocbootstrap/user/login",,array($site_access.name))}</p>
          </div>
          {/if}
          
          <form class="validate-form" method="post" action={"/user/login/"|ezurl} name="loginform">
            <div class='form-group'>
              <div class='controls with-icon-over-input'>          
                <input type="text" autofocus="" autocomplete="off" name="Login" placeholder="{"Username"|i18n("design/ocbootstrap/user/login",'User name')}" class="form-control" data-rule-required="true" value="{$User:login|wash}">
                <i class='icon-user text-muted'></i>
              </div>
            </div>
            <div class='form-group'>
              <div class='controls with-icon-over-input'>          
                <input type="password" autocomplete="off" name="Password" placeholder="{"Password"|i18n("design/ocbootstrap/user/login")}" class="form-control" data-rule-required="true" >
                <i class='icon-lock text-muted'></i>
              </div>
            </div>
            <div class='checkbox'>
              <label for='remember_me'>          
                <input id='remember_me' type="checkbox" tabindex="1" name="Cookie" id="id4" />{"Remember me"|i18n("design/ocbootstrap/user/login")}          
              </label>
            </div>
            <button class='btn btn-lg btn-primary center-block' name="LoginButton">{'Login'|i18n('design/ocbootstrap/user/login','Button')}</button>
            
            {if and( is_set( $User:post_data ), is_array( $User:post_data ) )}
              {foreach $User:post_data as $key => $postData}
                 <input name="Last_{$key|wash}" value="{$postData|wash}" type="hidden" /><br/>
              {/foreach}
            {/if}
            <input type="hidden" name="RedirectURI" value="{$User:redirect_uri|wash}" />
            
          </form>
          <div class='text-center'>
            <hr class='hr-normal'>
            <a href={'/user/forgotpassword'|ezurl}>{'Forgot your password?'|i18n( 'design/ocbootstrap/user/login' )}</a>
          </div>
        
        </div>
      
      </div>
    </div>
  </div>
</div>