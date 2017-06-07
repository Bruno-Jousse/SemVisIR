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

				$exif=$this->modele->exif($img);
				$exif=$this->modele->testExif($exif);
				
				try{
					if($this->choixAffichage($vue, $img, $exif)==false){
						$this->vue->vue_erreur("L'affichage choisi ne correspond à aucun de ceux disponibles.");
						return;
					}
				} catch(ModeleRechercheException $e){
					$this->vue->vue_erreur($e);
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

		//Vérifie le mode d'affichage et récupère les images et leurs informations
		private function choixAffichage($vue, $img, $exif){

			switch($vue){
				case "Basique":
					//$this->affichageBasique($img, $exif, $imagesSim, $imagesCate, $liens);
					break;
				case "Avancee":
					//$this->affichageAvance($img, $exif, $imagesSim, $imagesCate, $liens);	
					break;
				case "Graphique":
					//$this->affichageGraphique($img, $exif, $imagesSim, $imagesCate, $liens);	
					break;
				default:
					return false;
			}

			try{
				//Récupération images.
				$imagesCategoriesEtLiens=$this->modele->getImagesCategoriesEtLiens();
				$images=$imagesCategoriesEtLiens[0];
				$categories=$imagesCategoriesEtLiens[1];
				$liens=$imagesCategoriesEtLiens[2];
				
				//Trie selon similarité
				$images=$this->modele->array_sort($images, "sim", SORT_DESC);	

			} catch(ModeleRechercheException $e){
				throw $e;
			}

			$this->vue->affichage($img, $exif, $images, $categories, $liens, $vue);

			return true;
		}
	}
?>