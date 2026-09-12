jQuery(function ($) {
    'use strict';
    var frame;
    var $imagePreview = $('#cdbm-image-preview');
    var $liveImage = $('#cdbm-live-image');

    function updatePreview() {
        var title = $('#cdbm-title').val() || 'Your update title';
        var description = $('#cdbm-description').val() || 'Your update description will appear here.';
        var category = ($('#cdbm-categories').val().split(',')[0] || 'Your update').trim();
        var buttonText = $('#cdbm-button-text').val() || $('#cdbm-button-text').attr('placeholder');
        $('#cdbm-live-title').text(title);
        $('#cdbm-live-description').text(description);
        $('#cdbm-live-category').text(category);
        $('#cdbm-live-button').text(buttonText).toggleClass('is-hidden', !$('#cdbm-button-url').val());
    }

    $('#cdbm-title, #cdbm-description, #cdbm-categories, #cdbm-button-url, #cdbm-button-text').on('input change', updatePreview);
    $('#cdbm-select-image').on('click', function (event) {
        event.preventDefault();
        frame = wp.media({ title: cdbmAdmin.selectImage, button: { text: cdbmAdmin.useImage }, multiple: false, library: { type: 'image' } });
        frame.on('select', function () {
            var image = frame.state().get('selection').first().toJSON();
            $('#cdbm-image-id').val(image.id); $('#cdbm-image-url').val(image.url);
            $imagePreview.html($('<img>', { src: image.url, alt: '' }));
            $liveImage.html($('<img>', { src: image.url, alt: '' }));
        });
        frame.open();
    });
    $('#cdbm-remove-image').on('click', function (event) { event.preventDefault(); $('#cdbm-image-id, #cdbm-image-url').val(''); $imagePreview.html('<span class="dashicons dashicons-format-image"></span><span>No image selected</span>'); $liveImage.html('<span class="dashicons dashicons-megaphone"></span>'); });
    updatePreview();
});
