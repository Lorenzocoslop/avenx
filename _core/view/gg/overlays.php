<div class="modal modal-xl fade bg-white-sm-down" id="perfil" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-lg">
        <div class="modal-content">
            <form id="form-perfil" name="form-perfil" method="post" onsubmit="return savePerfil();">
                <div class="modal-header">
                    <h4 class="modal-title fw-bold text-uppercase" id="perfilModalLabel">Perfil</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="row">


                        <?php
                        echo Form::inputFile([
                            'size' => 12,
                            'label' => 'Foto de perfil<small class="rule">'.implode(', ',Image::$typesAllowed).'</small>',
                            'type' => "file",
                            'name' => 'img',
                            'id' => 'input_img_'.$objSession->getTableName(),
                            'attributes' => 'onchange="showPreview(this, `img`, `'.$objSession->getTableName().'`);"'
                        ]);
    
                        echo GG::getPreviewImage($objSession);
                        ?>

                        <?=
                        Form::inputText([
                            'size' => 6,
                            'name' => 'perfilNome',
                            'label' => 'Nome',
                            'value' => $objSession->get('nome'),
                        ]); 
                        ?>
                        <?=
                        Form::inputText([
                            'size' => 6,
                            'name' => 'perfilEmail',
                            'type' => 'email',
                            'label' => 'E-mail',
                            'value' => $objSession->get('email'),
                        ]); 
                        ?>
                        
                        <?=
                        Form::inputText([
                            'size' => 6,
                            'name' => 'perfilLogin',
                            'label' => 'Usu&aacute;rio',
                            'value' => $objSession->get('login'),
                        ]); 
                        ?>
                        
                        <?=
                        Form::inputText([
                            'size' => 6,
                            'name' => 'perfilTel',
                            'label' => 'Telefone',
                            'class' => 'phone',
                            'value' => $objSession->get('tel'),
                        ]); 
                        ?>

                        <?=
                        Form::inputText([
                            'size' => 6,
                            'name' => 'perfilSenha',
                            'label' => 'Nova senha',
                            'type' => 'password',
                            'attributes' => 'placeholder="senha" autocomplete="new-password" data-type="togglePassword"'
                        ]); 
                        ?>
                        <?=
                        Form::inputText([
                            'size' => 6,
                            'name' => 'perfilC_senha',
                            'label' => 'Confirmar nova senha',
                            'type' => 'password',
                            'attributes' => 'placeholder="senha" autocomplete="new-password" data-type="togglePassword"'
                        ]); 
                        ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-secondary"><span class="glyphicon glyphicon-save"></span> Salvar</button>
                </div>

            </form>
        </div>
    </div>
</div>


<div class="modal modal-xl fade bg-white-sm-down" id="overMessage" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"></h4>
            </div>                        

            <div class="modal-body">
                <div class="row" id="txtOverMessage">

                </div>
            </div>
        </div>
    </div>
</div>

<?php if($view['list-filter'] != ''){ ?>
<div class="modal modal-xl fade bg-white-sm-down modal-lg" id="modalFilter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <form id="formFilter" data-module="<?=$view['modulo']?>" class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold text-uppercase">Filtro</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>                        

            <div class="modal-body p-4">
                <div class="row">
                    <?=$view['list-filter']?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary px-3 py-2" data-bs-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-secondary text-white px-3 py-2"><i class="ti ti-filter"></i> Filtrar</button>
            </div>
        </form>
    </div>
</div>
<?php } ?>

