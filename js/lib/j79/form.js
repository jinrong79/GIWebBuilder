/**
 *j79Form
 *
 *created by J79\
 * @param {obj} params :
 *                         {
 *                          "uiForm"       : '#form1',               //form selector
 *                          "actionSubmit" : function ,              //action handle when submit. called with form-data.
 *                          "btnLabel"     : '<i class="glyphicon glyphicon-ok"></i> 提交'，                 //btn label for submit.
 *
 *                         }
 *
 *
 */
function j79Form(params) {

    this.params = params;
}


j79Form.prototype = {

    /**
     * setup
     * setup form.
     * @param formData            : form initial data.
     * @param XMLSettingUrl       : form setting xml url.
     * @param flagEdit            : true- is edit form;  false[default]- is addnew form. effect editability of form elements.
     * @param callbackAfterSetup  : callback when finished setup form.
     */
    setup: function (formData, XMLSettingUrl, flagEdit,  callbackAfterSetup, flagViewOnly) {

        var SELF = this;

        flagEdit = flagEdit || false;

        flagViewOnly=flagViewOnly || false;


        var formSetting = {

            'urlXML': XMLSettingUrl,
            'uiForm': SELF.params.uiForm,
            'data': formData,
            'flagEdit': flagEdit,
            "flagView":flagViewOnly,
            'actionFinished': function () {

                j79.hideLoading(SELF.params.uiForm);

                var btnSubmitSelector=SELF.params.uiBtnSubmit || '';

                var handleSubmit=function(e){
                    var result = true;
                    $(SELF.params.uiForm + " [form-input]").each(function () {
                        var singleResult = $(this).validate();
                        if (singleResult == false) {
                            result = false;
                        }
                    });
                    if (result == true) {
                        var formData=SELF.getData();
                        if(SELF.params.actionSubmit && formData!={}){
                            SELF.params.actionSubmit(formData);
                        }

                    } else {
                        alert("信息填写不完整，请检查~");
                    }
                };


                if(btnSubmitSelector==''){//no submit btn selector is provided, then add in after form build.
                    //add submit buttons:
                    var btnLabel=SELF.params.btnLabel || '<i class="glyphicon glyphicon-ok"></i> 提交';
                    $btnSubmit = $('<div class="form-group"><div class="col-md-10 col-md-offset-2">' +
                        '<button class="btn btn-primary btn-lg btn-submit" type="button">'+btnLabel+'</button>' +
                        '</div></div>');
                    $btnSubmit.appendTo($(SELF.params.uiForm));

                    //attach submit button handler
                    $(SELF.params.uiForm + ' .btn-submit').click(function (e) {
                        handleSubmit(e);
                    });

                }else{//if provided submit btn selector

                    //attach submit  handler
                    $(btnSubmitSelector).click(function (e) {
                        handleSubmit(e);
                    });

                }







                //初始化onBlur验证
                $('[form-input]').blur(function(){
                    $(this).validate();
                });

                //hanler when form setup finished.
                if(callbackAfterSetup){
                    callbackAfterSetup();
                }

            }

        };

        var form1 = new j79FormBuilder(formSetting);
        form1.ini();


    },//-/setup

    /**
     * validate
     * validate form, and return result.
     * @returns {boolean} : true -- all valid; false-- some not valid.
     */
    validate:function(){
        SELF=this;
        var result = true;
        $(SELF.params.uiForm + " [form-input]").each(function () {
            var singleResult = $(this).validate();
            if (singleResult == false) {
                result = false;
            }
        });
        return result;
    },//-/validate


    /**
     * getData
     * get form data of valid input.
     * @returns {object}
     */
    getData: function () {
        SELF=this;
        //append form data to postData
        var formData = {};
        $(SELF.params.uiForm+" [form-input]").each(function () {
            var singleResult = $(this).validate();
            if (singleResult == true) {
                if($(this).val()) {
                    formData[$(this).attr('id')] = $(this).val();
                }
            }
        });

        return formData;


    },//-/getData

};
