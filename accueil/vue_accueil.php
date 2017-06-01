<?php
	class VueAccueil extends VueGenerique{

		public function affiche(){
			$this->titre="Accueil";
			$this->contenu='<div  class="container content-section text-center">
      <form class="form-horizontal col-md-8 col-md-offset-2" method="POST" action="index.php?module=recherche" enctype="multipart/form-data" onsubmit="return checkSize(4194304);"> 
    <div class="form-group row">
      <div class="center">
        <div class="text-center" >
          <label>Choisissez une image:  </label>
        </div>
        <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
        <input id="choixImg" type="file" name="file" accept="image/*" onchange="readURL(this);" required/>
      </div>  
    </div>   
    <div class="form-group" >
        <div class="center">
        <a href="#" id="lienImg" data-lightbox="image_requete"><img id="imgPasCacheDuTout"/></a>
        </div>
    </div>
    <div class="form-group row" style="margin-top:3%">
      <label class="col-sm-3">Choix stratégiques :</label>
      <div class="col-sm-9">
        <select class="form-control" name="algo"  required >
          <option selected disabled hidden >Choisisez le choix stratégique ...</option>
          <option value="Basique">Basique</option>
          <option value="VP">A base de patron VP</option>
          <option value="SVP">A base de patron SVP</option>
          <option  value="SCVP">A base de patron SCVP</option>
          <option value="Patron">Ciblée à base de patron</option>
          <option value="Cible">Ciblée</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3">Présentation des résultats :</label>
      <div class="col-sm-9">
        <select class="form-control" name="affichage" required>
          <option selected disabled hidden >Choisisez une présentation...</option>
          <option value="Basique">Basique</option>
          <option value="Avancee">Avancée</option>
          <option value="Graphique">Graphique</option>
        </select>
      </div>
    </div>
    <input type="submit" value="Rechercher"/>
  </form>
  </div>
  
  <!-- Les scripts -->
   <script type="text/javascript">
     function checkSize(max_img_size){
         var input = document.getElementById("lienImg");

         // check for browser support (may need to be modified)
         if(input.files && input.files.length == 1){           
          if (input.files[0].size > max_img_size) {
            alert("The file must be less than " + (max_img_size/1024/1024) + "MB");
            return false;
          }
        }

      return true;
    }

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var i=e.target.result;   

                $(\'#imgPasCacheDuTout\')
                    .attr("src", i)
                    .width(300)
                    .height(300);
                $(\'#lienImg\').attr("href", i); 
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
  
  </script>
  ';
	}
}

