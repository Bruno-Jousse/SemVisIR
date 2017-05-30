<?php
	require_once("modele_recherche.php");
	require_once("vue_recherche.php");
	
	class ControleurRecherche extends ControleurGenerique{
			
		public function main(){
			$this->vue=new VueRecherche();
			$this->modele=new ModeleRecherche();

			if(isset($_POST["algo"]) && isset($_POST["affichage"]) && isset($_FILES["file"])){
				$img=$_FILES["file"];

				$algo=htmlspecialchars($_POST["algo"]);
				$vue=htmlspecialchars($_POST["affichage"]);		

				if($this->testImg($img)==false){
					$this->vue->vue_erreur("Une erreur est survenue lors de la réception de l'image, elle doit être  inférieure à 4Mo et avoir un nom valide. Vérifiez aussi la validité de son extension (.png, .jpg, .jpeg ou .gif).");
					return;
				}

				if($this->choixAlgo($algo)===false){
					$this->vue->vue_erreur("L'algorithme choisi ne correspond à aucun de ceux disponibles.");
					return;
				}	

				$exif=$this->getExif($img);
				$exif=$this->testExif($exif);
				
				if($this->choixAffichage($vue, $img, $exif)==false){
					$this->vue->vue_erreur("L'affichage choisi ne correspond à aucun de ceux disponibles.");
					return;
				}
				
			}
			else{
				$this->vue->vue_erreur("Vous avez mal rempli le formulaire, veuillez retourner à la page d'accueil.");

			}
		}

		public function testImg($img){
			$maxsize=4194304;
			$extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );

			$extension_upload = strtolower(substr(strrchr($img['name'], '.'), 1));
			
			if ($img['error'] > 0 || $img['size']>$maxsize || !in_array($extension_upload, $extensions_valides) || mb_strlen($img['name'],"UTF-8") > 225 || !preg_match("`^[-0-9A-Z_\.]+$`i",$img['name'])) {
				return false;
			}
			return true;
		}

		//Appelle l'algo permettant de créer les fichiers textes.
		private function choixAlgo($algo){
			switch($algo){
				case "Basique":
					//algo
					break;
          		case "VP":
				  	//algo
					break;
          		case "SVP":
				  	//algo
					break;
          		case "SCVP":
					//algo
					break;
          		case "Patron":
				  	//algo
					break;          
				case "Cible":
					//algo
					break;
				default:
					return false;
			}

			return true;
		}

		private function getExif($img){
			return $this->modele->exif($img);
		}

		//Sélectionne le bon mode d'affichage
		private function choixAffichage($vue, $img, $exif){
			switch($vue){
				case "Basique":
					$this->rechercheBasique($img, $exif);
					break;
				case "Avancee":
					$this->rechercheAvancee($img, $exif);
					break;
				case "Graphique":
					$this->rechercheGraphique($img, $exif);
					break;
				default:
					return false;
			}
			return true;
		}

		public function testExif($exif){

      		if(!isset($exif["COMPUTED"]["UserComment"])) 
        		$exif["COMPUTED"]["UserComment"]="";

      		if(!isset($exif["GPS"]["GPSLongitude"]) || !isset($exif["GPS"]['GPSLongitudeRef']) || !isset($exif["GPS"]["GPSLatitude"]) || !isset($exif["GPS"]['GPSLatitudeRef'])){
        		$exif["GPS"]["GPSLongitude"]=null;
        		$exif["GPS"]["GPSLongitudeRef"]=null;
        		$exif["GPS"]["GPSLatitude"]=null;
        		$exif["GPS"]["GPSLatitudeRef"]=null;
     		 }

			if(!isset($exif["FILE"]["FileSize"])){
			  $exif["FILE"]["FileSize"]=null;
			}

			if(!isset($exif["DateTimeOriginal"])){
				$exif["DateTimeOriginal"]=null;
			}
			return $exif;
		}
		
		private function rechercheBasique($img, $exif){
			try{
				$images=$this->modele->getImagesSim();
			} catch(ModeleRechercheException $e){
				$this->vue->vue_erreur($e);
				return;
			}

			$images=array_values($images); 
			$images=$this->modele->array_sort($images, "sim", SORT_DESC);		
			$this->vue->basique($img, $exif, $images);
		}

		private function rechercheAvancee($img, $exif){
			try{
				$images=$this->modele->getImagesCat();
			} catch(ModeleRechercheException $e){
				$this->vue->vue_erreur($e);
			}

			foreach($images as $categories => $categorie){
				$images[$categories]=$this->modele->array_sort($categorie, "sim", SORT_DESC);
			}
			$this->vue->avancee($img, $exif, $images);
		}

		private function rechercheGraphique($img, $exif){
			try{
				$imagesEtLiens=$this->modele->getImagesEtLiens();
			} catch(ModeleRechercheException $e){
				$this->vue->vue_erreur($e);
			}
			$images=$imagesEtLiens[0];
			$liens=$imagesEtLiens[1];
			$this->vue->graphique($img, $exif, $images, $liens);
		}
	}
?>