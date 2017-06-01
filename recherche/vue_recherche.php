<?php
	class VueRecherche extends VueGenerique{

    private $name="";
    private $imageData="";
    private $location="";
    private $texte="";
    private $tailleKo="";
    private $date="";
    private $jsonArray="";
    private $jsonLiens="";

    private function initialisationVariables($img, $exif, $images){
      $this->name=$img["name"];
      $this->imageData = file_get_contents($img["tmp_name"]);

      $long=$exif["GPS"]["GPSLongitude"];
      $longRef=$exif["GPS"]["GPSLongitudeRef"];
      $lat=$exif["GPS"]['GPSLatitude'];
      $latRef=$exif["GPS"]['GPSLatitudeRef'];
      if($long != null && $longRef != null && $lat != null && $latRef != null){
        $longitude=$this->getGps($long, $longRef);
        $latitude=$this->getGps($lat, $latRef);
        $this->location='longitude='.$longitude.', latitude='.$latitude;
      }

      $this->texte=$exif["COMPUTED"]["UserComment"];

      $taille=$exif["FILE"]["FileSize"];
      if($taille!=null){
        $this->tailleKo=$taille/1024;
        $this->tailleKo.=" Ko";
      }

      $time=$exif["DateTimeOriginal"];
      if($time!=null){
          $this->date=date("H:i d/m/Y", $time);
      }

      $this->jsonArray=json_encode($images,JSON_UNESCAPED_SLASHES);
    }

//Affichage basique
		public function basique($img, $exif, $images){
      $this->initialisationVariables($img, $exif, $images);
			
      $this->titre="Recherche basique";
			$this->contenu='<div  class="container content-section text-center">
      <div class="col-sm-4" id="divGauche">
    <div class="well well-sm">
      <div class="text-center">
        <div id="titre">
    <h2>Image requête : </h2>
    <hr>
    '.sprintf('<a href="data:image/png;base64,%s" id="imgRequete" data-lightbox="image_requete" data-title="%s" >', base64_encode($this->imageData), $this->texte)
    .''.
     sprintf('<img src="data:image/png;base64,%s" id="imgRequete"  alt="image requete"/>', base64_encode($this->imageData)).'
    </a>
    </div>
    <div id="info" >
      <h4 class="text-center"> Informations sur l\'image requête :</h4>
      <hr>
      <ul>
        <li>Nom : '.$this->name.'</li>
        <li>Date : '.$this->date.'</li>
        <li>Lieu : '.$this->location.'</li>
        <li>Taille : '.$this->tailleKo.' </li>
        <li>Texte (annotation si il y en a) : '.$this->texte.'</li>
      </ul>
    </div>
    </div>
    </div>
  </div>
  <div class="col-sm-8">
    <div class="partieDroite">
    <h2>Liste des résultats :</h2>
    <hr>
        <div id="divboucle">
        </div>
        
        <div id="pagination">
          <a href="javascript:firstPage()" id="btn_first"><<</a>
          <a href="javascript:prevPage()" id="btn_prev"><</a>
          page: <span id="page"></span>
          <a href="javascript:nextPage()" id="btn_next">></a>
          <a href="javascript:lastPage()" id="btn_last">>></a>
        </div>
      </div>
    </div>
  </div>
  ';
      $this->basiqueScript();
		}

//Affichage avancée
		public function avancee($img, $exif, $images){
      $this->initialisationVariables($img, $exif, $images);

			$this->titre="Recherche avancée";
			$this->contenu='
<div  class="container content-section text-center">
  <div class="col-sm-4" id="divGauche">
    <div class="well well-sm">
      <div class="text-center">
        <div id="titre">
    <h2>Image requête : </h2>
    <hr>
    '.sprintf('<a href="data:image/png;base64,%s" id="imgRequete" data-lightbox="image_requete" data-title="%s" >', base64_encode($this->imageData), $this->texte)
    .''.
     sprintf('<img src="data:image/png;base64,%s" id="imgRequete"  alt="image requete"/>', base64_encode($this->imageData)).'
    </a>
    </div>
    <div id="info" >
      <h4 class="text-center"> Informations sur l\'image requête :</h4>
      <hr>
      <ul>
        <li>Nom : '.$this->name.'</li>
        <li>Date : '.$this->date.'</li>
        <li>Lieu : '.$this->location.'</li>
        <li>Taille : '.$this->tailleKo.' </li>
        <li>Texte (annotation si il y en a) : '.$this->texte.'</li>
      </ul>
    </div>
    </div>
    </div>
  </div>
  <div class="col-sm-8">
    <div class="partieDroite">
    <h4 id="titreCat"> Catégories:</h4>
    <hr>
        <div class="categories">
        <a class="fleche" href="javascript:flecheGauche()" id="flecheG"><</a>
          <div id="Categorie0" onclick="selectionCategorie(this)" class="cat"><input id="Input0"  class="radio" type="radio" name="Categorie" checked /><label class="radioLabel" for="Input0"></label></div>   
          <div id="Categorie1" onclick="selectionCategorie(this)" class="cat"><input id="Input1" class="radio" type="radio" name="Categorie" /><label class="radioLabel" for="Input1"></label></div>
          <div id="Categorie2" onclick="selectionCategorie(this)" class="cat"><input id="Input2" class="radio" type="radio" name="Categorie" /><label class="radioLabel" for="Input2"></label></div>
          <div id="Categorie3" onclick="selectionCategorie(this)" class="cat"><input id="Input3" class="radio" type="radio" name="Categorie" /><label class="radioLabel" for="Input3"></label></div>
        <a class="fleche" href="javascript:flecheDroite()" id="flecheD">></a>
        </div>
        <hr>

        <div id="divboucle">
        </div>
        
        <div id="pagination">
          <a href="javascript:firstPage()" id="btn_first"><<</a>
          <a href="javascript:prevPage()" id="btn_prev"><</a>
          page: <span id="page"></span>
          <a href="javascript:nextPage()" id="btn_next">></a>
          <a href="javascript:lastPage()" id="btn_last">>></a>
        </div>
      </div>
      </div>
    </div>
  </div>
';

      $this->categorieScript();
		}

//Affichage graphique
    public function graphique($img, $exif, $images, $liens) {

      $this->initialisationVariables($img, $exif, $images);
      $this->jsonLiens=json_encode($liens,JSON_UNESCAPED_SLASHES);
      
			$this->titre="Recherche avancée";
			$this->contenu='
<div  class="container content-section text-center">
  <div class="col-sm-4" id="divGauche">
    <div class="well well-sm">
      <div class="text-center">
        <div id="titre">
    <h2>Image requête : </h2>
    <hr>
    '.sprintf('<a href="data:image/png;base64,%s" id="imgRequete" data-lightbox="image_requete" data-title="%s" >', base64_encode($this->imageData), $this->texte)
    .''.
     sprintf('<img src="data:image/png;base64,%s" id="imgRequete"  alt="image requete"/>', base64_encode($this->imageData)).'
    </a>
    </div>
    <div id="info" >
      <h4 class="text-center"> Informations sur l\'image requête :</h4>
      <hr>
      <ul>
        <li>Nom : '.$this->name.'</li>
        <li>Date : '.$this->date.'</li>
        <li>Lieu : '.$this->location.'</li>
        <li>Taille : '.$this->tailleKo.' </li>
        <li>Texte (annotation si il y en a) : '.$this->texte.'</li>
      </ul>
    </div>
    </div>
    </div>
  </div>
  <div class="col-sm-8" style="height:700px;">
    <div class="partieDroite">
      <!--<canvas id="viewport"></canvas>-->
      <div>
        <h4> Choix de l\'affichage: </h4>
        <button onclick="changeLayout(\'circle\')">Circle</button>
        <button onclick="changeLayout(\'concentric\')">Concentric</button>
        <button onclick="changeLayout(\'cose\')">Cose</button>
        <button onclick="changeLayout(\'grid\')">Grid</button>
      </div>
      <div id="cy"></div>
      <button onclick="reset()">Reset</button>
    </div>
  </div>
</div>
  ';
        $this->graphiqueScript();
      }


    private function basiqueScript(){
      $this->contenu.='
		<script type="text/javascript">
      var objJson='.$this->jsonArray.';
      
      var current_page = 1;
      var records_per_page = 20;

      function prevPage()
      {
        if (current_page > 1) {
        current_page--;
        changePage(current_page);
        }
      }

      function nextPage()
      {
        if (current_page < numPages()) {
          current_page++;
          changePage(current_page);
        }
      }

      function changePage(page)
      {
        var btn_next = document.getElementById("btn_next");
        var btn_prev = document.getElementById("btn_prev");
        var btn_first = document.getElementById("btn_first");
        var btn_last = document.getElementById("btn_last");
        var listing_table = document.getElementById("divboucle");
        var page_span = document.getElementById("page");

        // Validate page
        if (page < 1) page = 1;
        if (page > numPages()) page = numPages();

        listing_table.innerHTML = "";

        for (var i = (page-1) * records_per_page; i < (page * records_per_page) && i < objJson.length; i++) {
          var infos="";

          for(var j in objJson[i].meta){
            infos+=j+": "+objJson[i].meta[j]+"<br/>";
          } 
          infos+="Similarities="+objJson[i].sim;
          
          listing_table.innerHTML += \'<a href="\'+ objJson[i].src +\'" data-lightbox="images" data-title="\'+ infos +\'" ><img src="\'+ objJson[i].src +\'" class="imgBasique"></a>\';
        }

        page_span.innerHTML = page;

        if (page == 1) {
            btn_prev.style.visibility = "hidden";
            btn_first.style.visibility = "hidden";
        } else {
            btn_prev.style.visibility = "visible";
            btn_first.style.visibility = "visible";
        }

        if (page == numPages()) {
            btn_next.style.visibility = "hidden";
            btn_last.style.visibility = "hidden";
        } else {
            btn_next.style.visibility = "visible";
            btn_last.style.visibility = "visible";
        }
    }

    function numPages()
    {
        return Math.ceil(objJson.length / records_per_page);
    }

    function firstPage(){
      current_page=1
      changePage(current_page);
    }

    function lastPage(){
      current_page=numPages()
      changePage(current_page);
    }

    window.onload = function() {
        changePage(1);
    };
      
		</script>';
    }



    private function categorieScript(){
      $this->contenu.='
      <script type="text/javascript">
      var objJson='.$this->jsonArray.';
      var listeImages;

      //Pagination

      var current_page = 1;
      var records_per_page = 20;

      function prevPage()
      {
        if (current_page > 1) {
        current_page--;
        changePage(current_page);
        }
      }

      function nextPage()
      {
        if (current_page < numPages()) {
          current_page++;
          changePage(current_page);
        }
      }

      function changePage(page)
      {
        var btn_next = document.getElementById("btn_next");
        var btn_prev = document.getElementById("btn_prev");
        var btn_first = document.getElementById("btn_first");
        var btn_last = document.getElementById("btn_last");
        var listing_table = document.getElementById("divboucle");
        var page_span = document.getElementById("page");

        // Validate page
        if (page < 1) page = 1;
        if (page > numPages()) page = numPages();

        listing_table.innerHTML = "";

        if(typeof listeImages !== "undefined"){

          for (var i = (page-1) * records_per_page; i < (page * records_per_page) && i < listeImages.length; i++) {
            var infos="";

            for(var j in listeImages[i].meta){
              infos+=j+": "+listeImages[i].meta[j]+"<br/>";
            } 
            infos+="Similarities= "+listeImages[i].sim+"<br/>"
            +"Category: "+categorieChecked;
            listing_table.innerHTML += \'<a href="\'+ listeImages[i].src +\'" data-lightbox="images" data-title="\'+ infos +\'" ><img src="\'+ listeImages[i].src +\'" class="imgBasique"></a>\';
          }
        }
       
        page_span.innerHTML = page;

        if (page == 1 || typeof listeImages === "undefined") {
            btn_prev.style.visibility = "hidden";
            btn_first.style.visibility = "hidden";
        } else {
            btn_prev.style.visibility = "visible";
            btn_first.style.visibility = "visible";
        }

        if (page == numPages() || typeof listeImages === "undefined") {
            btn_next.style.visibility = "hidden";
            btn_last.style.visibility = "hidden";
        } else {
            btn_next.style.visibility = "visible";
            btn_last.style.visibility = "visible";
        }
    }

    function numPages()
    {
        return Math.ceil(listeImages.length / records_per_page);
    }

    function firstPage(){
      current_page=1;
      changePage(current_page);
    }

    function lastPage(){
      current_page=numPages();
      changePage(current_page);
    }

    //affichage catégorie

    var indexCategories=0;
    var categories;
    var categoriesHTML;
    var categorieChecked;
    
    function affichageCategories(){
        for(var i=0; i<categoriesHTML.length; i++){
          categoriesHTML[i].getElementsByTagName("label")[0].innerHTML=categories[i+indexCategories];

          if(typeof categorieChecked !== "undefined"){
            if(categoriesHTML[i].getElementsByTagName("label")[0].innerHTML==categorieChecked){
              categoriesHTML[i].getElementsByTagName("input")[0].checked=true;
            }
            else{
              categoriesHTML[i].getElementsByTagName("input")[0].checked=false;
            }
          }        
        }
    }

    function flecheDroite(){
      indexCategories+=1;
      if(indexCategories>categories.length-categoriesHTML.length){
        indexCategories=categories.length-categoriesHTML.length;
      }
      affichageCategories();
    }

    function flecheGauche(){
      indexCategories-=1;
      if(indexCategories<0){
        indexCategories=0;
      }
      affichageCategories();
    }

    function selectionCategorie(div){
      if(typeof div !== "undefined"){
        categorieChecked=div.getElementsByTagName("label")[0].innerHTML;
        div.getElementsByTagName("input")[0].checked=true;
        if(listeImages!=objJson[categorieChecked]){
          listeImages=objJson[categorieChecked];
          changePage(1);
        }
      }
    }

    window.onload = function() {
        //createCategories();
        categoriesHTML=document.getElementsByClassName("cat");
        categories=Object.keys(objJson);
        affichageCategories();
        if(categories.length>0)
          selectionCategorie(categoriesHTML[0]);
    };

    window.onerror = function(msg, url, linenumber) {
      alert(\'Error message: \'+msg+\'\nURL: \'+url+\'\nLine Number: \'+linenumber);
      return true;
    }
    
		</script>';
    }

    private function graphiqueScript(){
 $this->contenu.='
    <script type="text/javascript">
  
    var images='.$this->jsonArray.';
    var liens='.$this->jsonLiens.';
    //var images={beach:[{"src":"images/ImageCLEFphoto2008/images/39/39714.jpg","meta":{"Title":"Die Sydney Harbour Bridge, vom Sydney Observatorium aus","Description":"","Notes":"","Location":"Sydney, Australien","Date":"September 2002"},"sim":"0.923"},{"src":"images/ImageCLEFphoto2008/images/00/116.jpg","meta":{"Title":"Termas de Papallacta","Description":"","Notes":"","Location":"Papallacta, Ecuador","Date":"April 2002"},"sim":"0.921"},{"src":"images/ImageCLEFphoto2008/images/32/32698.jpg","meta":{"Title":"El Puente del Puerto de Sydney, desde el Observatorio de Sydney","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.919"}],"sand":[{"src":"images/ImageCLEFphoto2008/images/39/39706.jpg","meta":{"Title":"El Puente del Puerto de Sydney desde Campbell\'s Cove","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.899"},{"src":"images/ImageCLEFphoto2008/images/11/11027.jpg","meta":{"Title":"Sand dunes in the Len\u00e7ois Maranhenses National Park","Description":"","Notes":"","Location":"Len\u00e7ois Maranhenses, Brazil","Date":"25 March 2004"},"sim":"0.895"},{"src":"images/ImageCLEFphoto2008/images/39/39696.jpg","meta":{"Title":"El Puente del Puerto de Sydney","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.879"}],"person":[{"src":"images/ImageCLEFphoto2008/images/05/5079.jpg","meta":{"Title":"Der Strand bei Paracas","Description":"","Notes":"","Location":"Paracas, Peru","Date":"September 2002"},"sim":"0.709"},{"src":"images/ImageCLEFphoto2008/images/06/6619.jpg","meta":{"Title":"In the hot springs","Description":"","Notes":"","Location":"Chivay, Peru","Date":"10 October 2002"},"sim":"0.708"},{"src":"images/ImageCLEFphoto2008/images/32/32890.jpg","meta":{"Title":"Circular Quay und das Opernhaus von Sydney","Description":"","Notes":"","Location":"Sydney, Australien","Date":"3. Januar 2005"},"sim":"0.706"},{"src":"images/ImageCLEFphoto2008/images/02/2452.jpg","meta":{"Title":"Las ruinas de Chan Chan","Description":"","Notes":"","Location":"Trujillo, Per\u00fa","Date":"Noviembre de 2002"},"sim":"0.704"},{"src":"images/ImageCLEFphoto2008/images/05/5183.jpg","meta":{"Title":"Die K\u00fcste der Isla de la Plata","Description":"","Notes":"","Location":"Isla de la Plata, Ekuador","Date":"September 2002"},"sim":"0.704"},{"src":"images/ImageCLEFphoto2008/images/39/39699.jpg","meta":{"Title":"Vista de una parte de la \u00d3pera de Sydney","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.703"}]};
    //var liens=[["beach","sand"],["sand","person"]];
    

    var initNodes=[];
    var initEdges=[];

    var colorI=0;
    for(categorie in images){
      var colorVal=getColor(colorI);
      initNodes.push({"data": {"id": categorie, "categorie": true, "color": colorVal}});
      
      for(var i=0; i<images[categorie].length; i++){
        var name=categorie+i;
        initNodes.push({"data": {"id": images[categorie][i]["src"], "categorie": false, "src": images[categorie][i]["src"], "meta": images[categorie][i]["meta"], "sim": images[categorie][i]["sim"]}});
        initEdges.push({"data": {"source": categorie, "target": images[categorie][i]["src"], "color": colorVal, "categorie": false}});
      }
      colorI++;
    }

    for(var i=0; i<liens.length; i++){
      initEdges.push({"data": {source: liens[i][0], "target": liens[i][1], "color": "#000000", "categorie": true }});
    }  

    var cy;

    function getColor(index){
      var nb=index%6;
      var i=Math.floor(index/6)
      if(nb==0){
        var r=255;
        var g=0;
        var b=(0+i*10)%256;
        return "rgb("+r+", "+g+", "+b+")";
      }
      else if(nb==1){
        var r=(255-i*10)%256;
        var g=0;
        var b=255;
        return "rgb("+r+", "+g+", "+b+")";
      }
      else if(nb==2){
        var r=0;
        var g=(0+i*10)%256;
        var b=255;
        return "rgb("+r+", "+g+", "+b+")";
      }
      else if(nb==3){
        var r=0;
        var g=255;
        var b=(255-i*10)%256;
        return "rgb("+r+", "+g+", "+b+")";
      }
      else if(nb==4){
        var r=(0+i*10)%256;
        var g=255;
        var b=0;
        return "rgb("+r+", "+g+", "+b+")";
      }
      else if(nb==5){
        var r=255;
        var g=(255-i*10)%256;
        var b=0;
        return "rgb("+r+", "+g+", "+b+")";
      }
      return "#000000";
    }

"use strict";
document.addEventListener(\'DOMContentLoaded\', function() {
    cy=cytoscape({
      container:document.getElementById("cy"),
      elements:{
        nodes: initNodes,
        edges: initEdges
      },

      style: cytoscape.stylesheet().selector(\'node\').css({  
          \'height\': "80px",
          \'width\': "80px",
          \'background-fit\': \'cover\',
          \'background-color\': \'#000000\'
      }).selector(\'edge\').css({
          \'width\': 2,
          \'line-color\': \'#000000\',
          \'target-arrow-color\': \'#000000\',
          \'target-arrow-shape\': \'triangle\'
      }).selector(\'.categorie\').css({
          \'height\': "40px",
          \'width\': "  40px",
	        \'background-color\': \'#FF0000\',
          \'color\': \'#FF0000\',
          \'text-transform\': \'uppercase\',
          \'font-weight\': \'bold\'
      }),

      layout:{
        //grid, circle, concentric, cose
        name: \'concentric\',
      }
    });

   cy.filter(function(element, i){
      if(element.isNode() && element.data("categorie") == false && element.data("src") != ""){
          element.style("background-image", element.data("src"));
          element.on("tap", function(evt){
            window.open(element.data("src"), "_blank");  
          });
          return true;
      }
      if(element.isNode() && element.data("categorie") == true){
        element.addClass("categorie");
        element.style(\'label\', element.data("id"));
        element.style(\'background-color\', element.data("color"));
        return true;
      }
      var couleur
      if(element.isEdge() && (couleur=element.data("color"))!==undefined){
        element.style("line-color", couleur);

        if(element.data("categorie")==true){
          element.style("width", 4);
        }
        return true;
      }
      return false;
    });

/*
  for(categorie in images){
          
          cy.add({group: "nodes", data: {id: categorie, "categorie": true}});
          
          for(var i=0; i<images[categorie].length; i++){
            var name=categorie+i;
            cy.add({group: "nodes", data: {id: name, "categorie": false}});
            cy.add({group: "edges", data: {source: categorie, target: name}});
          }
    }

    for(var i=0; i<liens.length; i++){
      cy.add({group: "edges", data: {source: liens[i][0], target: liens[i][1]}});
    }
*/

  });

  function reset(){
    if(cy!==undefined && cy!=null){
      cy.reset();
    }
  }

  function changeLayout(newLayout){
    if(cy!==undefined && cy!=null){
      switch(newLayout){
        case "circle":
          break;
        case "cose":
          break;
        case "grid":
          break;
        case "concentric":
          break;
        default:
          newLayout="concentric";
      }
      var layout = cy.layout({
        "name": newLayout
      });
      layout.run();
    }
  }

</script>';
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
	}
?>
