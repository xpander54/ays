<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	
	<?php if (is_search()) { ?>
	   <meta name="robots" content="noindex, nofollow" /> 
	<?php } ?>

	<meta name="description" content="Con el seguro de arrendamiento de AySerrano no tendrás de que preocuparte; actuamos inmediatamente en caso de cualquier imprevisto con tu inquilino." />

	<title>
		
		Seguro de Arrendamiento que protege tu patrimonio - AySerrano

	</title>

	<!-- <title>
		   
		   <?php/*
		      if (function_exists('is_tag') && is_tag()) {
		         single_tag_title("Tag Archive for &quot;"); echo '&quot; - '; }
		      elseif (is_archive()) {
		         wp_title(''); echo ' Archive - '; }
		      elseif (is_search()) {
		         echo 'Search for &quot;'.wp_specialchars($s).'&quot; - '; }
		      elseif (!(is_404()) && (is_single()) || (is_page())) {
		         wp_title(''); echo ' - '; }
		      elseif (is_404()) {
		         echo 'Not Found - '; }
		      if (is_home()) {
		         bloginfo('name'); echo ' - '; bloginfo('description'); }
		      else {
		          bloginfo('name'); }
		      if ($paged>1) {
		         echo ' - page '. $paged; }
		   */?>
	
	</title> -->

	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/shadowbox/shadowbox.css">

	<link href='http://fonts.googleapis.com/css?family=Coda:800' rel='stylesheet' type='text/css'>
	
	<link rel="shortcut icon" href="/favicon.ico">
	
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
	
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>




	<?php if ( is_singular() ) wp_enqueue_script('comment-reply'); ?>

	<?php wp_head(); ?>
</head>

<body id='body1' <?php body_class(); ?>>
	

	


	<div id="page-wrap">

		<header id="header">

			<div class="social-menu">
				<div class="main">

					<div class="logo">
						
						<img src="<?php bloginfo('template_url'); ?>/img/logo.png" alt="AySerrano">
						
					</div>

					<div class="social-icons">
						<!-- <ul>
							<li><a href="#"><img src="<?php// bloginfo('template_url'); ?>/img/social-media/twitter.png" alt="Twitter"></a></li>
							<li><a href=""><img src="<?php// bloginfo('template_url'); ?>/img/social-media/facebook.png" alt="Facebook"></a></li>
							<li><a href=""><img src="<?php// bloginfo('template_url'); ?>/img/social-media/gplus.png" alt="Google Plus"></a></li>
							<li><a href=""><img src="<?php// bloginfo('template_url'); ?>/img/social-media/linkdin.png" alt="Linked In"></a></li>
						</ul> -->

					</div>
					
					<div class="signin">
						<ul>
							<li>
								<img src="<?php bloginfo('template_url'); ?>/img/social-media/asesores.png" alt="acceso asesores" />
								<a href="http://www.ayserrano.com/asesores/">ingreso asesores</a>
							</li>
							<li>
								<img src="<?php bloginfo('template_url'); ?>/img/social-media/tel.png" alt="telefono" />
								5598 9394
							</li>
							<li>
								<img src="<?php bloginfo('template_url'); ?>/img/social-media/mail.png" alt="e-mail" />
								<a href="mailto:ayserranosc@gmail.com">ayserranosc@gmail.com</a>
							</li>
						</ul>
						
						
					</div>


				</div>
				
			</div>
			<div class="main-menu">
				<div class="main">


					
					<div class="menu1">
						<?php wp_nav_menu(array('menu' => 'menu1')); ?>

						
					</div>

				</div>
			</div>

			<div class="slide-cont">
				



				<div class="slide1 hidden-xs">

					<article class="coberturas-wrap-index">

						
						<div class="coberturas-indx-ttl">
							<h2>
								
								Coberturas

							</h2>
							
						</div>
						<ul class="coberturas-indx-list">
							<li><a href=""><span class='cobertura-index'>A</span> Básica</a></li>
							<li><a href=""><span class='cobertura-index'>B</span> Jurídica</a></li>
							<li><a href=""><span class='cobertura-index'>C</span> Jurídica más daños</a></li>
						</ul>
					</article>


					<!--Slide Bootstrap-->
	             
					     <div id="myCarousel" class="carousel slide">
				 
				                
				 
				                <!-- Carousel items -->
				                <div class="carousel-inner">
				 
				                  
									<div class="active item">
							          <img src="<?php bloginfo('template_url');?>/img/slide2/1.png" class="slide-img img" alt="DF">									
							        </div>
							        <div class="item">
							          <img src="<?php bloginfo('template_url');?>/img/slide2/2.png" class="slide-img img" alt="bienes raices">
							        </div>
							        <div class="item">
							          <img src="<?php bloginfo('template_url');?>/img/slide2/3.png" alt="seguro de arrendamiento" class="img slide-img">
							        </div>
							        <div class="item">
							          <img src="<?php bloginfo('template_url');?>/img/slide2/5.png" alt="AySerrano" class="img slide-img">
							        </div>



				 
				                </div>

				                <div class="indicadores">
				                	


					                <!-- <ol class="carousel-indicators">
					                					 
					                  <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
					                  <li data-target="#myCarousel" data-slide-to="1"></li>
					                  <li data-target="#myCarousel" data-slide-to="2"></li>
					                  <li data-target="#myCarousel" data-slide-to="3"></li>
					                					 
					                </ol> -->
					 
				                </div>
				                 
				                <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
				                <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
				                
				                
				                
				  </div> 

				 
				            
				<!--Slide Bootstrap-->
				

				

			</div>


				
		        
		       
			</div>
			
			
		


		
		</header>