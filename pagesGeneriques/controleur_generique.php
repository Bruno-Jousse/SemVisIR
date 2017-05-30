<?php
	require_once("vue_generique.php");
	require_once("modele_generique.php");
	class ControleurGenerique{
		public $modele;
		public $vue;

		public function __construct(){
			$this->modele=null;
			$this->vue=null;
		}
		
		public function getModele(){
			return $this->modele;
		} 

		public function getVue(){
			return $this->vue;
		}
	}

?>
