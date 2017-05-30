	<body >
		<div id = "color" >
  		<?php	
			include("header.php");
			include("nav.php");
		  	echo  "<section> " .$module->getControleur()->getVue()->getContenu() . " </section>"; 
			include("footer.php");
		?>
		</div>
	</body>
