<?php

namespace Miniflux\Helper;

function error_class(array $errors, $name)
{
    return ! isset($errors[$name]) ? '' : ' form-error';
}

function error_list(array $errors, $name)
{
    $html = '';

    if (isset($errors[$name])) {
        $html .= '<ul class="form-errors">';

        foreach ($errors[$name] as $error) {
            $html .= '<li>'.escape($error).'</li>';
        }

        $html .= '</ul>';
    }

    return $html;
}

function form_value($values, $name)
{
    if (isset($values->$name)) {
        return 'value="'.escape($values->$name).'"';
    }

    return isset($values[$name]) ? 'value="'.escape($values[$name]).'"' : '';
}

function form_hidden($name, $values = array())
{
    return '<input type="hidden" name="'.$name.'" id="form-'.$name.'" '.form_value($values, $name).'/>';
}

function form_select($name, array $options, $values = array(), array $errors = array(), $class = '')
{
    $html = '<select name="'.$name.'" id="form-'.$name.'" class="'.$class.'">';

    foreach ($options as $id => $value) {
        $html .= '<option value="'.escape($id).'"';

        if (isset($values->$name) && $id == $values->$name) {
            $html .= ' selected="selected"';
        }
        if (isset($values[$name]) && $id == $values[$name]) {
            $html .= ' selected="selected"';
        }

        $html .= '>'.escape($value).'</option>';
    }

    $html .= '</select>';
    $html .= error_list($errors, $name);

    return $html;
}

function form_radio($name, $label, $value, $checked = false, $class = '')
{
    return '<label><input type="radio" name="'.$name.'" class="'.$class.'" value="'.escape($value).'" '.($checked ? 'checked' : '').'>'.escape($label).'</label>';
}

function form_checkbox($name, $label, $value, $checked = false, $class = '')
{
    return '<label><input type="checkbox" name="'.$name.'" class="'.$class.'" value="'.escape($value).'" '.($checked ? 'checked="checked"' : '').'><span>'.escape($label).'</span></label>';
}

function form_label($label, $name, $class = '')
{
    return '<label for="form-'.$name.'" class="'.$class.'">'.escape($label).'</label>';
}

function form_input($type, $name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    $class .= error_class($errors, $name);

    $html = '<input type="'.$type.'" name="'.$name.'" id="form-'.$name.'" '.form_value($values, $name).' class="'.$class.'" ';
    $html .= implode(' ', $attributes).'/>';
    $html .= error_list($errors, $name);

    return $html;
}

function form_text($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('text', $name, $values, $errors, $attributes, $class);
}

function form_password($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('password', $name, $values, $errors, $attributes, $class);
}

function form_number($name, $values = array(), array $errors = array(), array $attributes = array(), $class = '')
{
    return form_input('number', $name, $values, $errors, $attributes, $class);
}
