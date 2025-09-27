<?php if(count($view['clientes'])>0){ ?>
<div class="clients wrapper pt-0">
    <div class="container wrapper text-center">

        <h1 class="color-primary">Clientes</h1>

        <div id="clients" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php  for($a=1; $a<=$paginas; $a++){?>
                <div class="carousel-item <?if($a==1){ echo "active"; }?>">
                    <div class="row">
                        <?php foreach($view['clientes'] as $obj){?>
                            <span class="col-3 col-sm-2 my-3"><img src="<?=$obj->getImage('t')?>" class="img-fluid" alt="..."></span>
                        <?php } ?>
                    </div>  
                </div>
                <?php } ?>
            </div>
            <!--
            <?php if($qtdClientes>$qtdPP){?>
                <a class="carousel-control-prev" href="#clients" role="button" data-slide="prev">
                    <i class="icon color-secondary icon-48"><?=file_get_contents("svg/prev-outline.svg")?></i>
                </a>
                <a class="carousel-control-next" href="#clients" role="button" data-slide="next">
                    <i class="icon color-secondary icon-48"><?=file_get_contents("svg/next-outline.svg")?></i>
                </a>
            <?php }?>
            -->
        </div>

    </div>
</div>
<?php }?>



