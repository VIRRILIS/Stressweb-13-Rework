<?php
/**
*
* @ IonCube v8.3 Loader By DoraemonPT
* @ PHP 5.3
* @ Decoder version : 1.0.0.7
* @ Author     : DoraemonPT
* @ Release on : 09.05.2014
* @ Website    : http://EasyToYou.eu
*
**/

	class View {
		var $template = null;
		protected $vars = array();
		protected $blocks = array();
		protected $result = array( 'info' => '', 'content' => '' );

		private static $instance = null;

		
		public static function getInstance() {
			if (null === self::$instance) {
				self::$instance = new self();
			}

			 return self::$instance;
		}

		function __construct() 
		{
			//
		}

		function __clone() {
		}

		function __wakeup() {
		}

		function SetViewPath($path) {
			global $l2cfg;

			$this->path = ROOT_DIR . 'templates' . DS . $path . DS;
			$this->tpldir = $l2cfg['siteurl'] . '/templates/' . $path;
		}

		function SetViewPathAdmin() {
			$this->path = PATHDIR . 'skin' . DS;
			$this->tpldir = TPLDIR;
		}
		
		function LoadView($tpl_name) {
			global $l2cfg;

			if (empty( $tpl_name )) {
				exit( 'Не указано имя шаблона' );
			}
			
			if (!file_exists( $this->path . $tpl_name . '.tpl' )) {
				
				exit('Невозможно загрузить шаблон: ' . $this->path . $tpl_name . '.tpl' );
			}			
			
			$this->template = file_get_contents( $this->path . $tpl_name . '.tpl' );

			if (strpos( $this->template, '{show=' ) !== false) {
				$this->template = preg_replace( '#{show=(.*?)}(.*?){/show}#ies', '$this->PageBlock(\'\1\',\'\2\')', $this->template );
			}


			if (strpos( $this->template, '{hide=' ) !== false) {
				$this->template = preg_replace( '#{hide=(.*?)}(.*?){/hide}#ies', '$this->PageBlock(\'\1\',\'\2\', false)', $this->template );
			}


			if (strpos( $this->template, '{include=' ) !== false) {
				$this->template = preg_replace( '#{include=(.+?)}#ies', '$this->LoadModule(\'\1\')', $this->template );
			}

			if (SCRIPT == 'index') {
				$this->Set( 'url', $l2cfg['siteurl'] );
				$this->Set( 'template', $this->tpldir );
			}


			if (SCRIPT == 'admin') {
				$this->Set( 'url', HTTP_HOME_URL );
				$this->Set( 'template', $this->tpldir );
			}

		}

		function PageBlock($apps, $content, $flag = true) {
			global $app;

			explode( ',', $apps );
			$apps = $app = ($app == '' ? 'main' : $app);

			if (( $flag && in_array( $app, $apps ) )) {
				return $content;
			}


			if (( !$flag && !in_array( $app, $apps ) )) {
				return $content;
			}

		}

		function LoadModule($file_name) 
		{
			$file_info = pathinfo( strtolower( $file_name ) );
			$ext = $file_info['extension'];
			$file_name = $file_info['basename'];

			if ($ext == 'php') 
			{
				if (!file_exists( MODULEDIR . $file_name )) {
					return 'Файл <b>module/' . $file_name . '</b> не найден.';
				}

				ob_start();
				include( MODULEDIR . $file_name );
				return ob_get_clean();
			}

			return 'Для подключения допускаются только файлы с расширением .php';
		}

		function Set($name, $var) {
			if ( is_array( $var ) && count( $var ) ) 
			{
				foreach ($var as $key => $key_var) {
					//$key_var = '';
					//$key = '';
					$this->Set( $key, $key_var );
				}

				return null;
			}

			$this->vars['{' . $name . '}'] = $var;
		}

		function Block($name, $flag = true, $data = null) {
			$name = '\'\[' . $name . '\](.*?)\[/' . $name . '\]\'si';
			$flag = ($flag ? '\1' : ($data ? $data : ''));
			$this->blocks[$name] = $flag;
		}

		function Build($tpl) {

			foreach ($this->vars as $key_find => $key_replace) {
				$find[] = $key_find;
				$replace[] = $key_replace;
			}

			//echo '<pre>debug:<br>';
			//print_r( array( $find, $replace ) );
			//echo '</pre>';
			
			$result = str_replace( $find, $replace, $this->template );

			if (count( $this->blocks )) {
				foreach ($this->blocks as $key_find=>$key_replace) {
					$find_preg[] = $key_find;
					$replace_preg[] = $key_replace;
				}

				$result = preg_replace( $find_preg, $replace_preg, $result );
			}
			if ( $tpl == "index" )
			{
			}
			if ( isset( $this->result[$tpl] ) )
			{
				$this->result[$tpl] .= $result;
			}
			else
			{
				$this->result[$tpl] = $result;
			}

				
				$this->ClearLow(  );
		}

		function GetResult($name, $clear = false) {
			$res = (isset( $this->result[$name] ) ? $this->result[$name] : '');

			if (( !empty( $$res ) && $clear )) {
				unset( $this->result[$name] );
			}

			return $res;
		}

		function SetResult($name, $data = '', $overwrite = false) {
			if (( $overwrite || !isset( $this->result[$name] ) )) {
				$this->result[$name] = $data;
				return null;
			}
			
			$this->result[ $name ] .= $data;
		}

		function Display($name) {
			echo $this->result[$name];
			$this->ClearHigh(  );
		}

		function ShowError($title = '', $message = '', $type_err = true) {
			$class = $type_err ? 'error' : 'noerror';

			if (!empty( $message )) 
			{
				$this->LoadView( 'error' );
				$this->Set( '', array( 'title' => $title, 'message' => $message, 'class' => $class ) );
				$this->Build( 'info' );
			}

		}

		function ClearLow() {
			$this->vars = array();
			$this->blocks = array();
			$this->template = null;
		}

		function ClearHigh() {
			$this->vars = array();
			$this->blocks = array();
			$this->result = array();
			$this->template = null;
		}
	}


	if (!defined( 'STRESSWEB' )) {
		exit( 'Access denied...' );
	}

?>