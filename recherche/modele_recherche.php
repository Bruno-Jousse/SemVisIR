<?php			
		
	class ModeleRecherche extends ModeleGenerique{	
	    
		public function exif($img){
 	 	    //$imageData = file_get_contents($img["tmp_name"]);
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

		//Affichage basique
		public function getImagesSim(){
			//Contient la fonction file_get_html
			include_once("tierApp/simple-html-dom/simple_html_dom.php");
			
			if(!($results=$this->similarity())){
				throw new ModeleRechercheException("Le fichier n'a pas pu être ouvert");
			}

			$directory="images/ImageCLEFphoto2008/images/";
			$images=array();

			for($i=0; $i<count($results); $i++){
				//0=src, 1=similarity
				//$infos=explode(" ", $results[$i]);
				$infos=preg_split('/ /', $results[$i], -1, PREG_SPLIT_NO_EMPTY);
				if(!($meta=$this->meta($infos[0]))){
					throw new ModeleRechercheException("Le fichier n'a pas pu être ouvert");
				}
				array_push($images, array("src" => $directory.$infos[0].".jpg", "meta" => $meta, "sim" => $infos[1]));
			}
			return $images;
		}

		//Affichage avancée
		public function getImagesCat(){
			include_once("tierApp/simple-html-dom/simple_html_dom.php");
			
			if(!($categories=$this->relation()[0])){
				throw new ModeleRechercheException("Le fichier n'a pas pu être ouvert");
			}
			if(!($results=$this->similarity())){
				throw new ModeleRechercheException("Le fichier n'a pas pu être ouvert");
			}
			$directory="images/ImageCLEFphoto2008/images/";
			$images=array();

			foreach($categories as $key => $categorie){
				$images[$key]=array();
				for($b=0; $b<count($categorie); $b++){
					$trouve=false;
					for($i=0; $i<count($results) && $trouve==false; $i++){
						//0=src, 1=similarity
						$infos=preg_split('/ /', $results[$i], -1, PREG_SPLIT_NO_EMPTY);
						if($infos[0]==$categorie[$b]){
							$trouve=true;
							if(!($meta=$this->meta($infos[0]))){
								throw new ModeleRechercheException("Le fichier n'a pas pu être ouvert");
							}
							$array=array("src" => $directory . $infos[0] . ".jpg", "meta" => $meta, "sim" => $infos[1]);
							//var_dump($images[$key]);
							array_push($images[$key], $array);
						}
					}
				}
			}
			return $images;
		}

		//Affichage graphique
		public function getImagesEtLiens(){
			include_once("tierApp/simple-html-dom/simple_html_dom.php");
			
			if(!($imagesEtLiens=$this->relation())){
				throw new ModeleRechercheException("Le fichier n'a pas pu être ouvert");
			}			if(!($results=$this->similarity())){
				throw new ModeleRechercheException("Le fichier n'a pas pu être ouvert");
			}
			$categories=$imagesEtLiens[0];
			$liens=$imagesEtLiens[1];
			$directory="images/ImageCLEFphoto2008/images/";
			$images=array();

			foreach($categories as $key => $categorie){
				$images[$key]=array();
				for($b=0; $b<count($categorie); $b++){
					$trouve=false;
					for($i=0; $i<count($results) && $trouve==false; $i++){
						//0=src, 1=similarity
						$infos=preg_split('/ /', $results[$i], -1, PREG_SPLIT_NO_EMPTY);
						if($infos[0]==$categorie[$b]){
							$trouve=true;
							if(!($meta=$this->meta($infos[0]))){
								throw new ModeleRechercheException("Le fichier n'a pas pu être ouvert");
							}
							$array=array("src" => $directory . $infos[0] . ".jpg", "meta" => $meta, "sim" => $infos[1]);
							//var_dump($images[$key]);
							array_push($images[$key], $array);
						}
					}
				}
			}
			$imagesEtLiens[0]=$images;
			return $imagesEtLiens;
		}

		//Récupère les informations des images
		public function meta($img){

			$directory="images/ImageCLEFphoto2008/annotations/";

			//$path=preg_replace("/^[^\/]*(?:\/[^\/]*){2}\//", $directory, $img);
			//$path=preg_replace("/\.[^\/.]+$/", ".rnd", $path);

			$path=$directory.$img.".rnd";
			
			if(!($html = file_get_html($path))){
				return false;
			}

			$array=array("Title" => $html->find("TITLE", 0)->innertext, "Description" => $html->find("DESCRIPTION",0)->innertext, "Notes" => $html->find("NOTES",0)->innertext, "Location" => $html->find("LOCATION",0)->innertext, "Date" => $html->find("DATE",0)->innertext);
			
			foreach($array as $key =>$value){
				$array[$key]=iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($value));
			}

			return $array;
		}


		//Récupère les catégories et les images
		//Retourne une array avec en 0 les categories et en 1 les liens
		public function relation(){
			if(!($file=fopen("category/listesultsRelations.txt","r"))){
				return false;
			}
			$results=array();
			$images=array();
			$i=0;
			$category="";
			$buffer=false;
			while(($buffer = fgets($file) )!== false && !preg_match("/inter-category relations:/", $buffer)){
				if(preg_match("/category :[^\n]*/", $buffer)){
					$buffer=substr($buffer,0,strlen($buffer)-1);
					$category=preg_replace("/category :/", "",$buffer);
					$images[$category]=array();
					$i++;
				}
				else{
					if(substr($buffer, -1)=="\n"){
						$buffer=substr($buffer,0,strlen($buffer)-1);
					}
					array_push($images[$category], $buffer);
				}
			}
			$liens=array();
			if(preg_match("/inter-category relations:/", $buffer)){
				while(($buffer = fgets($file) )!== false){
					$categories=split("has_seantic_relation", $buffer);
					$categories[0]=substr($categories[0],0,strlen($categories[0])-1);
					$categories[1]=substr($categories[1],1,strlen($categories[1])-2);
					array_push($liens, $categories);
				}
			}
			array_push($results, $images);
			array_push($results, $liens);
			fclose($file);
			return $results;
		}


		//Récupère juste les images (pas utilisé)
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