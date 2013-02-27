<?php 

/*
Plugin Name: RDPano
Description: Intégration de panorama krpano, panotour® et panoStudio
Author: RD-Création - Roland Dufour
Version: 0.5
Author URI: http://rdpano.rd-creation.fr/
*/

new RDPano();

class RDPano
{
	const VERSION = '0.5';
	static $RDPanoInst = 0;
	static $instances = array();
	
	function RDPano(){
		$this->initOptions();
		$this->checkMobileDatas();
		
		add_shortcode('rdpano', array($this, 'shortcode'));
		// Compatibilité avec PanoPress
		if ($this->options['rdpano_panopress'] == '1'){
			add_shortcode('pano', array($this, 'shortcode'));
		}
		
		wp_register_style('rdpano', plugins_url('', __FILE__) . '/rdpano.css', self::VERSION);
		
		if ($this->options['rdpano_panostudio'] != '1'){
			wp_register_script('rdpano', plugins_url('', __FILE__) . '/krpano.js', self::VERSION);
		}
		
		add_action('admin_menu', array($this, 'addOptionsPage'));
		add_action('init', array($this, 'initTinyMce'));
	}
	
	function initOptions(){
		if (isset($_POST['rdpano_title'], $_POST['rdpano_width'], $_POST['rdpano_height'])){
			update_option('rdpano_title', trim((string)$_POST['rdpano_title']));
			update_option('rdpano_width', (int)$_POST['rdpano_width']);
			update_option('rdpano_height', (int)$_POST['rdpano_height']);
			update_option('rdpano_global_swf', trim(trim((string)$_POST['rdpano_global_swf']), '/'));
			update_option('rdpano_panopress', ($_POST['rdpano_panopress'] == '1') ? '1' : '0');
			update_option('rdpano_panostudio', ($_POST['rdpano_panostudio'] == '1') ? '1' : '0');
		}

		$this->options = array(
			'rdpano_title'      => get_option('rdpano_title', 'Cliquez pour voir le panorama'),
			'rdpano_width'      => get_option('rdpano_width'),
			'rdpano_height'     => get_option('rdpano_height'),
			'rdpano_global_swf' => get_option('rdpano_global_swf'),
			'rdpano_panopress'  => get_option('rdpano_panopress', '0'),
			'rdpano_panostudio' => get_option('rdpano_panostudio', '0')
		);
	}
	
