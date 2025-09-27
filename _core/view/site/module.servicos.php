<?php if($action != ''){
    if ($view['servico'] === null) {
    ?>
    <main class="wrapper-sm text-start">
        <div class="container">
            <small class="text-uppercase mb-3">Servi&ccedil;o</small>
            <h2 class="display-4 fw-bold text-primary mb-2" id="titulo-top">Servi&ccedil;o n&atilde;o encontrado</h2>
            <div class="text-dark text-opacity-75 small">
                <h3>Navegue abaixo mais informações.</h3>
            </div>  
        </div>
    </main>
    <?php } else{ ?>
    <main class="wrapper-sm">

        <div class="container">          
                <div class="col-md-8 mx-auto">
                    <small class="text-uppercase">Servi&ccedil;o</small>
                    <h1 class="display-5 fw-bold mb-4"><?= $view['servico']->nome.($view['cidade'] ? ' em '.$view['cidade'] : '')?></h1>
                        <?php if($view['servico']->getImage('r') != '') {?>
                            <div class="d-none d-md-block ratio ratio-21x9 rounded-5 border border-white shadow my-5"><img src="<?=$view['servico']->getImage('r')?>" class="rounded-4" style="object-fit:cover"></div>
                            <div class="d-md-none ratio ratio-4x3 rounded-5 border border-white shadow my-5"><img src="<?=$view['servico']->getImage('r')?>" class="rounded-4" style="object-fit:cover"></div>
                            <?php } ?>
                    
                </div>

                <div class="col-md-8 mx-auto">

                        <p>
                            <?=$view['servico']->descricao?>
                        </p>
                </div>

                <div class="bg-white p-5 shadow-sm rounded-5">

                    <h2 class="text-center mb-4">Quer saber mais?</h2>

                    <div class="d-flex flex-wrap gap-3 justify-content-center">

                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalContato" class="gap-3 btn btn-outline-primary border-2 rounded-3 d-flex align-items-center justify-content-center">
                            <i class="ti ti-mail fs-3"></i>Atrav&eacute;s do E-mail
                        </a>  

                        <a href="https://api.whatsapp.com/send/?phone=55<?=Utils::replace('/[^0-9]/', '', $Config->get('whatsapp')).'&text='.urlencode('Olá, gostaria de mais informações sobre '.$view['servico']->nome)?>" target="_blank" class="gap-3 btn btn-outline-success border-2 rounded-3  d-flex align-items-center justify-content-center">
                            <i class="ti ti-brand-whatsapp fs-3"></i> Chamar no WhatsApp
                        </a>

                    </div>

                </div>
        </div>
    </main>  
    <?php } ?>
<?php } ?>

<section id="services" class="posts wrapper-sm bg-white text-center">

    <div class="container">

        <h2 class="h1 text-primary fw-bold mb-4 mb-xxl-5" data-aos="fade"><?=$action != '' ? 'Outros ' : ''?>Serviços</h2>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-5">

        <?php 
            foreach ($view['servicos'] as $servico) { ?>

            <div class="col">

                <article class="position-relative rounded-3 h-100" data-aos="fade-up" data-aos-delay="100">
                    <div class="rounded-3 ratio ratio-4x3 mb-3"><img src="<?=$servico->getImage()?>" class="rounded-4"></div>
                    <h4 class="text-primary fw-bold mt-2">
                        <a href="<?= __PATH__.'servicos/'.Utils::hoturl($servico->id,$servico->nome).($view['url_cidade'] != '' ? '/' : '').$view['url_cidade']  ?>" class="stretched-link text-reset text-decoration-none"><?=$servico->get('nome')?></a>
                    </h4>
                    <?=substr($servico->get('descricao'), 0, 200)?>...
                </article>
            </div>

            <?php } ?>

        </div>

    </div>
</section>

<div class="modal fade" id="modalContato" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content border-0 bg-light p-4 shadow rounded-5" onsubmit="return sendContato(this);">
      <div class="modal-header border-0">
        <h1 class="modal-title fs-4 fw-bold" id="exampleModalLabel">Enviar Contato</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body border-0 d-flex flex-column gap-3">
            
            <input type="hidden" name="tipo" value="servico">
            <input type="hidden" name="interesse" value="<?=(isset($view['servico']) ? $view['servico']->nome : '')?>">
            <input type="hidden" name="<?=Security::csrfGetTokenId()?>" value="<?=Security::csrfGetToken()?>">
            
            <div class="form-floating">
                <input type="text" id="nome" name="nome" class="form-control rounded-3" placeholder="Nome" required="">
                <label for="nome" class="form-label">Nome</label>
            </div>

            <div class="form-floating">
                <input type="email" id="email" name="email" class="form-control rounded-3" placeholder="email" required="">
                <label for="email" class="form-label">E-mail</label>
            </div>

            <div class="form-floating">
                <input type="text" id="telefone" name="telefone" class="form-control rounded-3" placeholder="telefone" required="">
                <label for="telefone" class="form-label">Telefone</label>
            </div>

            <div class="form-floating">
                <textarea id="mensagem" name="mensagem" class="form-control rounded-3" placeholder="mensagem" required="" style="height: 150px;"></textarea>
                <label for="mensagem" class="form-label">Mensagem</label>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="declaracao" value="1" id="declaracao" required>
                <label class="form-check-label" for="declaracao">
                    Aceito a <a href="<?=__PATH__?>politica-privacidade">Politica de Privacidade</a>
                </label>
            </div>  
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn rounded-3 text-dark border-2 btn-default" data-bs-dismiss="modal">Fechar</button>
        <button type="submit" class="btn rounded-3 text-white border-2 btn-primary">Enviar</button>
      </div>
    </form>
    </div>
</div>