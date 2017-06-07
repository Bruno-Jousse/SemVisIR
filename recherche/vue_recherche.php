<?php
	class VueRecherche extends VueGenerique{

    private $name="";
    private $imageData="";
    private $location="";
    private $texte="";
    private $taille="";
    private $date="";
    private $images="";
    private $categories="";
    private $liens="";
    private $vue="";

    //Initialise les variables de manière globale pour leur utilisation dans d'autres méthodes sans avoir à les passer en argument + json_encode
    private function initialisationVariables($img, $exif, $images, $categories, $liens, $vue){
      $this->name=$img["name"];
      $this->imageData = file_get_contents($img["tmp_name"]);

      $this->location=$exif["location"];
      
      $this->texte=$exif["texte"];

      $this->taille=$exif["taille"];

      $this->date=$exif["date"];

      $this->images=json_encode($images,JSON_UNESCAPED_SLASHES);
      $this->categories=json_encode($categories,JSON_UNESCAPED_SLASHES);
      $this->liens=json_encode($liens,JSON_UNESCAPED_SLASHES);
      $this->vue=json_encode($vue,JSON_UNESCAPED_SLASHES);
    }

    //Tous les affichages ont une partie commune
    private function htmlCommun(){
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
              <li>Taille : '.$this->taille.' </li>
              <li>Texte (annotation si il y en a) : '.$this->texte.'</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-8">
      <div style="width:100%">
        <div class="well well-lg" id="navAffichage">
          <button id="Basique" class="btn btn-default" onclick="changerAffichage(\'Basique\')">Basique</button>
          <button id="Avancee" class="btn btn-default" onclick="changerAffichage(\'Avancee\')">Avancée</button>
          <button id="Graphique" class="btn btn-default" onclick="changerAffichage(\'Graphique\')">Graphique</button>
        </div>
      </div>
    <div class="partieDroite">
    ';
    }

    public function affichage($img, $exif, $images, $categories, $liens, $vue){
      $this->initialisationVariables($img, $exif, $images, $categories, $liens, $vue);

      $this->titre="Recherche";

      $this->htmlCommun();

      $vueBasique=$this->htmlBasique();

      $vueAvancee=$this->htmlAvancee();

      $vueGraphique=$this->htmlGraphique();

      $vueBasique=json_encode($vueBasique);
      $vueAvancee=json_encode($vueAvancee);
      $vueGraphique=json_encode($vueGraphique);

      $this->contenu.='<div id="affichage">
      </div>
      		<script type="text/javascript">

          var affichageBasique='.$vueBasique.';

          var affichageAvance='.$vueAvancee.';

          var affichageGraphique='.$vueGraphique.'; 

          var vue='.$this->vue.';

          function changerAffichage(vue){
            $("#navAffichage").children().removeClass("active");
            switch(vue){
              case "Avancee":
                $("#Avancee").addClass("active");
                $("#affichage").html(affichageAvance);
                dispatchEvent(new Event("load"));
                break;
              case "Graphique":
                $("#Graphique").addClass("active");
                $("#affichage").html(affichageGraphique);
                var DOMContentLoaded_event = document.createEvent("Event");
                DOMContentLoaded_event.initEvent("DOMContentLoaded", true, true);
                window.document.dispatchEvent(DOMContentLoaded_event);
                break;
              case "Basique":
              default:
                $("#Basique").addClass("active");
                $("#affichage").html(affichageBasique);
                dispatchEvent(new Event("load"));
            }
          }

          changerAffichage(vue);
          </script>
      ';
    }

//Affichage basique
		public function htmlBasique(){
      return '
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

  <script type="text/javascript">
      var images='.$this->images.';
      
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

        for (var i = (page-1) * records_per_page; i < (page * records_per_page) && i < images.length; i++) {
          var infos="";

          for(var j in images[i].meta){
            infos+=j+": "+images[i].meta[j]+"<br/>";
          } 
          infos+="Similarity = "+images[i].sim+"<br/>";
          infos+="Categories = ";
          for(var j in images[i].categories){
            infos+=images[i].categories[j]+" ";
          }

          listing_table.innerHTML += \'<a class="lienImg" href="\'+ images[i].src +\'" data-lightbox="images" data-title="\'+ infos +\'" ><img src="\'+ images[i].src +\'" class="imgBasique"><h4 class="similarity">\'+images[i].sim+\'</h4></a>\';
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
        return Math.ceil(images.length / records_per_page);
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

//Affichage avancée
		public function htmlAvancee(){

      return '
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

  <script type="text/javascript">
      var images='.$this->images.';
      var categories='.$this->categories.';
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
            infos+="Similarity= "+listeImages[i].sim+"<br/>"
            infos+="Categories = ";
            for(var j in listeImages[i].categories){
              infos+=listeImages[i].categories[j]+" ";
            }            
            listing_table.innerHTML += \'<a class="lienImg" href="\'+ listeImages[i].src +\'" data-lightbox="images" data-title="\'+ infos +\'" ><img src="\'+ listeImages[i].src +\'" class="imgBasique"><h4 class="similarity">\'+listeImages[i].sim+\'</h4></a>\';
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
        listeImages=getImages(categorieChecked);
        changePage(1);
      }
    }

    function getImages(categorie){
      var imgs=[];
      for(var i=0; i<images.length; i++){
        for(var j=0; j<images[i].categories.length; j++){
          if(images[i].categories[j]==categorie){
            imgs.push(images[i]);
          }
        }
      }
      return imgs;
    }

    window.onload = function() {
        //createCategories();
        categoriesHTML=document.getElementsByClassName("cat");
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

//Affichage graphique
    public function htmlGraphique() {
      
      return '
      <hr>
        <div id="boutons">
          <h4 > Choix de l\'affichage: </h4>
          <button id="circle" class="btn btn-default" onclick="changeLayout(\'circle\')">Circle</button>
          <button id="concentric" class="btn btn-default active" onclick="changeLayout(\'concentric\')">Concentric</button>
          <button id="cose" class="btn btn-default" onclick="changeLayout(\'cose\')">Cose</button>
          <button id="grid" class="btn btn-default" onclick="changeLayout(\'grid\')">Grid</button>
        </div>
        <div id="cy"></div>
          <button class="btn btn-default" onclick="reset()">Reset</button>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    
      var images='.$this->images.';
      var categories='.$this->categories.';
      var liens='.$this->liens.';
      //var images={beach:[{"src":"images/ImageCLEFphoto2008/images/39/39714.jpg","meta":{"Title":"Die Sydney Harbour Bridge, vom Sydney Observatorium aus","Description":"","Notes":"","Location":"Sydney, Australien","Date":"September 2002"},"sim":"0.923"},{"src":"images/ImageCLEFphoto2008/images/00/116.jpg","meta":{"Title":"Termas de Papallacta","Description":"","Notes":"","Location":"Papallacta, Ecuador","Date":"April 2002"},"sim":"0.921"},{"src":"images/ImageCLEFphoto2008/images/32/32698.jpg","meta":{"Title":"El Puente del Puerto de Sydney, desde el Observatorio de Sydney","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.919"}],"sand":[{"src":"images/ImageCLEFphoto2008/images/39/39706.jpg","meta":{"Title":"El Puente del Puerto de Sydney desde Campbell\'s Cove","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.899"},{"src":"images/ImageCLEFphoto2008/images/11/11027.jpg","meta":{"Title":"Sand dunes in the Len\u00e7ois Maranhenses National Park","Description":"","Notes":"","Location":"Len\u00e7ois Maranhenses, Brazil","Date":"25 March 2004"},"sim":"0.895"},{"src":"images/ImageCLEFphoto2008/images/39/39696.jpg","meta":{"Title":"El Puente del Puerto de Sydney","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.879"}],"person":[{"src":"images/ImageCLEFphoto2008/images/05/5079.jpg","meta":{"Title":"Der Strand bei Paracas","Description":"","Notes":"","Location":"Paracas, Peru","Date":"September 2002"},"sim":"0.709"},{"src":"images/ImageCLEFphoto2008/images/06/6619.jpg","meta":{"Title":"In the hot springs","Description":"","Notes":"","Location":"Chivay, Peru","Date":"10 October 2002"},"sim":"0.708"},{"src":"images/ImageCLEFphoto2008/images/32/32890.jpg","meta":{"Title":"Circular Quay und das Opernhaus von Sydney","Description":"","Notes":"","Location":"Sydney, Australien","Date":"3. Januar 2005"},"sim":"0.706"},{"src":"images/ImageCLEFphoto2008/images/02/2452.jpg","meta":{"Title":"Las ruinas de Chan Chan","Description":"","Notes":"","Location":"Trujillo, Per\u00fa","Date":"Noviembre de 2002"},"sim":"0.704"},{"src":"images/ImageCLEFphoto2008/images/05/5183.jpg","meta":{"Title":"Die K\u00fcste der Isla de la Plata","Description":"","Notes":"","Location":"Isla de la Plata, Ekuador","Date":"September 2002"},"sim":"0.704"},{"src":"images/ImageCLEFphoto2008/images/39/39699.jpg","meta":{"Title":"Vista de una parte de la \u00d3pera de Sydney","Description":"","Notes":"","Location":"Sydney, Australia","Date":"Septiembre de 2002"},"sim":"0.703"}]};
      //var liens=[["beach","sand"],["sand","person"]];
      
      var initNodes=[];
      var initEdges=[];

      var categorieCouleur=[];

      for(var colorI=0; colorI<categories.length; colorI++){
        var colorVal=getColor(colorI);
        initNodes.push({"data": {"id": categories[colorI], "categorie": true, "color": colorVal}});
        categorieCouleur[categories[colorI]]=colorVal;
      }

      for(var i=0; i<images.length; i++){
        initNodes.push({"data": {"id": images[i].src, "categorie": false, "src": images[i].src, "meta": images[i].meta, "sim": images[i].sim, "categories": images[i].categories }});
        for(var j=0; j<images[i].categories.length; j++){
          initEdges.push({"data": {"source": images[i].categories[j], "target": images[i].src, "color": categorieCouleur[images[i].categories[j]], "categorie": false}});
        }
      }

      for(var i=0; i<liens.length; i++){
        console.log("source: "+liens[i][0]+", target: "+liens[i][1]);
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

              $("#lienTemp").remove();

              var infos="";
              for(var j in element.data("meta")){
                infos+=j+": "+element.data("meta")[j]+"<br/>";
              } 
              infos+="Similarity="+element.data("sim")+"<br/>";
              infos+="Categories = ";
              for(var categorie in element.data("categories")){
                infos+=element.data("categories")[categorie]+" ";
              }

              var link = $(\'<a id="lienTemp" style="display:none;" href="\'+element.data("src")+\'" rel="lightbox" data-title="\'+infos+\'" ><img src="\'+ element.data("src") +\'" class="imgBasique"></a>\');
         	    $("body").append(link);
			        $("#lienTemp").trigger("click"); 
          
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

    });

    function reset(){
      if(cy!==undefined && cy!=null){
        cy.fit(cy);
      }
    }

    function changeLayout(newLayout){
      if(cy!==undefined && cy!=null){
        $("#boutons").children("button").removeClass("active");
        switch(newLayout){
          case "circle":
            $("#circle").addClass("active");
            break;

          case "cose":
            $("#cose").addClass("active");
            break;

          case "grid":
            $("#grid").addClass("active");
            break;

          case "concentric":
          default:
            $("#concentric").addClass("active");
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
	}
?>
