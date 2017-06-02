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

		private function getExif($img){
			return $this->modele->exif($img);
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
				$imagesEtLiens=$this->modele->getImagesEtLiens();
				$imagesCate=$imagesEtLiens[0];
				$liens=$imagesEtLiens[1];
				$imagesSim=array();

				//Récupération images sans catégorie pour l'affichage basique
				foreach($imagesCate as $key => $images){
					for($i=0; $i<count($images); $i++){
						$imagesSim[$i]=($images[$i]);
					}
				}

				//Trie selon similarité
				$imagesSim=$this->modele->array_sort($imagesSim, "sim", SORT_DESC);		
				foreach($imagesCate as $categories => $categorie){
					$imagesCate[$categories]=$this->modele->array_sort($categorie, "sim", SORT_DESC);
				}

			} catch(ModeleRechercheException $e){
				throw $e;
			}

			$this->vue->affichage($img, $exif, $imagesSim, $imagesCate, $liens, $vue);

			return true;
		}

		public function testExif($exif){
			$newExif=array();

			if(!isset($exif["GPS"]["GPSLongitude"]) || !isset($exif["GPS"]['GPSLongitudeRef']) || !isset($exif["GPS"]["GPSLatitude"]) || !isset($exif["GPS"]['GPSLatitudeRef'])){
				$newExif["location"]="";
     		 }
			else{
				$longitude=$this->getGps($exif["GPS"]["GPSLongitude"], $exif["GPS"]["GPSLongitudeRef"]);
				$latitude=$this->getGps($exif["GPS"]['GPSLatitude'], $exif["GPS"]['GPSLatitudeRef']);

				$newExif["location"]='longitude='.$longitude.', latitude='.$latitude;
			}

			$texte="";
			if(isset($exif["EXIF"]["UserComment"])) {
				$texte.=$exif["EXIF"]["UserComment"]."\n";
			}

			if(isset($exif["IMAGE"]["ImageDescription"])){
				$texte.=$exif["IMAGE"]["ImageDescription"];
			}

			$newExif["texte"]=$texte;

			if(!isset($exif["FILE"]["FileSize"])){
			  	$newExif["taille"]="";
			}
			else{
				$tailleKo=$exif["FILE"]["FileSize"]/1024;
				$tailleKo.=" Ko";
				$newExif["taille"]=$tailleKo;
			}

			if(!isset($exif["EXIF"]["DateTimeOriginal"])){
				$newExif["date"]="";
			}
			else{
				$newExif["date"]=$this->transformDate($exif["EXIF"]["DateTimeOriginal"]);
			}
      		
			return $newExif;
		}
		
		public function getGps($exifCoord, $hemi) {

			$degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
			$minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
			$seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;

			$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

			return $flip * ($degrees + $minutes / 60 + $seconds / 3600);

		}

		public function gps2Num($coordPart) {

			$parts = explode('/', $coordPart);

			if (count($parts) <= 0)
				return 0;

			if (count($parts) == 1)
				return $parts[0];

			return floatval($parts[0]) / floatval($parts[1]);
		}

		public function transformDate($date){
			$exploded=explode(" ", $date);
			$explodedDate=explode(":", $exploded[0]);

			$jour=$explodedDate[2];
			$mois=$explodedDate[1];
			$annee=$explodedDate[0];
		
			$explodedHeure=explode(":", $exploded[1]);

			$heure=$explodedHeure[0];
			$minute=$explodedHeure[1];
			$seconde=$explodedHeure[2];
		
			$dateFin="Le ".$jour."/".$mois."/".$annee." à ".$heure.":".$minute.":".$seconde;
			return $dateFin;
		}
	/*
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
			$this->affichageGraphique($img, $exif, $imagesSim, $imagesCate, $liens);
		}

		private function affichageBasique($img, $exif, $imagesSim, $imagesCate, $liens){
			$this->vue->basique($img, $exif, $imagesSim, $imagesCate, $liens);
		}

		private function affichageAvance($img, $exif, $imagesSim, $imagesCate, $liens){
			$this->vue->avancee($img, $exif, $imagesSim, $imagesCate, $liens);
		}

		private function affichageGraphique($img, $exif, $imagesSim, $imagesCate, $liens){
			$this->vue->graphique($img, $exif, $imagesSim, $imagesCate, $liens);
		}*/
	}
?>