'use strict';

var currentMUHelperModuleEditor = null;
var currentMUHelperModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getMUHelperModulePopupAttributes()
{
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;

    return 'width=' + pWidth + ',height=' + pHeight + ',location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes';
}

/**
 * Open a popup window with the finder triggered by an editor button.
 */
function MUHelperModuleFinderOpenPopup(editor, editorName)
{
    var popupUrl;

    // Save editor for access in selector window
    currentMUHelperModuleEditor = editor;

    popupUrl = Routing.generate('muhelpermodule_external_finder', { objectType: 'productClass', editor: editorName });

    if (editorName == 'ckeditor') {
        editor.popup(popupUrl, /*width*/ '80%', /*height*/ '70%', getMUHelperModulePopupAttributes());
    } else {
        window.open(popupUrl, '_blank', getMUHelperModulePopupAttributes());
    }
}


var mUHelperModule = {};

mUHelperModule.finder = {};

mUHelperModule.finder.onLoad = function (baseId, selectedId)
{
    if (jQuery('#mUHelperModuleSelectorForm').length < 1) {
        return;
    }
    jQuery('select').not("[id$='pasteAs']").change(mUHelperModule.finder.onParamChanged);
    
    jQuery('.btn-default').click(mUHelperModule.finder.handleCancel);

    var selectedItems = jQuery('#muhelpermoduleItemContainer a');
    selectedItems.bind('click keypress', function (event) {
        event.preventDefault();
        mUHelperModule.finder.selectItem(jQuery(this).data('itemid'));
    });
};

mUHelperModule.finder.onParamChanged = function ()
{
    jQuery('#mUHelperModuleSelectorForm').submit();
};

mUHelperModule.finder.handleCancel = function (event)
{
    var editor;

    event.preventDefault();
    editor = jQuery("[id$='editor']").first().val();
    if ('ckeditor' === editor) {
        mUHelperClosePopup();
    } else if ('quill' === editor) {
        mUHelperClosePopup();
    } else if ('summernote' === editor) {
        mUHelperClosePopup();
    } else if ('tinymce' === editor) {
        mUHelperClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function mUHelperGetPasteSnippet(mode, itemId)
{
    var quoteFinder;
    var itemPath;
    var itemUrl;
    var itemTitle;
    var itemDescription;
    var pasteMode;

    quoteFinder = new RegExp('"', 'g');
    itemPath = jQuery('#path' + itemId).val().replace(quoteFinder, '');
    itemUrl = jQuery('#url' + itemId).val().replace(quoteFinder, '');
    itemTitle = jQuery('#title' + itemId).val().replace(quoteFinder, '').trim();
    itemDescription = jQuery('#desc' + itemId).val().replace(quoteFinder, '').trim();
    pasteMode = jQuery("[id$='pasteAs']").first().val();

    // item ID
    if (pasteMode === '3') {
        return '' + itemId;
    }

    // relative link to detail page
    if (pasteMode === '1') {
        return mode === 'url' ? itemPath : '<a href="' + itemPath + '" title="' + itemDescription + '">' + itemTitle + '</a>';
    }
    // absolute url to detail page
    if (pasteMode === '2') {
        return mode === 'url' ? itemUrl : '<a href="' + itemUrl + '" title="' + itemDescription + '">' + itemTitle + '</a>';
    }

    return '';
}


// User clicks on "select item" button
mUHelperModule.finder.selectItem = function (itemId)
{
    var editor, html;

    html = mUHelperGetPasteSnippet('html', itemId);
    editor = jQuery("[id$='editor']").first().val();
    if ('ckeditor' === editor) {
        if (null !== window.opener.currentMUHelperModuleEditor) {
            window.opener.currentMUHelperModuleEditor.insertHtml(html);
        }
    } else if ('quill' === editor) {
        if (null !== window.opener.currentMUHelperModuleEditor) {
            window.opener.currentMUHelperModuleEditor.clipboard.dangerouslyPasteHTML(window.opener.currentMUHelperModuleEditor.getLength(), html);
        }
    } else if ('summernote' === editor) {
        if (null !== window.opener.currentMUHelperModuleEditor) {
            html = jQuery(html).get(0);
            window.opener.currentMUHelperModuleEditor.invoke('insertNode', html);
        }
    } else if ('tinymce' === editor) {
        window.opener.currentMUHelperModuleEditor.insertContent(html);
    } else {
        alert('Insert into Editor: ' + editor);
    }
    mUHelperClosePopup();
};

function mUHelperClosePopup()
{
    window.opener.focus();
    window.close();
}

jQuery(document).ready(function() {
    mUHelperModule.finder.onLoad();
});
