<?php if($request->get('module') == ''){ ?>
    <div class="about">
        <div class="container wrapper">
            <div class="col-md-6 p-0"> 
                <h1 class="color-primary"><?php echo $view["home-sobre"]->get("nome"); ?></h1>
                <?=($view["home-sobre"]->get("descricao")!=''?$view["home-sobre"]->get("descricao"):'');?>
                <a href="<?=__BASEPATH__?>sobre" class="btn btn-secondary-outline">Saiba mais</a>
            </div>  
        </div>
        
    </div>
<?php } ?>

<?php if($request->get('module') == 'sobre'){ ?>
    <div class="about">
        <div class="container wrapper">
            <div class="col-md-6 p-0"> 
                <h1 class="color-primary"><?php echo $view["sobre"]->get("nome"); ?></h1>
                <?=($view["sobre"]->get("descricao")!=''?$view["sobre"]->get("descricao"):'');?>
            </div>  
        </div>
        
    </div>
<?php } ?>