<?php if($objSession->hasPermition('over.parametros')){ ?>


<div class="modal modal-xl fade bg-white-sm-down modal-xl" id="parametros" tabindex="-1" aria-labelledby="labelParametros" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="form-config-parametros" method="post" onsubmit="javascript: return saveConfig('parametros');">
                <div class="modal-header">
                    <h4 class="modal-title fw-bold text-uppercase" id="labelParametros">Par&acirc;metros Gerais</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        
                    <?= Form::inputText([       
                        'size' => 6,
                        'name' => 'facebook',
                        'label' => 'Facebook',
                        'value' => $Config->get('facebook'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 6,
                        'name' => 'instagram',
                        'label' => 'Instagram',
                        'value' => $Config->get('instagram'),
                        ]); ?>
                        
                        <?= Form::inputText([       
                        'size' => 6,
                        'name' => 'linkedin',
                        'label' => 'Linkedin',
                        'value' => $Config->get('linkedin'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 6,
                        'name' => 'youtube',
                        'label' => 'Youtube',
                        'value' => $Config->get('youtube'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 12,
                        'name' => 'googlemaps',
                        'label' => 'Link do Google Maps',
                        'value' => $Config->get('googlemaps'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 12,
                        'name' => 'fb-access-token',
                        'label' => 'Facebook API AccessToken',
                        'value' => $Config->get('fb-access-token'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 6,
                        'name' => 'fb-pixel',
                        'label' => 'Facebook FBPixelID',
                        'value' => $Config->get('fb-pixel'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 6,
                        'name' => 'ga-id',
                        'label' => 'Google Analytics ID',
                        'value' => $Config->get('ga-id'),
                        ]); ?>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary px-3 py-2" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" id="btnSave-parametros" class="btn btn-secondary px-3 py-2 text-white d-flex align-items-center gap-2"><i class="ti ti-device-floppy"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php } ?>
<?php if($objSession->hasPermition('over.metatags')){ ?>


<div class="modal modal-xl fade bg-white-sm-down modal-lg" id="metatags" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="form-config-meta" method="post" onsubmit="javascript: return saveConfig('meta');">
                <div class="modal-header">
                    <h4 class="modal-title fw-bold text-uppercase">Meta tags</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">

                        <div class="col-sm-12 mb-3">
                            <div class="alert alert-secondary" role="alert">
                                <h4 class="alert-heading fw-bold">O que significam?</h4>
                                <hr>
                                <p class="mb-0"> As Meta Tags s&atilde;o utilizadas para passar aos sites de busca, como o Bing e o Google, instru&ccedil;&otilde;es sobre o t&iacute;tulo da p&aacute;gina e uma breve descri&ccedil;&atilde;o a ser exibida nos resultados de busca, entre outras instru&ccedil;&otilde;es.</p>
                            </div>
                        </div>

                        <?= Form::inputText([       
                        'size' => 12,
                        'name' => 'nome-site',
                        'label' => 'T&iacute;tulo do Site <small class="rule">(M&aacute;ximo de 70 caracteres)</small>',
                        'maxlength' => '70',
                        'value' => $Config->get('nome-site'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 12,
                        'name' => 'slogan',
                        'label' => 'Slogan <small class="rule">(M&aacute;ximo de 70 caracteres)</small>',
                        'maxlength' => '70',
                        'value' => $Config->get('slogan'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 12,
                        'name' => 'meta-desc',
                        'label' => 'Descri&ccedil;&atilde;o <small class="rule">(M&aacute;ximo de 140 caracteres)</small>',
                        'maxlength' => '140',
                        'value' => $Config->get('meta-desc'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 11,
                        'name' => 'keywords',
                        'label' => 'Keywords  <small class="rule">(m&aacute;x. 10 keywords separadas por v&iacute;rgula)</small>',
                        'attributes' => '',
                        'value' => $Config->get('keywords'),
                        ]); ?>

                        <div class="col-sm-1 mb-3">
                            <button type="button" class="w-100 h-100 btn btn-md btn-dark small text-white" data-bs-toggle="tooltip" data-bs-placement="top" title="Keywords, em portugu&ecirc;s palavras-chave, s&atilde;o os termos principais que determinam qual &eacute; o assunto de uma determinada p&aacute;gina da internet. &eacute; muito importante escolher as keywords certas, pois &eacute; baseado nelas que os mecanismos de buscas exibem seus resultados. Quando voc&ecirc; busca na internet pela palavra ''eletrodom&eacute;sticos'', por exemplo, s&atilde;o listados todos os sites que possuam essa como uma de suas keywords."><span class="ti ti-help"></span></button>
                        </div>

                        <?= Form::textarea([       
                        'size' => 12,
                        'name' => 'head-scripts',
                        'label' => 'C&oacute;digos para incluir no cabe&ccedil;alho <small class="rule">(css, javascript, google analytics, facebook...)</small>',
                        'attributes' => 'style = "height: 200px"',
                        'value' => $Config->get('head-scripts'),
                        ]); ?>

                        <?= Form::textarea([       
                        'size' => 12,
                        'name' => 'footer-scripts',
                        'label' => 'C&oacute;digos para incluir no final da p&aacute;gina <small class="rule">(css, javascript, google analytics, facebook...)</small>',
                        'attributes' => 'style = "height: 200px"',
                        'value' => $Config->get('footer-scripts'),
                        ]); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose-meta" class="btn btn-primary px-3 py-2" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" id="btnSave-meta" class="btn btn-secondary d-flex align-items-center gap-1 px-3 py-2 text-white"><i class="ti ti-device-floppy"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php } ?>
<?php if($objSession->hasPermition('over.contato')){ ?>


<div class="modal modal-xl modal-lg bg-white-sm-down fade" id="contato" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="form-config-contato" method="post" onsubmit="javascript: return saveConfig('contato');">
                <div class="modal-header">
                    <h4 class="modal-title fw-bold text-uppercase">Contato</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">

                        <?= Form::inputText([
                        'size' => 12,
                        'name' => 'razao',
                        'label' => 'Nome da Empresa',
                        'value' => $Config->get('razao'),
                        ]); ?>

                        <?= Form::inputText([
                        'size' => 6,
                        'name' => 'documento',
                        'label' => 'CPF/CNPJ',
                        'class' => 'cpfcnpj',
                        'value' => $Config->get('documento'),
                        ]); ?>

                        <?= Form::inputText([
                        'size' => 6,
                        'name' => 'email',
                        'type' => 'email',
                        'label' => 'E-mail',
                        'value' => $Config->get('email'),
                        ]); ?>

                        <?= Form::inputText([
                        'size' => 4,
                        'name' => 'cep',
                        'label' => 'CEP',
                        'class' => 'cep',
                        'value' => $Config->get('cep'),
                        ]); ?>

                        <?= Form::inputText([
                        'size' => 6,
                        'name' => 'endereco',
                        'label' => 'Rua',
                        'value' => $Config->get('endereco'),
                        ]); ?>

                        <?= Form::inputText([
                        'size' => 2,
                        'name' => 'numero',
                        'label' => 'N&uacute;mero',
                        'value' => $Config->get('numero'),
                        ]); ?>

                        <?= Form::inputText([
                        'size' => 4,
                        'name' => 'complemento',
                        'label' => 'Complemento',
                        'value' => $Config->get('complemento'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 4,
                        'name' => 'bairro',
                        'label' => 'Bairro',
                        'value' => $Config->get('bairro'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 4,
                        'name' => 'cidade',
                        'label' => 'Cidade',
                        'value' => $Config->get('cidade'),
                        ]); ?>

                        <?php 
                        foreach($GLOBALS['Estados'] as $key){
                            $opcoes[$key] = $key;
                        }
                
                        echo Form::select([       
                            'size' => 4,
                            'name' => 'estado',
                            'label' => 'Estado',
                            'options' => $opcoes,
                            'value' => $Config->get('estado'),
                            'class' => 'form-select'
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 4,
                        'name' => 'telefone',
                        'label' => 'Telefone Fixo',
                        'class' => 'phone',
                        'value' => $Config->get('telefone'),
                        ]); ?>

                        <?= Form::inputText([       
                        'size' => 4,
                        'name' => 'whatsapp',
                        'label' => 'Whatsapp',
                        'class' => 'phone',
                        'value' => $Config->get('whatsapp'),
                        ]); ?>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" id="btnClose-contato" class="btn px-3 py-2 btn-primary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" id="btnSave-contato" class="btn px-3 py-2 btn-secondary text-white"><i class="ti ti-device-floppy"></i> Salvar</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php } ?>