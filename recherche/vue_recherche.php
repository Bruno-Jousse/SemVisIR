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
';

  $this->categorieScript();
		}

//Affichage graphique
    public function graphique($img, $exif, $images, $liens) {
      $this->initialisationVariables($img, $exif, $images);
      $this->jsonLiens=json_encode($liens,JSON_UNESCAPED_SLASHES);
      
      //print_r($this->jsonArray);
      print_r($this->jsonLiens);
      
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
      <canvas id="viewport">
      </canvas>
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
(function($){

  var Renderer = function(canvas){
    var canvas = $(canvas).get(0);
    var ctx = canvas.getContext("2d");
    var gfx = arbor.Graphics(canvas);
    var particleSystem;

    var that = {
      init:function(system){
        
        particleSystem = system;

        particleSystem.screenSize(canvas.width, canvas.height) ;
        particleSystem.screenPadding(15); 

        that.initMouseHandling();

        particleSystem.eachNode(function(node, pt){
          var image=node.data.src;
          if(image !== undefined){
            node.data.img = new Image();
            node.data.img.src = node.data.src;
          }
        });
      },
      
      redraw:function(){
        gfx.clear();
        ctx.fillStyle = "white";
        ctx.fillRect(0,0, canvas.width, canvas.height);
        
        particleSystem.eachEdge(function(edge, pt1, pt2){

          ctx.strokeStyle = "#b2b19d";
          ctx.lineWidth = 1;
          ctx.beginPath();
          ctx.moveTo(pt1.x, pt1.y);
          ctx.lineTo(pt2.x, pt2.y);
          ctx.stroke();
        });

        particleSystem.eachNode(function(node, pt){
          if(node.data.categorie===undefined){
              var w = 5;
              ctx.fillStyle = "black";
              ctx.fillRect(pt.x-w/2, pt.y-w/2, w,w);
          }
          else{
            if(node.data.categorie=true){
              var label=node.name;

              if (label !== undefined){
                var w = Math.max(7, 7+gfx.textWidth(label));
                gfx.oval(pt.x-w/2, pt.y-w/2, w, w,  {fill:"#2E9AFE", alpha:"1"});
                gfx.text(label, pt.x, pt.y+7, {color:"white", align:"center", font:"Arial", size:8});
                gfx.text(label, pt.x, pt.y+7, {color:"white", align:"center", font:"Arial", size:8});
                
              }
            }
            else{
              var img=node.data.img;
              if (img !== undefined){
                ctx.drawImage(img, pt.x-(imageW/2), pt.y+radius/2, 50, 50);
              }
            }
          }
        });  			
      },
      
      initMouseHandling:function(){

        var handler = {
          clicked:function(e){
            var pos = $(canvas).offset();
            _mouseP = arbor.Point(e.pageX-pos.left, e.pageY-pos.top);
            
            nearest = particleSystem.nearest(_mouseP);

            if (!nearest.node) return false;
            selected = (nearest.distance < 50) ? nearest : null;

            if (nearest && selected && nearest.node===selected.node && selected.node.data.categorie!==undefined && selected.node.data.categorie==false){
              var link = selected.node.data.src;
              window.open(link, "_blank");
            }
          },
        }
        $(canvas).mousedown(handler.clicked);

      }
      
    }
    return that;
  }    

  $(document).ready(function(){
    var sys = arbor.ParticleSystem(2000, 500, 0.9);
    sys.parameters({gravity:true});
    sys.renderer = Renderer("#viewport");
    var images='.$this->jsonArray.';
    var liens='.$this->jsonLiens.';
    //var images={"beach":[{"src":"images/ImageCLEFphoto2008/images/39/39714.jpg","meta":{"Title":"Die Sydney Harbour Bridge, vom Sydney Observatorium aus","Description":"","Notes":"","Location":"Sydney, Australien","Date":"September 2002"},"sim":"0.923"},{"src":"images/ImageCLEFphoto2008/images/00/116.jpg","meta":{"Title":"Termas de Papallacta","Description":"","Notes":"","Location":"Papallacta, Ecuador","Date":"April 2002"},"sim":"0.921"},{"src":"images/ImageCLEFphoto2008/images/32/32698.jpg","meta":{"Title":"El Puente del Puerto de Sydney, desde el Observatorio de Sydney","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.919"}],"sand":[{"src":"images/ImageCLEFphoto2008/images/39/39706.jpg","meta":{"Title":"El Puente del Puerto de Sydney desde Campbell\'s Cove","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.899"},{"src":"images/ImageCLEFphoto2008/images/11/11027.jpg","meta":{"Title":"Sand dunes in the Len\u00e7ois Maranhenses National Park","Description":"","Notes":"","Location":"Len\u00e7ois Maranhenses, Brazil","Date":"25 March 2004"},"sim":"0.895"},{"src":"images/ImageCLEFphoto2008/images/39/39696.jpg","meta":{"Title":"El Puente del Puerto de Sydney","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.879"}],"person":[{"src":"images/ImageCLEFphoto2008/images/05/5079.jpg","meta":{"Title":"Der Strand bei Paracas","Description":"","Notes":"","Location":"Paracas, Peru","Date":"September 2002"},"sim":"0.709"},{"src":"images/ImageCLEFphoto2008/images/06/6619.jpg","meta":{"Title":"In the hot springs","Description":"","Notes":"","Location":"Chivay, Peru","Date":"10 October 2002"},"sim":"0.708"},{"src":"images/ImageCLEFphoto2008/images/32/32890.jpg","meta":{"Title":"Circular Quay und das Opernhaus von Sydney","Description":"","Notes":"","Location":"Sydney, Australien","Date":"3. Januar 2005"},"sim":"0.706"},{"src":"images/ImageCLEFphoto2008/images/02/2452.jpg","meta":{"Title":"Las ruinas de Chan Chan","Description":"","Notes":"","Location":"Trujillo, Per\u00fa","Date":"Noviembre de 2002"},"sim":"0.704"},{"src":"images/ImageCLEFphoto2008/images/05/5183.jpg","meta":{"Title":"Die K\u00fcste der Isla de la Plata","Description":"","Notes":"","Location":"Isla de la Plata, Ekuador","Date":"September 2002"},"sim":"0.704"},{"src":"images/ImageCLEFphoto2008/images/39/39699.jpg","meta":{"Title":"Vista de una parte de la \u00d3pera de Sydney","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.703"}]};
    //var liens=[["beach","sand"],["sand","person"]];

    for(categorie in images){
          
          var nodeCate=sys.addNode(categorie, {mass:.0001, categorie:true});
          //data["nodes"].push({categorie:{"mass":0.25, "categorie":"true"}});
          
          for(var i=0; i<images[categorie].length; i++){
            var name=categorie+i;
            var nodeImg=sys.addNode(name, {mass:0.25, categorie:false, src:images[categorie][i].src, meta:images[categorie][i].meta, sim:images[categorie][i].sim});
            console.log(nodeImg.data);

            if(nodeCate !== undefined && nodeImg !== undefined)
              sys.addEdge(nodeCate, nodeImg);

            //data[nodes].push({name:{"mass":0.25, "categorie":"false", "src":images[categorie][i].src, "meta":images[categorie][i].meta, "sim":images[categorie][i].sim}});
            //data[edges].push({categorie:{name:{}}});
          }
    }

    for(var i=0; i<liens.length; i++){
      console.log(liens[i][0]+", "+liens[i][1]);
      if(liens[i][0]!=null && liens[i][1]!=null){
        var node1=sys.getNode(liens[i][0]);
        var node2=sys.getNode(liens[i][1]);
        //data[edges].push({liens[i][0]:{}, liens[i][1]:{}});
        if(node1!== undefined && node2 !== undefined)
          sys.addEdge(node1, node2);
      }
    }
  });

})(this.jQuery);
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
