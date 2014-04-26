<?php

// no direct access
defined("_JEXEC") or die("Restricted access");
define( 'DS', DIRECTORY_SEPARATOR );
if (!class_exists('Facebook', false)) {
  require_once (JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'fb_tw_plus1'.DS.'facebook'.DS.'facebook.php');
}

jimport('joomla.html.html');
jimport('joomla.form.formfield');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.form.helper');

class JFormFieldfbextraparams extends JFormField {
  protected $type = "fbextraparams";

  protected function getInput(){
    $fbparams      = $this->form->getValue('params');
    $enable_fb_autopublish = $fbparams->enable_fb_autopublish;
    if ($enable_fb_autopublish=='0') return '';
    $fb_app_id     = $fbparams->app_id;
    $fb_secret_key = $fbparams->fb_secret_key;
    $fb_token      = $fbparams->fb_extra_params['fb_token'];
    $fb_admin      = $fbparams->fb_extra_params['fb_admin'];
    if ($fbparams->fb_extra_params['fb_ids']=='') {
      $fb_ids      = array();
    } else {
      $fb_ids      = $fbparams->fb_extra_params['fb_ids'];
    }
    echo "<div id='fb_extra_params'><ul>";
    if (($fb_app_id != '') && ($fb_secret_key != '')) {

      $facebook = new Facebook(array('appId' => $fb_app_id, 'secret' => $fb_secret_key));
      $user = $facebook->getUser();
      
      $uri= JFactory::getURI();
      $url=urlencode($uri->current().'?'.$uri->getQuery());
      $loginUrl = "https://www.facebook.com/dialog/oauth?client_id=".$fb_app_id."&redirect_uri=".$url."&scope=publish_stream,offline_access,manage_pages,user_groups";
 
      if ($fb_token != '') { //if there is a token => check the token
        $facebook->setAccessToken($fb_token);
        try {
          $user=$facebook->api('/me');
        } catch (Exception $e) { 
          //Access token is not valid
          $user=null;
        }
      } else {
        //Access token missing
        $user=null;
      }

      if ($user) { //access token ok               
        $fb_admin = $user['id'];
        echo "<li><label id='jform_params_fbextraparams_fb_token-lbl' for='jform_params_fbextraparams_fb_token' class='hasTip' title='Facebook security access token'>Access Token</label>
              <input type='text' name='jform[params][".$this->fieldname."][fb_token]' id='jform_params_fbextraparams_fb_token' value='".$fb_token."' size='50'/></li>";
        echo "<li><label id='jform_params_fbextraparams_fb_admin-lbl' for='jform_params_fbextraparams_fb_admin' class='hasTip' title='Administrator ID'>Autopublish administrator ID</label>
              <input type='text' name='jform[params][".$this->fieldname."][fb_admin]' id='jform_params_fbextraparams_fb_admin' value='".$fb_admin."' size='50'/></li>";
        if (count($fb_ids)==0) {
          echo "<li><div class='fb_box'><label class='fb_message'>Choose the walls account,groups or pages where to publish</label></div></li>";
        }
        echo "<li><label class='fb_bold'>Admin account</label></li>";
        
        echo "<li><label class='hasTip' title='Wall ID::Administrator ID'>".$user['name']."</label>
              <input type='checkbox' name='jform[params][".$this->fieldname."][fb_ids][]' value='".$fb_admin."'".(in_array($fb_admin, $fb_ids) ? " checked='checked'" : "")."></li>";

        try {
          $groups = $facebook->api('/'.$fb_admin.'/groups/','GET', array('access_token' => $fb_token));
        } catch (FacebookApiException $e) {
          JFactory::getApplication()->enqueueMessage( '<pre>'.print_r($e,true).'</pre>', 'message' );
          $groups = null;
        }
        if ($groups && $groups['data'] && is_array($groups['data']) && count($groups['data']) > 0) {
          echo "<li><label class='fb_bold'>Groups</label></li>";
          foreach($groups['data'] as $group) {
            echo "<li><label class='fb_label'>".$group['name']."</label>
                  <input type='checkbox' name='jform[params][".$this->fieldname."][fb_ids][]' value='".$group['id']."'".(in_array($group['id'], $fb_ids) ? " checked='checked'" : "")."></li>";
          }
        }
 
        try {
          $pages = $facebook->api('/'.$fb_admin.'/accounts/', 'GET', array('access_token' => $fb_token));
        } catch (FacebookApiException $e) {
          JFactory::getApplication()->enqueueMessage( '<pre>'.print_r($e,true).'</pre>', 'message' );
          $pages = null;
        }        
        if ($pages && $pages['data'] && is_array($pages['data']) && count($pages['data']) > 0) {
          echo "<li><label class='fb_bold'>Pages</label></li>";
          foreach($pages['data'] as $page) {
            if ($page['category'] != 'Application') {
              echo "<li><label class='fb_label'>".$page['name']."</label>
                    <input type='checkbox' name='jform[params][".$this->fieldname."][fb_ids][]' value='".$page['id']."'".(in_array($page['id'], $fb_ids) ? " checked='checked'" : "")."></li>";
            }
          } 
        }
      } else { //if access token is not valid or missing
        $code = JRequest::getVar('code');
        if ($code && !empty($code)) {
          if ($fb_post_mode=='2') {
            try {
              $fb_token = $facebook->getAccessToken();
            } catch (FacebookApiException $e) {
              JFactory::getApplication()->enqueueMessage( '<pre>'.print_r($e,true).'</pre>', 'message' );
              $fb_token = null;
            }
          } else {
            $uri= JFactory::getURI();
            $url=urlencode($uri->current().'?'.$uri->getQuery());       
            $token_url = "https://graph.facebook.com/oauth/access_token?client_id=".$fb_app_id."&redirect_uri=".$url."&client_secret=".$fb_secret_key."&code=".$code;
            $response = $this->get_url_contents($token_url);
            if (empty($response)||preg_match('/error/i',$response)){
              JFactory::getApplication()->enqueueMessage( 'Error on access token:<pre>'.$response.'</pre>', 'error' );
            } else {
              $params = null;
              parse_str($response, $params);
              $fb_token = $params['access_token'];
            }
          }   
          echo "<li><label id='jform_params_fbextraparams_fb_token-lbl' for='jform_params_fbextraparams_fb_token' class='hasTip' title='Facebook security access token'>Access Token</label>
                <input type='text' name='jform[params][".$this->fieldname."][fb_token]' id='jform_params_fbextraparams_fb_token' value='".$fb_token."' size='50'/></li>";
          echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_admin]' value=''/></li>";
          echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_ids]'   value=''/></li>";
          echo "<li><div class='fb_box'><label class='fb_message'>Save to complete the configuration of the Facebook Application</label></div></li>";
        } else {
          $fb_token='';
          $fb_admin='';
          echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_token]' value=''/></li>";
          echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_admin]' value=''/></li>";
          echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_ids]'   value=''/></li>"; 
          echo "<li><div class='fb_box'><label class='fb_message'>Click the link to connect the Facebook Application</label>
                <a class='fb_button' href='".$loginUrl."' title='Facebook login'>Facebook login</a></div></li>";
        }
      }
    } else {
      $fb_token='';
      $fb_admin='';
      echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_token]' id='jform_params_fbextraparams_fb_token' value=''/></li>";
      echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_admin]' id='jform_params_fbextraparams_fb_admin' value=''/></li>";
      echo "<li><input type='hidden' name='jform[params][".$this->fieldname."][fb_ids]'   id='jform_params_fbextraparams_fb_ids'   value='".array()."'/></li>";     
    }
    echo "</ul>";
    echo "</div>";
    echo "<style>.fb_bold { font-weight:bold; color:#686;} .fb_label{font-size:11px;width:145px;} 
                 div.fb_box label.fb_message { font-weight:bold; color:#600; width: auto; max-width:auto; background-color: #FEE; padding: 5px 8px; border: 1px solid #F99; float: none; margin: 5px auto;}
                 .fb_box { display: block; float: left; clear: left; text-align: center; width: 100%; }
                 .fb_button { display: inline-block; padding: 6px 8px; font-size: 12px; font-weight: bold; border: 1px solid #CCD; margin: 5px; text-decoration: none;}</style>";
 
  }

  private function get_url_contents($url){
    $ch = curl_init();
    $timeout = 5;
    curl_setopt ($ch, CURLOPT_URL,$url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
  }

}

?>