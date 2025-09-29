<?php

class Form {
    private static function parseAttributes($attr){
        $defaultAttr = [
            'type' => 'text',
            'label' => '',
            'size' => '6',
            'id' => md5(microtime()),
            'class' => '',
            'value' => '',
            'options' => [],
            'attributes' => '',
            'maxlength' => '255',
            'rows' => '6',
            'required' => false,
        ];
        return array_merge($defaultAttr, $attr);
    }

    private static function defaultElement($elem, $label='', $id='', $required=false, $size=6){
        return '
        <div class="col-sm-'.$size.' mb-3">
            <div class="form-floating">
                '.$elem.'
                <label for="'.$id.'" class="form-label">'.$label.($required ? '*' : '').'</label>
            </div>
        </div>';
    }

    public static function inputFile($attr=[]){
        return self::inputText($attr+['type' => 'file']);
    }

    public static function inputDate($attr=[]){
        return self::inputText($attr+['type' => 'date']);
    }

    public static function inputDatetime($attr=[]){
        return self::inputText($attr+['type' => 'datetime-local']);
    }
    
    public static function inputMoney($attr=[]){
        $attr['class'] = ($attr['class'] ?? '').' money';
        return self::inputText($attr);
    }

    public static function inputText($attr=[]){
        $attr = self::parseAttributes($attr);
        $elem = '<input name="'.$attr['name'].'" id="'.$attr['id'].'" maxlength="'.$attr['maxlength'].'"'.($attr['required'] ? ' required' : '').' type="'.$attr['type'].'" class="form-control '.$attr['class'].'" value="'.$attr['value'].'" placeholder="Digite" '.$attr['attributes'].'/>';
        return self::defaultElement($elem, $attr['label'], $attr['id'], $attr['required'], $attr['size']);
    }

    public static function inputHidden($attr=[]){
        $attr = self::parseAttributes($attr);
        return '
        <input name="'.$attr['name'].'" id="'.$attr['id'].'" type="hidden" value="'.$attr['value'].'" />';
    }

    public static function select($attr=[]){
        $attr = self::parseAttributes($attr);
        $elem = '<select class="form-select '.$attr['class'].'" name="'.$attr['name'].'" id="'.$attr['id'].'" '.$attr['attributes'].'>';
                foreach($attr['options'] as $key=>$value)
                    $elem .= '<option value="'.$key.'"'.($key == $attr['value'] ? ' selected':'').'>'.$value.'</option>';
        $elem .= '
                </select>';
        return self::defaultElement($elem, $attr['label'], $attr['id'], $attr['required'], $attr['size']);   
    }

    public static function selectBoolean($attr=[]){
        $attr['options'] = ['1' => 'Sim', '0' => 'Não'];
        return self::select($attr);
    }

    public static function textarea($attr=[]){
        $attr = self::parseAttributes($attr);
        $elem = '<textarea class="form-control '.$attr['class'].'" name="'.$attr['name'].'" id="'.$attr['id'].'" rows="'.$attr['rows'].'" '.$attr['attributes'].'>'.$attr['value'].'</textarea>';
        return self::defaultElement($elem, $attr['label'], $attr['id'], $attr['required'], $attr['size']);   
    }
    
    public static function ckeditor($attr=[]){
        $attr = self::parseAttributes($attr);
        return '
        <div class="form-group col-sm-'.$attr['size'].'">
            <label for="'.$attr['id'].'">'.$attr['label'].'</label>
            <textarea class="form-control ckeditor '.$attr['class'].'" name="'.$attr['name'].'" id="'.$attr['id'].'" '.$attr['attributes'].'>'.$attr['value'].'</textarea>
        </div>';
    }
    
    public static function radiobutton($attr=[]){
        $attr = self::parseAttributes($attr);
        
        $elem = '
        <div class="form-group col-sm-'.$attr['size'].' mb-3">
            <p class="mb-0">'.$attr['label'].'</p>
            <div class="btn-group btn-group-justified" data-toggle="buttons">';
            foreach($attr['options'] as $key=>$value){
                $elem .= '
                <input type="radio" name="'.$attr['name'].'" '.($attr['value']==$key?'checked':'').' id="'.$attr['name'].'_'.$key.'" value="'.$key.'" class="btn-check" '.$attr['attributes'].' />
                <label class="btn btn-primary" for="'.$attr['name'].'_'.$key.'">'.$value.'</label>';
            }    
            $elem .= '
            </div>
        </div>';
        return $elem;
    }

    public static function radiobuttonBoolean($attr=[]){
        $attr['options'] = ['1' => 'Sim', '0' => 'Não'];
        return self::radiobutton($attr);
    }

    public static function autoComplete($attr=[]){
        $attr = self::parseAttributes($attr);

        if(!isset($attr['module'])) return '<p class="alert alert-danger">Missing "module" attribute</p>';
        if(!isset($attr['field']) || $attr['field'] == '') $attr['field'] = 'nome';
        if(!isset($attr['filter']) || $attr['filter'] == '') $attr['filter'] = '';

        return '
        <div class="col-sm-'.$attr['size'].' mb-3 position-relative">
            <div class="input-group">
                <div class="form-floating w-25">
                    <input 
                        type="text" 
                        class="form-control" 
                        id="'.$attr['id'].'" 
                        name="'.$attr['name'].'"
                        "'.($attr['required'] ? ' required' : '').' 
                        value="'.$attr['value'].'" 
                        '.$attr['attributes'].' 
                        onblur="getName(`'.$attr['module'].'`, this.value, `nm_'.$attr['id'].'`, `'.$attr['field'].'`)"
                    />
                  <label class="form-label">Id '.$attr['label'].($attr['required'] ? '*' : '').'</label>
                </div>

                <div class="form-floating w-75">
                    <input 
                        type="text" 
                        class="form-control autocomplete" 
                        data-table="'.$attr['module'].'" 
                        data-name="'.$attr['field'].'" 
                        data-filter="'.$attr['filter'].'" 
                        data-field="'.$attr['id'].'" 
                        id="nm_'.$attr['id'].'" 
                        name="nm_'.$attr['name'].'"
                        '.($attr['required'] ? ' required' : '').' 
                        value="'.$attr['nm_value'].'" 
                        '.$attr['attributes'].' 
                    />
                  <label class="form-label">'.$attr['label'].($attr['required'] ? '*' : '').'</label>
                </div>
            </div>
            <div class="d-flex align-items-center lh-1 gap-2 position-absolute end-0 translate-middle-y top-50 me-4">
                <a href="javascript:;" class="bg-white border rounded-circle p-2 opacity-50" onclick="if($(`#'.$attr['id'].'`).val() != ``) modalForm(`'.$attr['module'].'`,$(`#'.$attr['id'].'`).val());"><span class="ti ti-eye fs-4"></span></a>
                <a href="javascript:;" class="bg-light rounded-circle p-2 border border-light" onclick="javascript:modalForm(`'.$attr['module'].'`,0);"><i class="ti fs-4 ti-circle-plus"></i></a>
            </div>
        </div>
        ';
    }
}
