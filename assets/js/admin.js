jQuery(function ($) {
    'use strict';
    var frame;
    $('#cdbm-select-image').on('click', function (event) {
        event.preventDefault();
        frame = wp.media({ title: cdbmAdmin.selectImage, button: { text: cdbmAdmin.useImage }, multiple: false, library: { type: 'image' } });
        frame.on('select', function () {
            var image = frame.state().get('selection').first().toJSON();
            $('#cdbm-image-id').val(image.id); $('#cdbm-image-url').val(image.url);
            $('#cdbm-image-preview').html($('<img>', { src: image.url, alt: '' }));
        });
        frame.open();
    });
    $('#cdbm-remove-image').on('click', function () { $('#cdbm-image-id, #cdbm-image-url').val(''); $('#cdbm-image-preview').empty(); });
});