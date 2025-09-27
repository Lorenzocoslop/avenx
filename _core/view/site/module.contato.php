<section id="contact" class="wrapper-sm">

    <div class="container">

        <h2 class="display-3 text-primary fw-bold mb-4" data-aos="fade">Contato</h2>

        <div class="d-flex flex-wrap gap-4 gap-xxl-5 justify-content-between" data-aos="fade-up">

            <div class="d-flex gap-2 gap-md-3 align-items-center lh-sm">
                <div><i class="ti ti-phone fs-2 text-primary"></i></div>
                <div class="fw-bold"><?=$Config->get('telefone')?></div>
            </div>

            <div class="d-flex gap-2 gap-md-3 align-items-center lh-sm">
                <div><i class="ti ti-mail fs-2 text-primary"></i></div>
                <div class="fw-bold"><?=$Config->get('email')?></div>
            </div>

            <div class="d-flex gap-2 gap-md-3 align-items-center lh-sm">
                <div><i class="ti ti-map-pin fs-2 text-primary"></i></div>
                <div class="fw-bold"><?=
                    $Config->get('endereco').
                    ($Config->get('numero')!= '' ? ',' : '').$Config->get('numero').
                    ($Config->get('complemento')!= '' ? ' - ' : '').$Config->get('complemento').
                    ($Config->get('bairro')!= '' ? ' - ' : '').$Config->get('bairro').
                    ($Config->get('cidade')!= '' ? ' - ' : '').$Config->get('cidade').
                    ($Config->get('estado')!= '' ? '/' : '').$Config->get('estado')
                ?></div>
            </div>
            <?php /*
            <a href="https://api.whatsapp.com/send/?phone=55<?=Utils::replace('/[^0-9]/','', $Config->get('whatsapp'))?>" target="_blank" class="d-inline-flex gap-2 text-white align-items-center btn rounded-pill text-uppercase btn-success">
                <i class="ti ti-brand-whatsapp fs-3"></i> Chamar no WhatsApp
            </a>*/ ?>

        </div>
        <?php if($request->get('module') != ''){ ?>
        <form class="mt-5 pt-5 border-top border-opacity-10 needs-validation" data-aos="fade-up" method="post" onsubmit="return sendContato(this);">
            <input type="hidden" name="tipo" value="contato">
            <input type="hidden" name="<?=Security::csrfGetTokenId()?>" value="<?=Security::csrfGetToken()?>">
            
                <h4 class="h3 mb-4">Precisa de ajuda? <strong>Entre em contato agora mesmo!</strong></h4>

                <div class="row g-2 g-md-3">

                    <div class="col-md-3">

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="nome" name="nome" required placeholder="Seu nome">
                            <label for="">Seu nome*</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="email" name="email" required placeholder="Seu e-mail">
                            <label for="">Seu e-mail*</label>
                        </div>
                        
                        <div class="form-floating">
                            <input type="text" class="form-control phone" id="phone" name="telefone" required placeholder="Seu telefone">
                            <label for="">Seu telefone*</label>
                        </div>

                    </div>

                    <div class="col-md-9">

                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Sua mensagem" id="mensagem" name="mensagem" required style="height: 207px"></textarea>
                            <label for="">Mensagem*</label>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="declaracao" value="1" id="declaracao" required>
                                <label class="form-check-label" for="declaracao">
                                    Aceito a <a href="<?=__PATH__?>politica-privacidade">Politica de Privacidade</a>
                                </label>
                            </div>

                            <button class="btn btn-outline-primary border-2 text-reset ms-auto" type="submit">Enviar</button>

                        </div>

                    </div>

                </div>


            </form>

        <?php } ?>

    </div>

</section>

<script nonce="<?=$HashNonce?>">
    function toForm(){
      $('html, body').animate({
          scrollTop: $(".contact-form").offset().top
      }, 100);
    }

    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
            }

            form.classList.add('was-validated')
        }, false)
        })
    })()
</script>
