var Translator = new Translate();
document.observe('dom:loaded', function() {
    PriceUpdater.init();
});

PriceUpdater = Class.create();
PriceUpdater = {
    init: function() {
        this.formId = 'priceupdate_form';
        this.form = new varienForm(this.formId, true);
        this.websiteId = $('website').value;
        this.categoryUrl = $('category_url').value;
        Validation.add('validate-percent-range', Translator.translate('Please enter a number -100 or greater.'), function(v) {
            return (parseNumber(v) >= parseNumber(-100));
        });
    },
    submit: function() {
        if(this.form.validator && this.form.validator.validate()){
            new Ajax.Request($(this.formId).action, {
                method: 'post',
                parameters: $(this.formId).serialize(),
                onLoading: function() {
                    $$('loader').show();
                },
                onComplete: function(transport) {
                    var response = transport.responseText.evalJSON();
                    if ($('messages') !== null) {
                        $('messages').replace(response.result);
                    } else {
                        $$('.messages')[0].replace(response.result);
                    }
                }
            });
        }
        return false;
    },
    getCategoryListByWebsite: function(websiteId) {
        if (websiteId == null) {
            websiteId = this.websiteId;
        }
        new Ajax.Request(this.categoryUrl, {
            method: 'post',
            parameters: {website: websiteId},
            onLoading: function() {
                $$('loader').show();
            },
            onComplete: function(transport) {
                var response = transport.responseText.evalJSON();
                if (response.result === 'SUCCESS') {
                    $$('.checkboxes').each(function (item) {
                        item.update(response.block);
                    });
                } else {
                    alert(response.message);
                }
            }
        });
        this.init();
    },
    updateRateClass: function(actionId) {
        (actionId === '0') ? $('rate').addClassName('validate-percent-range') : $('rate').removeClassName('validate-percent-range');
    },
    updateCheckboxes: function(element) {
        var checkboxes = $$("#priceupdate_form input[type=checkbox]");
        if (element.value === 'all') {
            checkboxes.each(function (box) {
                box.checked = element.checked;
            });
        } else if (checkboxes[0].checked) {
            checkboxes[0].checked = false;
        }
    }
};