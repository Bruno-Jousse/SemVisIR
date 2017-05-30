<?php
	class VueGenerique{
		public $contenu;
		public $titre;
		
		public function __construct(){
			$this->contenu="";
			$this->titre="";
			ob_start();
		}
		
		public function tamponVersContenu(){	
			$this->contenu=$this->contenu . ob_get_clean();
		}

		public function vue_erreur($message){						
			$this->contenu='<div class="col-md-offset-3 col-md-6" id = errMessage > 
						<h1><span class="glyphicon glyphicon-remove"></span> Une erreur est survenue :</h1> <br/> 
							<br/><br/>
							<p> <font size = 5px color="red">' . $message . '</font> </p>
							</br> 
							<a href="index.php?module=accueil"> 
								<input type="button" value="Accueil"> 
							</a>
					</div>';
			$this->titre="Erreur";
		}
		
		public function vue_confirm($message){

			$this->contenu.='<div class="col-md-offset-3 col-md-6" id = confirmMessage > 
						<h1><span class="glyphicon glyphicon-ok"></span> Operation réussie ! </h1> <br/>
							<br/><br/>
							<p> <font size = 5px color="green">' . $message . '</font> </p>
							</br> 
							<a href="index.php?module=accueil"> 
								<input type="button" value="Accueil"> 
							</a>
					</div>';
			$this->titre="Confirmation";
		}
		
		public function getContenu(){
			return $this->contenu;
		} 

		public function getTitre(){
			return $this->titre;
		} 
	}
?>
