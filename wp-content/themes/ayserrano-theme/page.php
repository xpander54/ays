<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="main indx-wrap">
			
			<div class="post" id="post-<?php the_ID(); ?>">

				<h2><?php// the_title(); ?></h2>

				<?php// include (TEMPLATEPATH . '/inc/meta.php' ); ?>

					<div class="main">

						<h2><a href="http://www.ayserranosc.wix.com/inicio">Visita sitio temporal</a></h2>
						
						<img src="<?php bloginfo('template_url');?>/img/construccion.png" alt="en construccion" class="thumbnail-img">

					</div>
					
					<div class="row">

						<div class="col-md-6 col-sm-6 col-sm-offset-1 col-md-offset-0">
							<div class="row">
								
								<div class="col-md-4 col-sm-4">
									<h2 class=subtitle>
										
										Coberturas
									</h2>

								</div>
								<div class="col-md-6 col-sm-6 line-subtitle">
									
								</div>

							</div>

							<div class="row">
								

								<div class="coberturas-index">
									<ul class="nav nav-tabs tabs-index">
									  <li class="active">
									  		
									  		<a href="#basica" data-toggle="tab">
									  			Basica
									  		</a>
									  	</li>
									  <li>
									  		<a href="#juridica" data-toggle="tab">
									  			Juridica
									  		</a>
									  		
									  	</li>
									  <li>
									  	<a href="#jmasd" data-toggle="tab">
									  		Juridica + Daños
									  	</a>
									  </li>
									</ul>
								</div>

								<div class="tab-content">
									


									<div class="tab-pane active" id="basica">
										<div class="cobertura-img">
								  			 <img src="<?php bloginfo('template_url');?>/img/coberturas/basica.png" alt="AySerrano" class="coberturas-img">
								  			<p>
								  				Recuperación extrajudicial de las rentas no cubiertas.
								  			</p>
								  		</div>
									</div>

									<div class="tab-pane" id="juridica">

										<div class="cobertura-img">
								  			 <img src="<?php bloginfo('template_url');?>/img/coberturas/juridica.png" alt="AySerrano" class="coberturas-img">
								  			<p>
								  				Resicisión del contrato por incumplimiento de pago
								  			</p>
								  		</div>

										
									</div>
									<div class="tab-pane" id="jmasd">
										<div class="cobertura-img">
								  			 <img src="<?php bloginfo('template_url');?>/img/coberturas/juridicaMsDanos.png" alt="AySerrano" class="coberturas-img">
								  			<p>
								  				Recuperación de pago de servicios y de daños al inmueble.
								  			</p>
								  		</div>
									</div>
							
								</div>

							</div>


						</div> 
						
						<!-- termina col left -->

						<div class="col-md-5 col-sm-5">

							 <div class="row">
								
								
								<div class="col-md-4 col-sm-4 col-subtitle">
										<h2 class=subtitle>
											
											Empresa
										</h2>

								</div>
								<div class="col-md-6 col-sm-6 line-subtitle">
									
								</div>

								

							
							</div>

							<div class="row">

								<br><br>
								

								<div class="panel-group" id="accordion">
 
								  <div class="panel panel-default">
								 
								    <div class="panel-heading">
								      <h4 class="panel-title">
								        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
								          A y Serrano
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
								          ¿Está protegido frente a un impago?
								        </a>
								      </h4>
								    </div>
								    <div id="collapseTwo" class="panel-collapse collapse">
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

		</div>

<?php //get_sidebar(); ?>

<?php get_footer(); ?>