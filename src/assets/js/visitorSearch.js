/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function setField(name) {
    $('input#field').val(name);
    if (name === '') {
        $('input#field').val('');
        $('input#search').val('');
    }
    $('form#search-form').submit();
}

