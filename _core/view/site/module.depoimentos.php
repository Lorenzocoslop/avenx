<?php if( count($view["depoimentos"]) > 0 ){ ?>
<div class="cites wrapper carousel slide text-center text-md-left" id="cites" data-ride="carousel">
    <div class="container">

        <h1 class="text-center">Depoimentos</h1>

        <div class="carousel-inner">

        <?php if( count($view["depoimentos"]) > 1 ){ ?>
            <a class="carousel-control-prev" href=".cites" role="button" data-slide="prev"><i class="icon icon-prev"></i></a>
            <a class="carousel-control-next" href=".cites" role="button" data-slide="next"><i class="icon icon-next"></i></a>
        <?php } ?>    

        <?php 
            $i = 0;
            foreach($view["depoimentos"] as $d){ 
                
        ?>
            <div class="carousel-item <?=($i++ == 0?"active":"")?>">
                <div class="d-md-flex align-items-start"> 
                    <div class="d-md-flex align-items-center col-sm-4 p-0">
                        <img src="img/cite.jpg" class="rounded-circle mr-md-3 mb-3 mb-md-0" alt="Foto sobre Mauris const equat"/>
                        <div class="mb-4 mb-md-0">
                            <h2 class="m-0"><?=$d->get('nome')?></h2>
                            <small><?=$d->get('empresa')?></small>
                        </div>
                    </div>
                    <div><p><?=$d->get('descricao')?></p></div>
                </div>

            </div>
        <?php } ?>

        </div>
    </div>
</div>
<?php } ?>