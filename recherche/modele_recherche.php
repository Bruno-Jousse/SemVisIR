<?php			
		
	class ModeleRecherche extends ModeleGenerique{	
	    
		//Retourne la liste des métadonnées de l'image
		public function exif($img){
			//L'image étant uploader de manière temporaire, on récupère son chemin avec ["tmp_name"]
			$exif = exif_read_data($img["tmp_name"], 0, true);
			$array=array();

			if($exif==false){
				return $array;
			}

			foreach ($exif as $key => $section){  
				$array[$key]=$section;     
				foreach ($section as $name => $value){
					$array[$key][$name]= $value;
    			}
			}
			return $array;
		}
		
		//vérifie si les métadonnées sont initialisées et retourne une nouvelle array avec les métadonnées utiles
		public function testExif($exif){
			$newExif=array();

			if(!isset($exif["GPS"]["GPSLongitude"]) || !isset($exif["GPS"]['GPSLongitudeRef']) || !isset($exif["GPS"]["GPSLatitude"]) || !isset($exif["GPS"]['GPSLatitudeRef'])){
				$newExif["location"]="";
     		 }
			else{
				$longitude=$this->getGps($exif["GPS"]["GPSLongitude"], $exif["GPS"]["GPSLongitudeRef"]);
				$latitude=$this->getGps($exif["GPS"]['GPSLatitude'], $exif["GPS"]['GPSLatitudeRef']);

				$newExif["location"]=$this->coordToAdress($latitude, $longitude);
			}

			$texte="";
			if(isset($exif["EXIF"]["UserComment"])) {
				//var_dump($exif["EXIF"]["UserComment"]);
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

		//Transforme les coordonnées GPS en float
		private function getGps($exifCoord, $hemi) {

			$degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
			$minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
			$seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;

			$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

			return $flip * ($degrees + $minutes / 60 + $seconds / 3600);

		}

		private function gps2Num($coordPart) {

			$parts = explode('/', $coordPart);

			if (count($parts) <= 0)
				return 0;

			if (count($parts) == 1)
				return $parts[0];

			return floatval($parts[0]) / floatval($parts[1]);
		}

		//Requête HTTP post au serveur possédant l'algo
		//Le serveur doit retourner une liste de 3 listes : une liste de liste de: chemin de l\'image et sa valeur de similarité; une liste de liste de: chemin del\'image et ses catégories; une liste de liste de: catégorie1 et categorie2 (représentant un lien entre deux catégories) 
		public function algoRequest($algo){
			/*
			require_once('tierApp/Requests-master/library/Requests.php');

			Requests::register_autoloader();

			$options = array('auth' => array('user', 'password'), 'algo' => $algo);
			
			//(url, header, options)
			$request = Requests::post('http://siteAlgo.fr', array(), $options);

			var_dump($request);
		
			return $request;
			*/
		}

		//Transforme des coordonnées float en une adresse (nom de rue, de la ville, département...)
		private function coordToAdress($lat, $long){
			$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&key=AIzaSyCmtgjI_qHSum-_LADHNYVbzjvQrJECm9s";
    		$json = json_decode(file_get_contents($url), true);
    		$a = $json['results'][0]['formatted_address'];
			return $a;
		}
		
		//Transforme le format de la date
		private function transformDate($date){
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

		//retourne:{ 0 => { 0 => {src:"", sim:"", categories:{ 0=> "", 1=> ""}, meta:{titre:"" date:""...} }, 1 => {}... },
		// 1 => { 0 => "catégorie1", 1 => "categorie2"... }, 
		//2 => { 0 => { 0 => "sourceLien", 1 => "targetLien"}, 1 => {}... }}
		//en [0]: Array possédant les images ayant une source, une valeur de similarité, une array de metadonnées et une array de catégories, 
		//en [1] array possédant les catégories 
		//et en [2] array possédant les liens entre les catégories.
		public function getImagesCategoriesEtLiens(){
			include_once("tierApp/simple-html-dom/simple_html_dom.php");
			
			//Récupère les images, catégories et liens.
			if(!($imagesCategoriesEtLiens=$this->relation())){
				throw new ModeleRechercheException("Le fichier n'a pas pu être ouvert");
			}

			//Récupère les images et leurs similarités.
			if(!($results=$this->similarity())){
				throw new ModeleRechercheException("Le fichier n'a pas pu être ouvert");
			}

			$images=$imagesCategoriesEtLiens[0];
			$categories=$imagesCategoriesEtLiens[1];
			$liens=$imagesCategoriesEtLiens[2];
			$directory="images/ImageCLEFphoto2008/images/";
			$imagesFin=array();
			
			//On regroupe les informations des images
			foreach($images as $key => $sesCategories){
				$trouve=false;
				for($j=0; $j<count($results) && $trouve==false; $j++){
					//0=src, 1=similarity
					$infos=preg_split('/ /', $results[$j], -1, PREG_SPLIT_NO_EMPTY);
					if($infos[0]==$key){
						$trouve=true;
						
						//On ajoute ses métadonnées (annotations)
						if(!($meta=$this->meta($infos[0]))){
							throw new ModeleRechercheException("Le fichier n'a pas pu être ouvert");
						}
						$array=array("src" => $directory . $infos[0] . ".jpg", "meta" => $meta, "sim" => $infos[1], "categories" => $sesCategories);
						array_push($imagesFin, $array);
					}
				}
			}

			$imagesCategoriesEtLiens[0]=$imagesFin;
			return $imagesCategoriesEtLiens;
		}

		//Récupère les informations des images contenues dans les annotation s
		public function meta($img){

			$directory="images/ImageCLEFphoto2008/annotations/";

			//$path=preg_replace("/^[^\/]*(?:\/[^\/]*){2}\//", $directory, $img);
			//$path=preg_replace("/\.[^\/.]+$/", ".rnd", $path);

			$path=$directory.$img.".rnd";
			
			if(!($html = file_get_html($path))){
				return false;
			}

			$array=array("Title" => $html->find("TITLE", 0)->innertext, "Description" => $html->find("DESCRIPTION",0)->innertext, 
			"Notes" => $html->find("NOTES",0)->innertext, "Location" => $html->find("LOCATION",0)->innertext, "Date" => $html->find("DATE",0)->innertext);
			
			foreach($array as $key =>$value){
				$array[$key]=iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($value));
			}

			return $array;
		}
			
		// Retourne :
		//En [0] on a le nom des images qui sert de clé et qui contient ses catégories, 
		//en [1] on a les catégories 
		//et en [2] les liens entre catégories
		public function relation(){
			if(!($file=fopen("category/listesultsRelations.txt","r"))){
				return false;
			}
			$results=array();
			$categories=array();
			$images=array();
			$category="";

			while(($buffer = fgets($file) )!== false && !preg_match("/inter-category relations:/", $buffer)){

				//Nouvelle catégorie
				if(preg_match("/category :[^\n]*/", $buffer)){
					$buffer=substr($buffer,0,strlen($buffer)-1);
					$category=preg_replace("/category :/", "",$buffer);
					array_push($categories, $category);
				}

				//Image dans la catégorie
				else{
					if(substr($buffer, -1)=="\n"){
						$buffer=substr($buffer,0,strlen($buffer)-1);
					}
					if(!isset($images[$buffer])){
						$images[$buffer]=array();
					}
					array_push($images[$buffer], $category);
				}
			}

			$liens=array();

			//relations entre catégories (liens)
			if(preg_match("/inter-category relations:/", $buffer)){

				//Lien entre deux catégories (0 => source, 1 => target)
				while(($buffer = fgets($file) )!== false){
					$lien=split("has_seantic_relation", $buffer);
					$lien[0]=substr($lien[0],0,strlen($lien[0])-1);
					$lien[1]=substr($lien[1],1,strlen($lien[1])-2);
					array_push($liens, $lien);
				}
			}

			array_push($results, $images);
			array_push($results, $categories);
			array_push($results, $liens);
			fclose($file);
			return $results;
		}


		//Récupère juste les images (pas utilisé ni testé)
		public function result(){
			if(!($file=fopen("category/listResults.txt","r"))){
				return false;
			}
			$results=array();
			while(($buffer = fgets($file)) !== false){
				array_push($results, $buffer);
				var_dump($buffer);
			}
			fclose($file);
			return $results;
		}

		//Récupère les images et valeurs de similaritées
		public function similarity(){
			if(!($file=fopen("category/listResultsSimilarity.txt", "r"))){
				return false;
			}
			$results=array();
			while(($buffer = fgets($file)) !== false){
				//retirer le \n
				if(substr($buffer, -1)=="\n"){
					$buffer=substr($buffer,0,strlen($buffer)-1);
				}
				array_push($results, $buffer);
			}
	
			fclose($file);
			return $results;
		}

		//Trie l'array selon les valeurs dans la clé $on
		public function array_sort($array, $on, $order=SORT_ASC)
		{
			$new_array = array();
			$sortable_array = array();

			if (count($array) > 0) {
				foreach ($array as $k => $v) {
					if (is_array($v)) {
						foreach ($v as $k2 => $v2) {
							if ($k2 == $on) {
								array_push($sortable_array, $v2);
							}
						}
					} 
					else {
						array_push($sortable_array, $v);
					}
				}
				switch ($order) {
					case SORT_ASC:
						asort($sortable_array);
					break;
					case SORT_DESC:
						arsort($sortable_array);
					break;
				}
				foreach ($sortable_array as $k => $v) {
					array_push($new_array, $array[$k]);
				}
			}
			return $new_array;
		}
	}
?>
