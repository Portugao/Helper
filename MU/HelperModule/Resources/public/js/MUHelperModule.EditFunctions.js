'use strict';

/**
 * Initialises the reset button for a certain date input.
 */
function mUHelperInitDateField(fieldName)
{
    jQuery('#' + fieldName + 'ResetVal').click(function (event) {
        event.preventDefault();
        jQuery('#' + fieldName).val('');
    }).removeClass('hidden');
}

var editedObjectType;
var editedEntityId;
var editForm;
var formButtons;
var triggerValidation = true;

function mUHelperTriggerFormValidation()
{
    mUHelperExecuteCustomValidationConstraints(editedObjectType, editedEntityId);

    if (!editForm.get(0).checkValidity()) {
        // This does not really submit the form,
        // but causes the browser to display the error message
        editForm.find(':submit').first().click();
    }
}

function mUHelperHandleFormSubmit (event) {
    if (triggerValidation) {
        mUHelperTriggerFormValidation();
        if (!editForm.get(0).checkValidity()) {
            event.preventDefault();
            return false;
        }
    }

    // hide form buttons to prevent double submits by accident
    formButtons.each(function (index) {
        jQuery(this).addClass('hidden');
    });

    return true;
}

/**
 * Initialises an entity edit form.
 */
function mUHelperInitEditForm(mode, entityId)
{
    if (jQuery('.muhelper-edit-form').length < 1) {
        return;
    }

    editForm = jQuery('.muhelper-edit-form').first();
    editedObjectType = editForm.attr('id').replace('EditForm', '');
    editedEntityId = entityId;

    var allFormFields = editForm.find('input, select, textarea');
    allFormFields.change(function (event) {
        mUHelperExecuteCustomValidationConstraints(editedObjectType, editedEntityId);
    });

    formButtons = editForm.find('.form-buttons input');
    editForm.find('.btn-danger').first().bind('click keypress', function (event) {
        if (!window.confirm(Translator.__('Do you really want to delete this entry?'))) {
            event.preventDefault();
        }
    });
    editForm.find('button[type=submit]').bind('click keypress', function (event) {
        triggerValidation = !jQuery(this).prop('formnovalidate');
    });
    editForm.submit(mUHelperHandleFormSubmit);

    if (mode != 'create') {
        mUHelperTriggerFormValidation();
    }
}

