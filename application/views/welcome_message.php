<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <title>SportOn</title>
  <link rel="stylesheet" href="<?php echo globel_url .'assets/css/normalize.css'; ?>" />
  <link rel="stylesheet" href="<?php echo globel_url .'assets/css/foundation.min.css'; ?>" />
  <link rel="stylesheet" href="<?php echo globel_url .'assets/css/style.css'; ?>" />
  <link rel="stylesheet" href="<?php echo globel_url .'assets/css/ie.css'; ?>" />
  <link rel="stylesheet" href="<?php echo globel_url .'assets/css/flexslider.css'; ?>">
  <link rel="shortcut icon" href="<?php echo globel_url .'assets/img/logo.png'; ?>">
</head>
<body>
<div id="top"  data-magellan-expedition="fixed">
	<div class="row">
		<div class="large-12 columns">
			<nav class="top-bar">
			  <ul class="title-area">
			   <li class="name logo" style="background-color:#b0c4de;">
			     <img style="height: 103px; width: 220px;" src="<?php echo globel_url .'assets/img/logo.png'; ?>">
			    </li>
			  </ul>
			</nav>
		</div>
	</div>
</div>

<header id="header" >
	<div class="row">
    
    	<div class="large-6 columns">
			<div id="teaser-slider-2">
							<div class="flexslider">
								<ul class="slides">
									<li>
										<img src="<?php echo globel_url .'assets/img/slides/iphoneshots.png'; ?>" alt="Petrichor - slider">
									</li>
									<li>
										<img src="<?php echo globel_url .'assets/img/slides/iphoneshots-1.png'; ?>" alt="Petrichor - slider">
									</li>
									<li>
										<img src="<?php echo globel_url .'assets/img/slides/iphoneshots-2.png'; ?>" alt="Petrichor - slider">
									</li>
									<li>
										<img src="<?php echo globel_url .'assets/img/slides/iphoneshots-3.png'; ?>" alt="Petrichor - slider">
									</li>
									
								</ul>
							</div> 
						</div>
		</div>
        
		<div class="large-6 columns"> 
			<h1><b>Play,</b><br>Anything, Anywhere!</h1>
			<a class="download-btn" href="https://play.google.com/store" target="_blank"></a>
		</div>
		
	</div>
</header>

<div class="section product gray" data-magellan-destination="product">
	<div class="row">
		<div class="large-12 columns">
			<div class="row">
				<div class="small-4 columns hints iphone">
					<img src="<?php echo globel_url .'assets/img/app_demo.jpg'; ?>" >
				</div>
				<div class="small-8 columns hints iphone">
					<div class="hint-right">
						<h2>Bring the world a little closer, one game at a time.</h2>
						<span class="subheading">
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tempor enim at nisl ultrices vestibulum. Quisque malesuada libero non diam porta auctor. Duis nulla mi, tristique ut interdum in, bibendum id nulla. Maecenas id facilisis lacus. Nunc purus risus, maximus sed orci vitae,
						</span>	
					</div>
					<div class="hint hint-right hint-bottom">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="section product gray" data-magellan-destination="product">
	<div class="row">
		<div class="large-12 columns">
			<div class="row">
				<div class="small-8 columns hints iphone">
					<div class="hint-right">
						<h2>Now</h2>
						<span class="subheading">
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tempor enim at nisl ultrices vestibulum. Quisque malesuada libero non diam porta auctor. Duis nulla mi, tristique ut interdum in, bibendum id nulla. Maecenas id facilisis lacus. Nunc purus risus, maximus sed orci vitae,
						</span>	
					</div>
					<div class="hint hint-right hint-bottom">
					</div>
				</div>
				<div class="small-4 columns hints iphone">
					<img src="<?php echo globel_url .'assets/img/second_demo.png'; ?>" >
				</div>
			</div>
		</div>
	</div>
</div>

<div id="contact"  class="section contact gray" data-magellan-destination="contact">    
    <div class="row">
		<div class="large-12 columns">
			<h2>Let us know what you think!</h2>
			<span class="subheading">Your feedaback is very important to us. The Sporton team is focused on providing the best experience for our users. Let us know what you think about the app and how we can make it better for you.</span>

    
		<form method="post" action="#" id="contact_form" class="center">
					<div>
						<label for="name">Name *</label>
						<input type="text" class="input-field reset"  id="name" name="name">
					</div>
					<div>
						<label for="email">Email *</label>
						<input type="text" class="input-field reset" id="email" name="email">
					</div>
					<div class="checkbox-div">
						<label>Favorite Sports *</label>
						<ul class="checkbox-grid">
							<?php foreach($categories as $category) { ?>
								<li><label>
										<input  class="reset" type="checkbox" name="sports[]" value="<?php echo $category->category_name; ?>" />
										<span></span>
										<?php echo '&nbsp;'.$category->category_name; ?>
									</label>
								</li>
							<?php } ?>
						</ul>
					</div>
					<div>
						<label>Message *</label>
						<textarea style="height:110px;" class="reset" id="message" name="message" ></textarea>
					</div>
					<a id="button-send" href="#" title="Submit"  class="button" style="width:100%;">Submit</a>
					<div id="success">Your message has been successfully sent!</div>
					<div id="error">Unable to send your message, please try later.</div>
				</form>

        </div>
        </div>
</div>
<footer>
	<div class="row">
		<div class="large-6 columns">
			<ul class="inline-list">
			  <li class="copyright">2015 &copy; SportOn</a></li>
			 
  			</ul>
		</div>
		<div class="large-6 columns">
			<ul class="inline-list social-media right">
				<li><a target="_blank" href="http://www.facebook.com/" class="icon icon-facebook"></a></li>
				<li><a target="_blank" href="htp://twitter.com/" class="icon icon-twitter"></a></li>
				<li><a target="_blank" href="https://plus.google.com/" class="icon icon-googleplus"></a></li>
			</ul>
		</div>
	</div>
</footer>			
  <script src="<?php echo globel_url .'assets/js/custom.modernizr.js'; ?>"></script>	
  <script type="text/javascript" src="<?php echo globel_url .'assets/js/jquery-1.8.2.min.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo globel_url .'assets/js/foundation.min.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo globel_url .'assets/js/functions.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo globel_url .'assets/js/jquery.nicescroll.js'; ?>"></script>
  <script src="<?php echo globel_url .'assets/js/jquery.localscroll-1.2.7.js'; ?>" type="text/javascript"></script>
  <script src="<?php echo globel_url .'assets/js/jquery.scrollTo-1.4.3.1.js'; ?>" type="text/javascript"></script>
  <script src="<?php echo globel_url .'assets/js/jquery.flexslider.js'; ?>" type="text/javascript"></script><!-- Flex slider -->
  <script type="text/javascript" src="<?php echo globel_url .'assets/js/custom.js'; ?>"></script>
</body>
</html>