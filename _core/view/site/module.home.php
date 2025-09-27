<?php
$telefone2 = Utils::replace('#[^0-9]#','',$Config->get('whatsapp'));

if(strlen($telefone2)==10){
    $whats =  Utils::Mask($telefone2,'(##) ####-####');
}elseif(strlen($telefone2)==11){
    $whats =  Utils::Mask($telefone2,'(##) #####-####');
}
?>

<?php if(count($view['banners'])>0){ ?>
    <?php foreach ($view['banners'] as $obj) { ?>
        <div class="hero text-white">  
            <div class="hero-caption container text-center">
                <h1 class="mb-4 mb-md-5 animated fadeInUp delay1 duration500 color-primary"><?=$obj->get('nome')?></h1>
                <p class="mb-4 animated fadeInUp delay2 duration500"><?=$obj->get('descricao')?></p>
                <?php if($obj->get('url')!=''){ ?>
                    <a href="<?=__BASEPATH__?>recursos" target="<?=$obj->get('janela')?>" class="btn btn-primary-outline mt-4 animated fadeInUp delay3 duration500"><?=$obj->get('botao')?></a>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>



