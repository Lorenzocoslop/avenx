<main id="loginPage" class="bg-gradient-animate p-0 h-100 d-flex align-items-center">

    <div class="container d-flex flex-column align-items-center justify-content-center p-0">

        <div class="overflow-hidden border border-2 border-secondary shadow-lg col-md-6 col-xl-4 col-sm-12" id="loginContent">
            <div class="d-flex flex-column p-5 bg-primary">
                <div class="d-flex align-items-center pb-5">
                    <img src="<?=__BASEPATH__?>img/brand-white.png" alt="" width="150" loading="lazy" >
                </div>
                <slot id="changePassword">
                    <form method="post" class="d-flex flex-column justify-content-between h-100">
                        <div>
                            <h4 class="text-secondary fw-bold lh-1">Recupera&ccedil;&atilde;o de senha</h4>
                            <p class="text-white">Informe seu dado no campo abaixo:</p>
                        </div>
            
                        <div class="form-floating mb-3">
                            <input type="text" name="login" id="login" class="form-control cpfcnpj user-remember" placeholder="Digite seu usu&aacute;rio" required autofocus>
                            <label class="form-label">Login / E-mail / Telefone</label>
                        </div>
        
                        <div class="d-flex flex-wrap-reverse flex-lg-nowrap align-items-center gap-3 mt-3">
                            <a href="<?=__PATH__?>" class="btn btn-outline-secondary flex-fill col-sm-4">Voltar</a>
                            <button class="btn btn-secondary flex-fill col-sm-8" type="submit">Avançar</button> 
                        </div>
            
                    </form>
                </slot>
            </div>
        </div>
        <?php include 'inc.assinatura.php'; ?>
    </div>
</main>

<script nonce="<?=$HashNonce?>">
    <?php
    $view['end_scripts'] .= '
    $(`form`).on(`submit`, function(e){
		e.preventDefault();
        sendEmail(this);
        return false;
	});
    ';
    ?>
    function sendEmail(form) {
        const retorno = '#changePassword';
        const url = '<?=__PATH__.$request->get('module')?>/send-email';
        form.preventDefault;
        $(form).ajaxSubmit({
            type: "POST",
            url: url,
            dataType: 'json',
            beforeSend: () => {
                blockUi();
            },
            success: function (resp) {
                if(resp.success) {
                    $(retorno).html(resp.html);
                    
                    $(`#reenviarCodigoLink`).on('click', function(e){
                        e.preventDefault();
                        reenviarEmail($(this).data('token'), $(this).data('userid'));
                    }); 

                    $(`#confirmarEmail`).on('click', function(e){
                        e.preventDefault();
                        confirmEmail();
                    });

                    document.getElementById("digits").addEventListener("paste", (e) => {
                        e.preventDefault();
                    
                        const text = (e.clipboardData || window.clipboardData).getData("text");
                    
                        const chars = text.split("");
                    
                        const inputs = document.querySelectorAll("#digits input");
                    
                        chars.forEach((char, index) => {
                            if (inputs[index]) {
                                inputs[index].value = char;
                            }
                        });
                    });

                    MessageBox.success(resp.message);
                    listenerInputs();
                    return true;
                }
                MessageBox.error(resp.message);
                return false;
            },
            error: function (xhr, status, error) {
                MessageBox.error('Ocorreu um erro. Para detalhes pressione F12 e verifique no console.');
                console.error(xhr.responseText);
            },
            complete: () => {
                unblockUi();
            }
        });

        return false;
    }

    function reenviarEmail(token, id) {
        const url = '<?=__PATH__.$request->get('module')?>/reenviar-email/token/' + token + '/id/' + id;

        $.ajax({
            url: url,
            dataType: `json`,
            type: 'GET',

            success: function(resp) {
                if(resp.success) {
                    MessageBox.success(resp.message);
                }else{
                    MessageBox.error(resp.message);
                }
            },
            error: function(xhr, status, error) {
                MessageBox.error('Ocorreu um erro. Para detalhes pressione F12 e verifique no console.');
                console.error(xhr.responseText);
            }
        })
    }

    function confirmEmail(){
        
        const retorno = '#changePassword';
        var valor = $('#digit1').val()+$('#digit2').val()+$('#digit3').val()+$('#digit4').val()+$('#digit5').val()+$('#digit6').val();

        if (valor == '') {
            MessageBox.error('O Token precisa ser informado!');
            return false;
        }

        $.ajax({
            url: `<?=__PATH__.$request->get('module')?>/confirm-email`,
            dataType: `json`,
            type: 'POST',
            data:{
                id: id_usuario.value,
                token : valor
            },
            success: function (resp) {
                if (resp.success) {
                    MessageBox.success(resp.message);
                    $(retorno)
                        .html(resp.html)
                        .find('form')
                        .on('submit', function(e){
                            e.preventDefault();
                            changePassword(this);
                        });
                }else{
                    MessageBox.error(resp.message);
                }
            },
            error: function (xhr, status, error) {
                MessageBox.error('Ocorreu um erro. Para detalhes pressione F12 e verifique no console.');
                console.error(xhr.responseText);
        }
        });

        return false;
  
    }

    function changePassword(form) {
        const url = `<?=__PATH__.$request->get('module')?>/change-password`;
        $(form).ajaxSubmit({
            type: "POST",
            url: url,
            dataType: `json`,
            success: function (resp) {
                if(resp.success) {
                    MessageBox.success(`Senha alterada com sucesso`);
                    setTimeout(function() {
                    window.location.href = `<?=__PATH__?>`;
                    }, 3000);
                } else {
                    MessageBox.error(`Senhas não confeerem.`);
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

        return false;
    }

    function listenerInputs(){
        nextEmpty();
        $(".digit-input").on("input", handleInput)
                    .on("keydown", handleKey);
  
        function handleInput(e){    
            var len = $(this).val().length;
            if(len){
                if( $(e.currentTarget).index() < 5 ){
                    next(e);
                }
            }
        }
        function handleKey(e){
            var len = $(this).val().length;
            if(e.which == 8 && len === 0){
                setTimeout(function(){ prev(e); }, 10);
            }
        }
  
        function prev(e){
            var $cur = $(e.currentTarget);
            var target = $cur.index() - 1;
            $(`.digits input:eq(${target})`).focus().select();
        }
        function next(e){
            var $cur = $(e.currentTarget);
            var target = $cur.index() + 1;
            $(`.digits input:eq(${target})`).focus().select();
        }
        function nextEmpty(){
        $(".digits input[value='']").filter(":first").focus();
        }
        function getCode(){
            var sb = "";
            $('.digits input').each(function(){
            sb += $(this).val();
            });
            return sb;
        }
    }

    
</script>