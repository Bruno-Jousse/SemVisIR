<?php
	require_once("controleur_recherche.php");
	require_once("modele_recherche_exception.php");
		
	class ModRecherche extends ModuleGenerique{ 
			
		public function __construct(){
			$this->controleur=new ControleurRecherche();
			$this->controleur->main();	
		}
	}
?>
