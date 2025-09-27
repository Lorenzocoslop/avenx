<?php

$telefone2 = Utils::replace('#[^0-9]#','',$Config->get('whatsapp'));



if(strlen($telefone2)==10){

    $whats =  Utils::Mask($telefone2,'(##) ####-####');

}elseif(strlen($telefone2)==11){

    $whats =  Utils::Mask($telefone2,'(##) #####-####');

}



?>



<footer style="background-color: #e1e1e1;" class="py-5">

    <div class="container py-5">
        
        
        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-5">

            <div class="d-flex flex-column flex-md-row align-items-center justify-content-md-between gap-3 gap-md-5" style="color:#949494;">

                <?php

                foreach($menu as $nome => $url){

                    echo '<a href="'.__PATH__.$url.'"  class="'.($request->get('module')==$url?" active":'').' nav-item nav-link">'.$nome.'</a>';

                }

                ?>

            </div>

            <div class="text-center text-md-start gap-3 d-md-flex align-items-center">
                <div class="mb-3 mb-md-0">
                    &copy; <?= date('Y') ?><span class="d-none d-md-inline">.</span> Todos os direitos reservados.
                </div>
                <a href="https://www.levsistemas.com.br" target="_blank" rel="noopener" class="mt-3 mt-md-0">
                    <img src="https://levsistemas.com.br/addons/favicon.ico" alt="">
                </a>
            </div>

        </div>

    </div>

</footer>