<?php
/**
 * SearchSphinx! Module Entry Point
 * 
 * @subpackage Modules
 * @license    GNU/GPL, see LICENSE.php
 * mod_zsearchsphinx is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';
//JHtml::stylesheet(Juri::base() . 'modules/mod_zsearchsphinx/css/bootstrap.css');
//JHtml::stylesheet(Juri::base() . 'http://code.jquery.com/ui/1.9.0/themes/smoothness/jquery-ui.css');   
JHtml::stylesheet(Juri::base() . 'modules/mod_zsearchsphinx/css/style.css');   
//JHtml::_("script", "https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js");//hele
//JHtml::_("script", "https://code.jquery.com/jquery-1.12.4.min.js");
//    JHtml::_("script", "https://code.jquery.com/jquery-1.9.1.min.js");
//JHtml::_('jquery.framework');
//JHtml::script(Juri::base() . 'modules/mod_zsearchsphinx/js/bootstrap.min.js');   //hele
//JHtml::_('bootstrap.framework');
//JHtml::_("script", "https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"); //hele
//JHtml::_('jquery.ui');
//  JHtml::_("script", "https://code.jquery.com/ui/1.9.2/jquery-ui.min.js");
JHtml::script(Juri::base() . 'modules/mod_zsearchsphinx/js/search.js');
$docs = ModZSearchSphinxHelper::getSearch();
require JModuleHelper::getLayoutPath('mod_zsearchsphinx');
