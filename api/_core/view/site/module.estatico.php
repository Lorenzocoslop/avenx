
<div class="post wrapper">
    
    <div class="container">
        <div class="post-header">
            <h1 class="color-primary"><?=$view['estatico']->get('nome')?></h1>
            <!-- <small>17/07/2017</small> -->
            <?=($view['estatico']->get('nome')!=''?"<p>".$view['estatico']->get('subtitulo')."</p>":"")?>
            <p></p>
        </div>
           
        <?php if($view['estatico']->getImage('r') != ""){ ?>        
            <span class="ratio ratio-16p9">
                <img src="<?=$view['estatico']->getImage('r')?>"/>
            </span>
        <?php } ?>
        
        <div class="post-body">
            <?=$view['estatico']->get('descricao')?>
        </div>

        
    </div>

</div>