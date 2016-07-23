/**
 * Created by david on 24/06/16 and corriged by JeanSeb.
 */
$(document).ready(function(){
     $('#insert_image').click(function(event){
        event.preventDefault();
        addImageField();
        return false;
    });

    $('.alert').click(function(event){
        $(this).hide();console.log('passed');
        event.preventDefault();
    });
});

function addInsertImageButton(){
    var emplacement = $('#annonce_images');
    var index = emplacement.find(':input').length;

    if(index == 0){
        addImageField();
    }
    else{
        emplacement.children('div').each(function(){
            addDeleteLink($(this));
        });
    }
}

function addImageField(){
    var emplacement = $('#annonce_images');
    console.log(emplacement);

    var index = emplacement.find(':input').length;

    //Checking if we are editing and, if yes, how many images do we have
    var editKey = $('#edit-nb-images');
    if(editKey.attr('data-images') > 0 ){
        //Key found -> edition
        index = index+parseInt(editKey.attr('data-images'));
        console.log(index);
    }

    if(index == 3){
        emplacement.append('<div class="warning">Attention : Seulement 3 images sont autorisées</div>');
        return;
    }

    var template = emplacement.attr('data-prototype').replace(/__name__label__/g, 'Image n°'+(index+1)).replace(/__name__/g, index).replace('<div>', '<div class="jumbotron">');

    var prototype = $(template);
    addDeleteLink(prototype);
    emplacement.append(prototype);
}

function addDeleteLink(prototype){
    var deleteLink = $('<a href="#" class="button_delete">Supprimer</a>');
    prototype.append(deleteLink);
    deleteLink.click(function(event){
        prototype.remove();
        event.preventDefault();
        return false;
    });
}