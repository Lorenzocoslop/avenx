<header class="header_area sticky-header">
		<div class="main_menu">
			<nav class="navbar navbar-expand-lg navbar-light main_box">
				<div class="container">
					<!-- Brand and toggle get grouped for better mobile display -->
					<a class="navbar-brand logo_h" href="<?=__PATH__?>"><img src="img/logo.png" alt="" style="width: 160px;"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
					 aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse offset" id="navbarSupportedContent">
						<ul class="nav navbar-nav menu_nav ml-auto">
							<li class="nav-item <?=$request->get('module') == '' ? 'active' : ''?>"><a class="nav-link" href="<?=__PATH__?>">Início</a></li>
							<li class="nav-item <?=$request->get('module') == 'loja' ? 'active' : ''?>"><a class="nav-link" href="<?=__PATH__.'loja'?>">Loja</a></li>
							<li class="nav-item <?=$request->get('module') == 'contato' ? 'active' : ''?>"><a class="nav-link" href="<?=__PATH__.'contato'?>">Contato</a></li>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							<li class="nav-item"><a href="#" class="cart"><span><i class="bi bi-bag"></i></span></a></li>
							<li class="nav-item">
								<button class="search"><span id="search"><i class="bi bi-search"></i></span></button>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</div>
		<div class="search_input" id="search_input_box">
			<div class="container">
				<form class="d-flex justify-content-between">
					<input type="text" class="form-control" id="search_input" placeholder="Search Here">
					<button type="submit" class="btn"></button>
					<span class="lnr lnr-cross" id="close_search" title="Close Search"></span>
				</form>
			</div>
		</div>
	</header>