	function initTinyMce(){
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages')){
			return;
		}
		if (get_user_option('rich_editing') == 'true'){
			add_filter('mce_external_plugins', array($this, 'initTinyMce_AddPlugin'));
			add_filter('mce_buttons', array($this, 'initTinyMce_AddBtn'));
		}
	}
	function initTinyMce_AddBtn($buttons){
		array_push($buttons, "separator", "rdpano");
		return $buttons;
	}
	function initTinyMce_AddPlugin($plugin_array){
		$plugin_array['rdpano'] = plugins_url('', __FILE__).'/tinymce/editor_plugin.js';
		return $plugin_array;
	}
	
	function addOptionsPage() {
		add_options_page('Configuration RDPano', 'RDPano', 8, __FILE__, array($this, 'options_page'));
	}
	function options_page(){
		$formAction = $_SERVER['REQUEST_URI'];
		include (dirname(__FILE__) . '/admin.php');
	}
	
	function shortcode($atts, $content = null){
		$html = PHP_EOL.'<!-- '.__CLASS__.' '.self::VERSION.' -->'.PHP_EOL;
		
		if (empty($atts['title'])){
			$atts['title'] = 'Cliquez pour voir le panorama';
		}
		wp_enqueue_style('rdpano');
		wp_enqueue_script('rdpano');
		
		// Compatibilité mini-attributs
		$this->miniAtts($atts, 'f', 'file');
		$this->miniAtts($atts, 'w', 'width');
		$this->miniAtts($atts, 'h', 'height');
		$this->miniAtts($atts, 'p', 'preview');
		$this->miniAtts($atts, 't', 'title');
		
		$id = 'rdpano'.self::$RDPanoInst++;
		$pano = new RDPano_Inst($id, $this, $atts);
		self::$instances[] = $pano;
		$html .= $pano->getPlayer();
		
		$html .= PHP_EOL.'<!-- /'.__CLASS__.' '.self::VERSION.' -->'.PHP_EOL;
		
		return $html;
	}
	function miniAtts(&$atts, $mini, $full){
		if (!isset($atts[$full]) && isset($atts[$mini])){
			$atts[$full] = $atts[$mini];
		}
	}
	function isMobile(){
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (preg_match('/(ipad|ipod|iphone|android|opera mini|blackberry|pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine|iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i', $ua)){
			return true;
		}
		else {
			$st = array(1207,"3gso","4thp","501i","502i","503i","504i","505i","506i",6310,6590,"770s","802s","a wa","acer","acs-","airn","alav","asus","attw","au-m","aur ","aus ","abac","acoo","aiko","alco","alca","amoi","anex","anny","anyw","aptu","arch","argo","bell","bird","bw-n","bw-u","beck","benq","bilb","blac","c55\/","cdm-","chtm","capi","cond","craw","dall","dbte","dc-s","dica","ds-d","ds12","dait","devi","dmob","doco","dopo","el49","erk0","esl8","ez40","ez60","ez70","ezos","ezze","elai","emul","eric","ezwa","fake","fly-","fly_","g-mo","g1 u","g560","gf-5","grun","gene","go.w","good","grad","hcit","hd-m","hd-p","hd-t","hei-","hp i","hpip","hs-c","htc ","htc-","htca","htcg","htcp","htcs","htct","htc_","haie","hita","huaw","hutc","i-20","i-go","i-ma","i230","iac","iac-","iac\/","ig01","im1k","inno","iris","jata","java","kddi","kgt","kgt\/","kpt ","kwc-","klon","lexi","lg g","lg-a","lg-b","lg-c","lg-d","lg-f","lg-g","lg-k","lg-l","lg-m","lg-o","lg-p","lg-s","lg-t","lg-u","lg-w","lg\/k","lg\/l","lg\/u","lg50","lg54","lge-","lge\/","lynx","leno","m1-w","m3ga","m50\/","maui","mc01","mc21","mcca","medi","meri","mio8","mioa","mo01","mo02","mode","modo","mot ","mot-","mt50","mtp1","mtv ","mate","maxo","merc","mits","mobi","motv","mozz","n100","n101","n102","n202","n203","n300","n302","n500","n502","n505","n700","n701","n710","nec-","nem-","newg","neon","netf","noki","nzph","o2 x","o2-x","opwv","owg1","opti","oran","p800","pand","pg-1","pg-2","pg-3","pg-6","pg-8","pg-c","pg13","phil","pn-2","pt-g","palm","pana","pire","pock","pose","psio","qa-a","qc-2","qc-3","qc-5","qc-7","qc07","qc12","qc21","qc32","qc60","qci-","qwap","qtek","r380","r600","raks","rim9","rove","s55\/","sage","sams","sc01","sch-","scp-","sdk\/","se47","sec-","sec0","sec1","semc","sgh-","shar","sie-","sk-0","sl45","slid","smb3","smt5","sp01","sph-","spv ","spv-","sy01","samm","sany","sava","scoo","send","siem","smar","smit","soft","sony","t-mo","t218","t250","t600","t610","t618","tcl-","tdg-","telm","tim-","ts70","tsm-","tsm3","tsm5","tx-9","tagt","talk","teli","topl","hiba","up.b","upg1","utst","v400","v750","veri","vk-v","vk40","vk50","vk52","vk53","vm40","vx98","virg","vite","voda","vulc","w3c ","w3c-","wapj","wapp","wapu","wapm","wig ","wapi","wapr","wapv","wapy","wapa","waps","wapt","winc","winw","wonu","x700","xda2","xdag","yas-","your","zte-","zeto","aste","audi","avan","blaz","brew","brvw","bumb","ccwa","cell","cldc","cmd-","dang","eml2","fetc","hipt","http","ibro","idea","ikom","ipaq","jbro","jemu","jigs","keji","kyoc","kyok","libw","m-cr","midp","mmef","moto","mwbp","mywa","newt","nok6","o2im","pant","pdxg","play","pluc","port","prox","rozo","sama","seri","smal","symb","tosh","treo","upsi","vx52","vx53","vx60","vx61","vx70","vx80","vx81","vx83","vx85","wap-","webc","whit","wmlb","xda-"); 
			$uaSt = substr($ua, 0, 4);
			if (in_array($uaSt, $st)){
				return true;
			}
		}
		return false;
	}
	function checkMobileDatas(){
		if (isset($_POST['fullRDPano']) && $_POST['fullRDPano'] == '1'){
			unset($_POST['fullRDPano']);
			$jsonA = array();
			foreach ($_POST as $k => $v){
				if (is_scalar($v)){
					$jsonA[$k] = $v;
				}
			}
			$jsonA['target'] = 'container';
			$jsonA['width'] = $jsonA['height'] = '100%';
			$json  = json_encode($jsonA);
			$src   = plugins_url('', __FILE__);
			$title = (!empty($jsonA['title'])) ? $jsonA['title'] : 'Panorama - Vue mobile par RDPano';
			
			
			if (isset($_POST['panostudio']) && $_POST['panostudio'] == '1'){
				$el = 'document.getElementById(\''.$_POST['target'].'\')';
				if (!isset($_POST['xml'])){
					$_POST['xml'] = preg_replace('`\.swf$`i', '.xml', $_POST['swf']);
				}
				$_POST['xml'] = plugins_url('', __FILE__).'/xml.php?xml='.$_POST['xml'];
				$html = '<object classid="CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$_POST['width'].'" height="'.$_POST['height'].'" id="" codebase="http://active.macromedia.com/flash9/cabs/swflash.cab#version=9,0,28,0"><param name="movie" value="'.$src.'/panoStudioViewer.swf" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="allowFullScreen" value="true" /><param name="FlashVars" value="pano='.$_POST['xml'].'" /><embed src="'.$src.'/panoStudioViewer.swf" width="'.$_POST['width'].'" height="'.$_POST['height'].'" type="application/x-shockwave-flash" name="" allowScriptAccess="always" allowNetworking="all" allowFullScreen="true" FlashVars="pano='.$_POST['xml'].'" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed></object>';
				$script  = '<script type="text/javascript">//<![CDATA[
  '.$el.'.innerHTML = \''.str_replace('"', '&quot;', str_replace('\'', '', $html)).'\'.replace(/&quot;/g, String.fromCharCode(34));
  //]]></script>';
			} 
			
			else {
				$onclick = 'embedpano('.str_replace('"', '\'', str_replace('\'', '&amp;apos;', json_encode($datas))).'); '.$el.'.style.background = \'\'; '.$el.'.removeAttribute(\'onclick\');';
				
				$script = '<script type="text/javascript" src="'.$src.'/krpano.js"></script>
  <script type="text/javascript">//<![CDATA[
  embedpano('.$json.');
  //]]></script>';
			}
			
			echo <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
  <title>$title</title>
  <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <meta name="apple-mobile-web-app-capable" content="yes"/>
  <style type="text/css">
	@media only screen and (min-device-width: 800px) { html { overflow:hidden; } }
    * { padding: 0; margin: 0; }
    html { height: 100%; }
    body { height: 100%; overflow:hidden; margin: 0; padding: 0; }
    #container { height: 100%; min-height: 100%; width: 100%; margin: 0 auto; }
  </style>
</head>
<body>
  <div id="container"></div>
  $script
  </body>
</html>
HTML;
			exit;
		}
	}
}
	
	
class RDPano_Inst	
{
	var $id;
	var $inst;
	var $options = array();
	function RDPano_Inst($id, $inst, $options){
		$this->id      = $id;
		$this->inst    = $inst;
		$this->options = $options;
	}
	function getPlayer(){
		if (empty($this->options['file'])){
			return '';
		} 
		
		$datas = array('target' => $this->id, 'swf' => $this->options['file']);
		if (preg_match('/\.xml$/i', $this->options['file'])){
			$datas['xml'] = $this->options['file'];
			if (!empty($this->options['swf'])){
				$datas['swf'] = $this->options['swf'];
			} 
			else if (!empty($this->inst->options['rdpano_global_swf'])){
				$datas['swf'] = $this->inst->options['rdpano_global_swf'];
			}
			else {
				$datas['swf'] = preg_replace('/\.xml$/i', '.swf', $this->options['file']);
			}
		}
		if (isset($this->options['panostudio']) && $this->options['panostudio'] == '1'){
			$datas['panostudio'] = '1';
		}
		
		$styles = array();
		$w = (!empty($this->options['width'])) ? $this->options['width'] : $this->inst->options['rdpano_width'];
		if (!empty($w)){
			$datas['width'] = $w.(is_numeric($w) ? 'px' : '');
			$styles[] = 'width: '.$datas['width'].';';
		} 
		$h = (!empty($this->options['height'])) ? $this->options['height'] : $this->inst->options['rdpano_height'];
		if (!empty($h)){
			$datas['height'] = $h.(is_numeric($h) ? 'px' : '');
			$styles[] = 'height: '.$datas['height'].';';
		}
		$t = (!empty($this->options['title'])) ? $this->options['title'] : $this->inst->options['rdpano_title'];
		if (!empty($t)){
			$datas['title'] = $t;
		}
		
		$preview = (!empty($this->options['preview'])) ? $this->options['preview'] : null;
		$withPreview = (null !== $preview); 
		if ($withPreview){
			$styles[] = 'background-image: url(\''.str_replace('\'', '', $preview).'\');';
		}
		
		$content = '<img src="'.plugins_url('', __FILE__).'/play.png" class="rdpano_play" alt="" />';
		if (RDPano::isMobile()){
			// Pas de preview
			if (!$withPreview && isset($datas['title'])){
				$content .= $t;
			}
			$content .= '<form action="./" method="post" id="frm_'.$this->id.'" target="_blank">';
			$content .= '<input type="hidden" name="fullRDPano" value="1" />';
			foreach ($datas as $k => $v){
				$content .= '<input type="hidden" name="'.str_replace('"', '', $k).'" value="'.str_replace('"', '', $v).'" />';
			}
			$content .= '</form>';
			$onclick = 'document.getElementById(\'frm_'.$this->id.'\').submit();';
		} 
		else {
			$el = 'document.getElementById(\''.$this->id.'\')';
			if (isset($this->options['panostudio']) && $this->options['panostudio'] == '1'){
				if (!isset($datas['xml'])){
					$datas['xml'] = preg_replace('`\.swf$`i', '.xml', $datas['swf']);
				}
				$datas['xml'] = plugins_url('', __FILE__).'/xml.php?xml='.$datas['xml'];
				$html = '<object classid="CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$datas['width'].'" height="'.$datas['height'].'" id="" codebase="http://active.macromedia.com/flash9/cabs/swflash.cab#version=9,0,28,0"><param name="movie" value="'.plugins_url('', __FILE__).'/panoStudioViewer.swf" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="allowFullScreen" value="true" /><param name="FlashVars" value="pano='.$datas['xml'].'" /><embed src="'.plugins_url('', __FILE__).'/panoStudioViewer.swf" width="'.$datas['width'].'" height="'.$datas['height'].'" type="application/x-shockwave-flash" name="" allowScriptAccess="always" allowNetworking="all" allowFullScreen="true" FlashVars="pano='.$datas['xml'].'" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed></object>';
				$onclick = $el.'.innerHTML = \''.str_replace('"', '&quot;', str_replace('\'', '', $html)).'\'.replace(/&quot;/g, String.fromCharCode(34));';
			} else {
				$onclick = 'embedpano('.str_replace('"', '\'', str_replace('\'', '&amp;apos;', json_encode($datas))).'); '.$el.'.style.background = \'\'; '.$el.'.removeAttribute(\'onclick\');';
			}
		}
		$b = '<div id="'.$this->id.'" class="rdpano" title="'.$t.'" style="'.implode(' ', $styles).'"'.($withPreview ? ' onclick="'.$onclick.'"' : '').'>'.$content.'</div>';
		// Pas de preview mais pas depuis un mobile
		if (!$withPreview && !RDPano::isMobile()){
			$b .= '<script type="text/javascript" src="'.plugins_url('', __FILE__).'/krpano.js"></script><script type="text/javascript">//<![CDATA['.PHP_EOL . $onclick . PHP_EOL.'//]]></script>';
		}
		return $b;
	}
}