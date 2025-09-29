<?php
if ($id > 0 || $action != '') {

    if ($view['publicacao']->get('id') == '' || $view['publicacao']->get('titulo') == '') {
?>
        <main class="wrapper-sm bg-light text-start">
            <div class="container">
                <small>BLOG</small>
                <h2 class="display-4 fw-bold text-primary mb-2" id="titulo-top">Publicação não encontrada</h2>
                <div class="text-dark text-opacity-75 small">
                    <h3>Navegue abaixo para mais informações.</h3>
                </div>
            </div>


        </main>
    <?php } else { ?>
        <main class="wrapper-sm bg-light">

            <div class="container">
                <div class="col-md-8 mx-auto">
                    <small>BLOG</small>
                    <h1 class="display-5 fw-bold mb-4 text-primary"><?= $view['publicacao']->titulo ?></h1>
                </div>

                <?php if ($view['publicacao']->getImage() != '') { ?>
                    <div class="d-none d-md-block ratio ratio-21x9 rounded-5 border border-white shadow mt-5"><img src="<?= $view['publicacao']->getImage('r') ?>" class="rounded-4" style="object-fit:cover"></div>
                    <div class="d-md-none ratio ratio-4x3 rounded-5 border border-white shadow mt-5"><img src="<?= $view['publicacao']->getImage('r') ?>" class="rounded-4" style="object-fit:cover"></div>
                <?php } ?>

                <?php if (count($view['thumbs'])) { ?>
                    <div class="carousel slide mb-5" data-bs-ride="carousel" id="thumbs">
                        <div class="carousel-inner">
                            <div class="carousel-item active mb-4">
                                <div class="row row-cols-3 row-cols-md-6 g-3 m-0 justify-content-center">
                                    <?php $x = 0;
                                    $i = 0;
                                    foreach ($view['thumbs'] as $foto) {
                                        if ($x > 0 && $x % 6 == 0) {
                                            echo '</div></div><div class="carousel-item mb-4"><div class="row row-cols-3 row-cols-md-6 g-3 m-0 justify-content-center">';
                                        } ?>
                                        <div class="col"><a href="javascript:;" onclick="showGallery(<?= $i ?>)" class="ratio ratio-1x1 border d-block rounded-4"><img src="<?= $foto['r'] ?>" alt="<?= $foto['desc'] ?>"></a></div>
                                    <?php $i++; $x++;
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($i > 6) { ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#thumbs" data-bs-slide="prev" style="margin-left: -2rem; opacity: 1">
                                <span class="bg-dark p-1 rounded-3 d-flex align-items-center"><span class="carousel-control-prev-icon" style="height: 1.5rem; width: 1.5rem" aria-hidden="true"></span></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#thumbs" data-bs-slide="next" style="margin-right: -2rem; opacity: 1">
                                <span class="bg-dark p-1  rounded-3 d-flex align-items-center"><span class="carousel-control-next-icon" style="height: 1.5rem; width: 1.5rem" aria-hidden="true"></span></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        <?php } ?>

                    </div>
                <?php } ?>

                <div class="col-md-8 mx-auto">
                    <p><?= $view['publicacao']->descricao ?></p>
                    <?= ($view['publicacao']->video != '' ? Utils::getEmbed($view['publicacao']->video, '100%', '400px') : '') ?>
                </div>
                <div class="row row-cols-1 p-5">
                    <div class="d-flex justify-content-center">
                        <a href="https://api.whatsapp.com/send/?phone=55<?= Utils::replace('/[^0-9]/', '', $Config->get('whatsapp')) . '&text=' . urlencode('Estou lendo a publicação "' . $view['publicacao']->get('titulo') . '" e gostaria de mais detalhes'); ?>" target="_blank" class=" btn btn-outline-success rounded-pill d-flex align-items-center w-50 justify-content-center">
                            <i class="ti ti-brand-whatsapp fs-3"></i> Quero saber mais</a>
                    </div>
                </div>
            </div>

        </main>
    <?php
        echo isset($view['gallery']) ? '
    <script>
        function showGallery(index){
            blueimp.Gallery(' . $view['gallery'] . ').slide(index,0);
        }
    </script>
    ' : '';
    } ?>
<?php } ?>

<?php

$rs = Publicacao::search([
    's' => 'id',
    'w' => 'ativo = 1 AND id <>' . $id,
]);

$view['publicacoes'] = array();
while ($rs->next()) {
    $view['publicacoes'][] = Publicacao::load($rs->getInt('id'));
}

if (count($view['publicacoes']) > 0) { ?>
    <section class="posts wrapper-sm bg-light">

        <div class="container">

            <h2 class="h1 text-primary fw-bold mb-4 mb-xxl-5" data-aos="fade">Blog</h2>

            <div id="publicacoes">

            </div>


        </div>

    </section>

<?php } ?>

<script>
    function listPublicacoes(id, page) {
        const url = '<?= __PATH__ . $request->get('module') ?>/list/id/' + id + '/page/' + page;
        if (page == 1) {
            $('#publicacoes').html('');
        }
        $.ajax({
            dataType: 'json',
            type: "GET",
            url: url,
            success: function(resp) {
                $('#publicacoes').html(resp.html);
                AOS.init();
            }
        });
        return false;
    }

    function toUp() {
        $('html, body').animate({
            scrollTop: $("#publicacoes").offset().top
        });
    }

    function changeUrl(url, titulo = '') {
        if (history.replaceState) {
            var estadoObjeto = {
                url: url
            };
            history.replaceState(estadoObjeto, titulo, url);
        } else console.warn("O navegador não suporta HTML5 replaceState.");
        listPublicacoes(<?= $id ?>);
    }
</script>