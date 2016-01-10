<?php
/**
 * @version         1.0.3
 * @package         components
 * @sub package     com_jeproshop
 * @link            http://jeprodev.net
 *
 * @copyright (C)   2009 - 2011
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of,
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class JeproshopController extends JControllerLegacy
{
    public $use_ajax = true;

    public $default_form_language;

    public $allow_employee_form_language;

    public $allow_link_rewrite;

    /** @var string shop | group_shop */
    public $shopLinkType;

    public $multishop_context = -1;
    public $multishop_context_group = true;

    /**
     * @var array List of loaded routes
     */
    protected $routes = array();

    /**
     * @var bool If true, use routes to build URL (mod rewrite must be activated)
     */
    protected $use_routes = false;

    public $has_errors = false;

    protected $multilang_activated = false;

    public $default_routes = array();

    /** @var array Name and directory where class image are located */
    public $fieldImageSettings = array();

    public static $_current_index;
    protected static $_initialized = false;

    public function display($cachable = FALSE, $urlParams = FALSE){
        //$this->initContent();
        $view = $this->input->get('view', 'dashboard');
        $layout = $this->input->get('layout', 'default');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());
        $viewClass->setLayout($layout);
        $viewClass->display();
    }

    public function initialize(){
        if(self::$_initialized){ return; }
        $app = JFactory::getApplication();
        $context = JeproshopContext::getContext();

        if($app->input->get('use_ajax')){
            $this->use_ajax = true;
        }

        /* Server Params
        $protocol_link = (JeproshopTools::usingSecureMode() && JeproshopSettingModelSetting::getValue('enable_ssl')) ? 'https://' : 'http://';
        $protocol_content = (JeproshopTools::usingSecureMode() && JeproshopSettingModelSetting::getValue('enable_ssl')) ? 'https://' : 'http://';
        */
        if (isset($_GET['logout'])){ $context->employee->logout(); }

        if (isset(JeproshopContext::getContext()->cookie->last_activity)){
            if ($context->cookie->last_activity + 900 < time())
                $context->employee->logout();
            else
                $context->cookie->last_activity = time();
        }

        $controllerName = $app->input->get('view');
        if ($controllerName != 'authenticate' && (!isset($context->employee) || !$context->employee->isLoggedBack())){
            if (isset($context->employee)) {
                $context->employee->logout();
            }
            $email = false;
            if ($app->input->get('email') && JeproshopTools::isEmail($app->inpt->get('email'))){ $email = $app->input->get('email'); }

            //$app->redirect($this->getAdminLink('AdminLogin').((!isset($_GET['logout']) && $controllerName != 'AdminNotFound' && $app->input->get('view')) ? '&redirect=' . $controllerName : '').($email ? '&email='.$email : ''));
        }

        $current_index = 'index.php?option=com_jeproshop' . (($controllerName) ? 'view=' . $controllerName : '');
        if($app->input->get('return')){ $current_index .= '&return=' . urlencode($app->input->get('return')); }
        self::$_current_index = $current_index;
        if($this->use_ajax && method_exists($this, 'ajaxPreProcess')){ $this->ajaxPreProcess(); }

        self::$_initialized = true;

        $this->initProcess();
    }

    public function initProcess(){

    }

    public function initContent(){
        if(!$this->viewAccess()){
            JError::raiseWarning(500, JText::_('COM_JEPROSHOP_YOU_DO_NOT_HAVE_PERMISSION_TO_VIEW_THIS_PAGE_MESSAGE'));
        }

        $this->getLanguages();
        $app = JFactory::getApplication();

        $task = $app->input->get('task');
        $view = $app->input->get('view');
        $viewClass = $this->getView($view, JFactory::getDocument()->getType());

        if($task == 'edit'){
            if(!$viewClass->loadObject(true)){ return false; }
            $viewClass->setLayout('edit');
            $viewClass->renderEditForm();
        }elseif($task == 'add'){
            $viewClass->setLayout('add');
            $viewClass->renderAddForm();
        }elseif($task == 'view'){
            if(!$viewClass->loadObject(true)){ return false; }
            $viewClass->setLayout('view');
            $viewClass->renderView();
        }elseif($task == 'display' || $task  == ''){
            $viewClass->renderDetails();
        }elseif(!$this->use_ajax){

        }else{
            $this->execute($task);
        }
    }

    function viewAccess($disabled = false){
        if($disabled){ return true; }
        return true;
    }

    public function getLanguages(){
        $cookie = JeproshopContext::getContext()->cookie;
        $this->allow_employee_form_language = (int)JeproshopSettingModelSetting::getValue('allow_employee_form_lang');
        if($this->allow_employee_form_language && !$cookie->employee_form_lang){
            $cookie->employee_form_lang = (int)JeproshopSettingModelSetting::getValue('default_lang');
        }

        $lang_exists = false;
        $languages = JeproshopLanguageModelLanguage::getLanguages(false);
        foreach($languages as $language){
            if(isset($cookie->employee_form_language) && $cookie->employee_form_language == $language->lang_id){
                $lang_exists = true;
            }
        }

        $this->default_form_language = $lang_exists ? (int)$cookie->employee_form_language : (int)JeproshopSettingModelSetting::getValue('default_lang');

        return $languages;
    }

    public function catalog(){
        $app = JFactory::getApplication();
        $app->input->set('category_id', null);
        $app->input->set('parent_id', null);
        $app->redirect('index.php?option=com_jeproshop&view=product');
    }

    public function orders(){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_jeproshop&view=order');
    }

    public function customers(){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_jeproshop&view=customer');
    }

    public function price_rules(){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_jeproshop&view=cart&task=rules');
    }

    public function shipping(){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_jeproshop&view=carrier');
    }

    public function localization(){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_jeproshop&view=country');
    }

    public function settings(){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_jeproshop&view=setting');
    }

    public function administration(){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_jeproshop&view=administration');
    }

    public function stats(){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_jeproshop&view=stats');
    }

    /*** Links building ***/

    /**
     * Use controller name to create a link
     *
     * @param string $controller
     * @param boolean $with_token include or not the token in the url
     * @return string url
     * /
    public function getAdminLink($controller, $with_token = true) {
    $lang_id = JeproshopContext::getContext()->language->lang_id;

    $params = $with_token ? array('token' => JeproshopTools::getAdminTokenLite($controller)) : array();
    return $this->createUrl($controller, $lang_id, $params, false);
    }*/

    /**
     * Returns a link to a product image for display
     * Note: the new image filesystem stores product images in subdirectories of img/p/
     *
     * @param string $name rewrite link of the image
     * @param string $ids id part of the image filename - can be "id_product-id_image" (legacy support, recommended) or "id_image" (new)
     * @param string $type
     * @return string
     * /
    public function getImageLink($name, $ids, $type = null){
    $not_default = false;
    // legacy mode or default image
    $theme = ((JeproshopShopModelShop::isFeaturePublished() && file_exists(COM_JEPROSHOP_PRODUCT_IMAGE_DIRECTORY . $ids . ($type ? '_'.$type : '').'_'.(int)JeproshopContext::getContext()->shop->theme_id .'.jpg')) ? '_'.JeproshopContext::getContext()->shop->theme_id : '');
    if ((JeproshopSettingModelSetting::get('legacy_images') && (file_exists(COM_JEPROSHOP_PRODUCT_IMAGE_DIRECTORY . $ids . ($type ? '-'.$type : '').$theme.'.jpg'))) || ($not_default = strpos($ids, 'default') !== false)) {
    if ($this->allow_link_rewrite == 1 && !$not_default){
    //$uri_path = __PS_BASE_URI__.$ids.($type ? '_'.$type : '').$theme.'/'.$name.'.jpg';
    }else{
    //$uri_path = _THEME_PROD_DIR_.$ids.($type ? '_'.$type : '').$theme.'.jpg';
    }
    } else {
    // if ids if of the form id_product-id_image, we want to extract the id_image part
    $split_ids = explode('-', $ids);
    $image_id = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
    $theme = ((JeproshopShopModelShop::isFeaturePublished() && file_exists(_PS_PROD_IMG_DIR_.JeproshopImageModelImage::getStaticImageFolder($image_id). $image_id .($type ? '-'.$type : '').'-'.(int)JeproshopContext::getContext()->shop->theme_id . '.jpg')) ? '-'. JeproshopContext::getContext()->shop->theme_id : '');
    if ($this->allow_link_rewrite == 1){
    //$uri_path = __PS_BASE_URI__. $image_id .($type ? '_'.$type : '').$theme.'/'.$name.'.jpg';
    }else{
    //$uri_path = _THEME_PROD_DIR_. JeproshopImageModelImage::getStaticImageFolder($image_id).$image_id .($type ? '_'.$type : '').$theme.'.jpg';
    }
    }

    //return $this->protocol_content.JeproshopValidator::getMediaServer($uri_path).$uri_path;
    }

    /**
     * Load default routes group by languages
     * /
    protected function loadRoutes($shop_id = null) {
    $context = JeproshopContext::getContext();

    // Load custom routes from modules
    /*$modules_routes = Hook::exec('moduleRoutes', array('id_shop' => $shop_id), null, true, false);
    if (is_array($modules_routes) && count($modules_routes))
    foreach($modules_routes as $module_route)
    {
    if (is_array($module_route) && count($module_route))
    foreach($module_route as $route => $route_details)
    if (array_key_exists('controller', $route_details) && array_key_exists('rule', $route_details)
    && array_key_exists('keywords', $route_details) && array_key_exists('params', $route_details))
    {
    if (!isset($this->default_routes[$route]))
    $this->default_routes[$route] = array();
    $this->default_routes[$route] = array_merge($this->default_routes[$route], $route_details);
    }
    } * /

    $languages = JeproshopLanguageModelLanguage::getLanguages();

    foreach($languages as $language){ $ids[] = $language->lang_id; }
    if (isset($context->language) && !in_array($context->language->lang_id, $ids)) {
    $languages[] = (int)$context->language->lang_id;
    // Set default routes
    foreach ($languages as $lang){
    foreach ($this->default_routes as $id => $route){
    $this->addRoute( $id, $route['rule'], $route['controller'], $lang['lang_id'], $route['keywords'], isset($route['params']) ? $route['params'] : array(), $shop_id );
    }
    }
    }
    $app = JFactory::getApplication();
    // Load the custom routes prior the defaults to avoid infinite loops
    if ($this->use_routes) {
    // Get iso lang
    $iso_lang = $app->input->get('iso_lang');
    if (isset($context->language))
    $lang_id = (int)$context->language->lang_id;
    if ((!empty($iso_lang) && JeproshopTools::isLanguageIsoCode($iso_lang)) || !isset($lang_id))
    $lang_id = JeproshopLanguageModelLanguage::getIdByIso($iso_lang);

    $db = JFactory::getDBO();
    // Load routes from meta table
    $query = "SELECT meta.page, meta_lang.url_rewrite, meta_lang.lang_id FROM " . $db->quoteNazme('#__jeproshop_meta') . " AS meta LEFT JOIN " . $db->quoteName('#__jeproshop_meta_lang');
    $query .= " AS meta_lang ON (meta.meta_id = meta_lang.meta_id" . JeproshopShopModelShop::addSqlRestrictionOnLang('meta_lang', $shop_id) . ") ORDER BY LENGTH(meta_lang.rewrite_url) DESC";

    $db->setQuery($query);
    $results = $db->loadObjectList();
    if ($results){
    foreach ($results as $row) {
    if ($row->rewrite_url){
    $this->addRoute($row->page, $row->rewrite_url, $row->page, $row->lang_id, array(), array(), $shop_id);
    }
    }
    }

    // Set default empty route if no empty route (that's weird I know)
    if (!$this->empty_route){
    $this->empty_route = array(
    'routeID' =>	'index',
    'rule' =>		'',
    'controller' =>	'index',
    );
    }

    // Load custom routes
    /*foreach ($this->default_routes as $route_id => $route_data)
    if ($custom_route = Configuration::get('PS_ROUTE_'.$route_id, null, null, $id_shop))
    {
    if (isset($context->language) && !in_array($context->language->id, $languages = Language::getLanguages()))
    $languages[] = (int)$context->language->id;
    foreach ($languages as $lang)
    $this->addRoute(
    $route_id,
    $custom_route,
    $route_data['controller'],
    $lang['id_lang'],
    $route_data['keywords'],
    isset($route_data['params']) ? $route_data['params'] : array(),
    $id_shop
    );
    }* /
    }
    }

    /**
     * Create an url from
     *
     * @param string $view Name the route
     * @param int $lang_id
     * @param array $params
     * @param bool $force_routes
     * @param string $anchor Optional anchor to add at the end of this url
     * @param null $shop_id
     * @internal param bool $use_routes If false, don't use to create this url
     * @return string
     * /
    public function createUrl($view, $lang_id = null, array $params = array(), $force_routes = false, $anchor = '', $shop_id = null) {
    if ($lang_id === null){
    $lang_id = (int)JeproshopContext::getContext()->language->lang_id;
    }
    if ($shop_id === null){
    $shop_id = (int)JeproshopContext::getContext()->shop->shop_id;
    }
    if (!isset($this->routes[$shop_id])){ $this->loadRoutes($shop_id); }


    if (!isset($this->routes[$shop_id][$lang_id][$view])){
    $query = http_build_query($params, '', '&');
    $index_link = $this->use_routes ? '' : 'index.php?option=com_jeproshop';
    return ($view == 'index') ? $index_link.(($query) ? '?'.$query : '') : ((trim($view) == '') ? '' : 'index.php?option=com_jeproshop&view=' . $view).(($query) ? '&'.$query : '').$anchor;
    }
    $route = $this->routes[$shop_id][$lang_id][$view];
    // Check required fields
    $query_params = isset($route['params']) ? $route['params'] : array();
    foreach ($route['keywords'] as $key => $data){
    if (!$data['required'])
    continue;

    if (!array_key_exists($key, $params))
    die('Dispatcher::createUrl() miss required parameter "'.$key.'" for route "'. $view.'"');
    if (isset($this->default_routes[$view]))
    $query_params[$this->default_routes[$view]['keywords'][$key]['param']] = $params[$key];
    }

    // Build an url which match a route
    if ($this->use_routes || $force_routes) {

    $url = $route['rule'];
    $add_param = array();

    foreach ($params as $key => $value) {
    if (!isset($route['keywords'][$key])) {
    if (!isset($this->default_routes[$view]['keywords'][$key])){ $add_param[$key] = $value; }
    } else {
    if ($params[$key]){
    $replace = $route['keywords'][$key]['prepend'].$params[$key].$route['keywords'][$key]['append'];
    }else{
    $replace = '';
    }
    $url = preg_replace('#\{([^{}]*:)?'.$key.'(:[^{}]*)?\}#', $replace, $url);
    }
    }
    $url = preg_replace('#\{([^{}]*:)?[a-z0-9_]+?(:[^{}]*)?\}#', '', $url);
    if (count($add_param))
    $url .= '?'.http_build_query($add_param, '', '&');
    }else  {
    // Build a classic url index.php?controller=foo&...

    $add_params = array();
    foreach ($params as $key => $value)
    if (!isset($route['keywords'][$key]) && !isset($this->default_routes[$view]['keywords'][$key]))
    $add_params[$key] = $value;

    if (!empty($route['controller']))
    $query_params['controller'] = $route['controller'];
    $query = http_build_query(array_merge($add_params, $query_params), '', '&');
    if ($this->multilang_activated){ $query .=  '&lang_id='.(int)$lang_id; }
    $url = 'index.php?option=com_jeproshop' . $query;
    }

    return $url.$anchor;
    }

    /**
     * add files
     **/
    public function addJs($js_files){
        $document = JFactory::getDocument();
        if(is_array($js_files)){
            foreach($js_files as $js_file){
                $js_path = JURI::base() .  'components/com_jeproshop/assets/javascript/' . $js_file;
                $document->addScript($js_path);
            }
        }else{
            $js_path = JURI::base() . 'components/com_jeproshop/assets/javascript/' . $js_files;
            $document->addScript($js_path);
        }
    }

    public function cancel(){
        $app = JFactory::getApplication();

        $task = $app->input->get('task');
        $view = $app->input->get('view');
        $app->redirect('index.php?option=com_jeproshop&' . ($view ? '&view=' . $view : '') . ((isset($task) && $task != 'cancel') ? '&task=' . $task : ''));
    }
}