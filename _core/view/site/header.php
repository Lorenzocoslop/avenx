<?php

$telefone2 = Utils::replace('#[^0-9]#','',$Config->get('whatsapp'));



if(strlen($telefone2)==10){

    $whats =  Utils::Mask($telefone2,'(##) ####-####');

}elseif(strlen($telefone2)==11){

    $whats =  Utils::Mask($telefone2,'(##) #####-####');

}



?>

<header class="navbar navbar-expand-lg flex-column flex-md-row bg-white">
  <div class="container-xxl align-items-center justify-content-between">
    <a href="<?=__PATH__?>" class="navbar-brand" <?php if(empty($p)){?>data-aos="fade-left" data-aos-delay="200"<?php } ?>><img src="<?=__BASEPATH__?>img/brand.png" class="img-fluid object-fit-contain" style="width:clamp(100px, calc(100px + 7vw), 200px);" alt=""></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Alternar navegação">
      <span class="navbar-toggler-icon"></span>
      <span class="navbar-toggler-icon"></span>
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menu">

      <div class="navbar-nav gap-md-5 ms-auto align-items-md-center position-relative" <?php if(empty($p)){?>data-aos="fade-right" data-aos-delay="200"<?php } ?>>
        <a href="javascript:;" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="menu" class="p-5 fs-1 text-white position-absolute text-decoration-none top-0 end-0 d-lg-none">&times;</a>
        <?php 
          $menu = [
            "Home" => "",
            "Sobre n&oacute;s" => "sobre",
            "Serviços" => "servicos",
            "Blog" => "blog",
            "Contato" => "contato"
          ];

          foreach($menu as $nome => $url)
            echo '<a href="'.__PATH__.$url.'"  class="'.($request->get('module')==$url?" active":'').' nav-item nav-link">'.$nome.'</a>';
        ?>
      </div>
    </div>
  </div>
</header>