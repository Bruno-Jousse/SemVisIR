	<?php 
	require_once("controleur_generique.php");	
	require_once("modele_generique_exception.php");
	
	class ModuleGenerique{

		public $controleur;

		public function __construct(){
			$this->controleur=null;
		}

		public function getControleur(){
			return $this->controleur;
		}	
			
	}
?>
