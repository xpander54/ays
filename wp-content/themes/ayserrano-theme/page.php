<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="main indx-wrap">
			
			<div class="post" id="post-<?php the_ID(); ?>">
				
				
				<!-- 



				<h2><?php// the_title(); ?></h2>
				
				
				<?php// include (TEMPLATEPATH . '/inc/meta.php' ); ?>
				 -->

					<div class="main">
						<div class="index-txt1">
							El <span class='gris'>A</span><span class='rojo'>B</span><span class='azul'>C</span> de los <strong>seguros</strong>
						</div> 

						
						
						
					</div>
					<br><br>
					<div class="row">

											
						
						<!-- termina col left -->

						<div class=" col-sm-12 ">

							 <div class="row">

							 	<div class=" col-sm-2 line-subtitle">
						
								</div>	
								
								
								<div class="col-sm-2 col-subtitle">
										<h2 class='subtitle'>
											
											Empresa
										</h2>

								</div>
								<div class="col-sm-7 line-subtitle">
									
								</div>

								

							
							</div>

							<div class="row">

								<br><br>
								

								<div class="panel-group" id="accordion">
 
								  <div class="panel panel-default">
								 
								    <div class="panel-heading">
								      <h4 class="panel-title">
								        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
								          ¿Quiénes Somos?
								        </a>
								      </h4>
								    </div>
								  
								    <div id="collapseOne" class="panel-collapse collapse in">
								      <div class="panel-body">
								        
										
										 	<div class="coberturas-index">
												

												<div class="entry">

													
													<?php the_content(); ?>

													<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>

													<?php  edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
												
													<?php // comments_template(); ?>

												</div>

												<?php endwhile; endif; ?>
											
											</div>
										
										

								      </div> <!-- panel-body -->
								    </div>
								  
								  </div>
								 
								 
								  <div class="panel panel-default">
								    <div class="panel-heading">
								      <h4 class="panel-title">
								        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
								          Solamente con la prevención correcta podemos protegernos de los incidentes que surjan, para ello vale la pena cuestionarse sobre lo siguiente:
 											
								        </a>
								      </h4>
								    </div>
								    <div id="collapseTwo" class="panel-collapse collapse in">
								      <div class="panel-body">

								      	<ul>
								      		<li>¿Sabe que hacer si le dejan de pagar la renta?</li>
								      		<li>¿Tiene miedo de potenciales daños a su patrimonio?</li>
								      		<li>¿Sabe usted como recuperar su inmueble de un inquilino deudor?</li>
								      		<li>¿Conoce usted como tener un arrendamiento óptimo?</li>
								      		<li>¿Le gustaría que esos problemas, no fueran suyos?</li>
								      	</ul>
								        
										


								      </div>
								    </div>
								  </div>
								 
								 
								  
								 
								 
								</div>
							</div>
							

							 

						</div>


					</div>
					<!-- row2 -->

			</div>

		<br>
			<div class="top-btn-div">
				
				<a class='top-btn' title='regresar' href="#body1">
	  				^
	  			</a>
			
			</div>
		</div>
		<br><br><br><br><br><br><br><br><br><br>

<?php //get_sidebar(); ?>

<?php get_footer(); ?>