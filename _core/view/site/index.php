<?php FBApi::event('PageView', 'Site_PV'); ?>
<!DOCTYPE html>
<html lang="<?=$GLOBALS['Language']?>">
    <head>

        <title><?=html_entity_decode($view['title'],0,$GLOBALS['Charset'])?></title>

        <meta charset="<?=$GLOBALS['Charset']?>">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="<?= $view['keywords'] ?>" />
        <meta name="description" content="<?=trim(Utils::replace('/\s+/', ' ', Utils::subText($view['description'],400)))?>" />
        <meta name="author" content="LEVSISTEMAS" />
        <meta name="reply-to" content="<?=$Config->get('email')?>" />
        <meta name="robots" content="index,follow" />
        <meta name="verify-v1" content="" />

        <link rel="canonical" href="<?=$view['canonical']?>" />
        <link rel="shortcut icon" href="<?= __BASEPATH__ ?>fav-icon.png">

        <?=(isset($view['json-ld']) && count($view['json-ld']) > 0 ? '<script type="application/ld+json">
        '.json_encode($view['json-ld']).'
        </script>' :'')?>

        <?php if(isset($view['og'])) foreach($view['og'] as $k => $v) echo '<meta property="og:'.$k.'" content="'.Utils::replace('/\s+/', ' ', $v).'" />'.PHP_EOL; ?>
        
        <link href="<?=__PATH__?>css/framework.min.css?v=<?=filemtime($defaultPath.'css/framework.min.css')?>" rel="stylesheet">  
        <?php if(file_exists($defaultPath.'css/style.css')){ ?>
            <link href="<?=__PATH__?>css/style.css?v=<?=filemtime($defaultPath.'css/style.css')?>" rel="stylesheet">      
        <?php } ?>

        <?=str_replace('#NONCE#', $HashNonce, html_entity_decode($Config->get('head-scripts'), ENT_QUOTES, $GLOBALS['Charset']))?>  

    </head>

    <body class="<?= $view['page_class'] ?>">
        <?php

        include("header.php");
        include dirname(__FILE__) . "/module." . $view['module'];
        include("footer.php");
        
        if(isset($view['thumbs']) && count($view['thumbs']) > 0){ ?>

        <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" aria-label="image gallery" aria-modal="true" role="dialog" data-start-slideshow="false" data-filter=":even">
            <div class="slides" aria-live="off"></div>
            <h3 class="title">&nbsp;</h3>
            <a href="javascript:;" class="prev" aria-controls="blueimp-gallery" aria-label="Anterior" aria-keyshortcuts="ArrowLeft"></a>
            <a href="javascript:;" class="next" aria-controls="blueimp-gallery" aria-label="Próximo" aria-keyshortcuts="ArrowRight"></a>
            <a href="javascript:;" class="close" aria-controls="blueimp-gallery" aria-label="Fechar" aria-keyshortcuts="Escape"></a>
            <a href="javascript:;" class="play-pause" aria-controls="blueimp-gallery" aria-label="Play" aria-keyshortcuts="Space" aria-pressed="true" role="button" ></a>
            <ol class="indicator"></ol>
        </div>

        <?php } ?>

        <script nonce="<?=$HashNonce?>"> 
            const __PATH__ = '<?= __PATH__ ?>';
            const __BASEPATH__ = '<?= __BASEPATH__ ?>';
        </script>

        <script src="<?=__BASEPATH__?>js/framework.min.js?v=<?=filemtime($defaultPath.'js/framework.min.js')?>"></script>
        <script src="<?=__BASEPATH__?>js/common.js?v=<?=filemtime($defaultPath.'js/common.js')?>"></script>

        <?php if(isset($view['thumbs']) && count($view['thumbs']) > 0){ ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/blueimp-gallery/3.3.0/css/blueimp-gallery.min.css" integrity="sha512-ZpixWcgC4iZJV/pBJcyuoyD9sUsW0jRVBBTDge61Fj99r1XQNv0LtVIrCwHcy61iVTM+/1cXXtak8ywIbyvOdw==" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-gallery/3.3.0/js/jquery.blueimp-gallery.min.js" integrity="sha512-/VEtHEuE2wVQIAautYg+nwpQT0wyKqOkNM8RfpMTob5PTsmy8Pcq0KHvxU59mWaL0+PBkzQBKKJ+SCVrTKw1TQ==" crossorigin="anonymous"></script>
        <?php } ?>
        
        <script nonce="<?=$HashNonce?>">
            function sendContato(form){
                $(form).ajaxSubmit({
                    url: '<?=__PATH__?>ajax/send-mail',
                    type: "POST",
                    dataType: "json",
                    beforeSend: function() {
                        blockUi();
                    },
                    success: function(data, textStatus, jqXHR)
                    {
                        unblockUi();
                        
                        if (data['success']) {
                            <?=( (string) $Config->get('fb-pixel') != '' ? "fbq('track', 'Contact', {eventID: 'Site_Contact'});" : '')?>
                            <?=( (string) $Config->get('ga-id') != '' ? "gtag('event', 'Contact');" : '')?>
                            form.reset();
                            MessageBox.success(data['message']);
                        }else{

                            MessageBox.error(data['message']);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        unblockUi();
                        MessageBox.error('Ocorreu um erro: ' + errorThrown);
                    }
                });
                return false;
            }
            <?php if($view['end_scripts'] != ''){ ?>
            $(function() {
               <?=$view['end_scripts']?>  
            });
            <?php } ?>
        </script>
        
        <?php if( (string) $Config->get('fb-pixel') != ''){ ?>
            <!-- Meta Pixel Code -->
            <script nonce="<?=$HashNonce?>">
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', '<?=(string) $Config->get('fb-pixel')?>');
                fbq('track', 'PageView', {eventID: 'Site_PV'});
            </script>
            <noscript><img alt="" height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=<?=(string) $Config->get('fb-pixel')?>&ev=PageView&eid=Site_PV&noscript=1"
            /></noscript>
            <!-- End Meta Pixel Code -->
        <?php } ?>

        <?php if( (string) $Config->get('ga-id') != ''){ ?>
            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?=(string) $Config->get('ga-id')?>"></script>
            <script nonce="<?=$HashNonce?>">
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', '<?=(string) $Config->get('ga-id')?>');
            </script>
        <?php } ?>

        <?=str_replace('#NONCE#', $HashNonce, html_entity_decode($Config->get('footer-scripts'), ENT_QUOTES, $GLOBALS['Charset']))?>   

    </body>
</html>