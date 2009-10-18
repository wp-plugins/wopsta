<?php 
/**
 * Wopsta Log
 * 
 * This is a global logger, based on FirePHP, used for debugging purposes.
 *
 * @package Wopsta
 * @since 0.1
 */
class wopstaLog {
	
	private static $firephp = null;
	
	private function __construct() {
		ob_start();
		include_once(dirname(__FILE__).'/FirePHPCore/FirePHP.class.php');
        $this->firephp = FirePHP::getInstance(true);
		$this->firephp->warn("FirePHP is active. Disable in production environment!");
    }
	
    public static function getInstance() {
		if(self::$firephp == null) {
			self::$firephp = new self;
		}
		return self::$firephp;
	}
	
	public function error($what) {
		$this->firephp->error($what);
	}
	
	public function info($what) {
		$this->firephp->info($what);
	}
	
	public function log($what) {
		$this->firephp->log($what);
	}
	
	public function warn($what) {
		$this->firephp->warn($what);
	}
}
?>