/**
 * editBase
 */
class editBase{

    constructor(params) {

        //parse params and initialize object attribute value.
        this.parseParam(params);

    }//-/

    /**
     * parseParam
     * parse params and initialize object attribute value
     * @param params
     */
    parseParam(params){

        if(typeof(params)=='undefined'){
            params={};
        }




        //ui:
        //ui.form -- form id
        //ui.submit -- submit button id
        this.ui=params.ui || {"form":"form"};

        //form input setting xml url:
        this.addFormXMLUrl=params.addFormXMLUrl || null;
        this.editFormXMLUrl=params.editFormXMLUrl || null;

        //data: detail data when edit.
        this.data=params.data || null;

        //mode: 0-- add new; 1-- modify; 2-- static view
        this.mode=params.mode || 0;

        //remote url to communicate with:
        this.url=params.url || null;

        //url to get detail data, if not provided , then use this.url.
        this.urlGetDetail=params.urlGetDetail || this.url || null;

        //url to add new data, if not provided , then use this.url.
        this.urlAdd=params.urlAdd || this.url || null;

        //url to edit data, if not provided , then use this.url.
        this.urlEdit=params.urlEdit || this.url || null;

        //url to delete data,  if not provided , then use this.url.
        this.urlDelete=params.urlDelete || this.url || null;

        //requestType:
        this.requestType=params.requestType || "POST";


        //handler of success add
        this.successSubmitAdd=params.successSubmitAdd || null;

        //handler of success edit.
        this.successSubmitEdit=params.successSubmitEdit || null;


        //handler of failed Add
        this.failSubmitAdd=params.failSubmitAdd || null;

        //handler of failed Edit
        this.failSubmitEdit=params.failSubmitEdit || null;



    }//-/

    /**
     * getDataTransporter
     * @returns {dataTransporterBase}
     */
    getDataTransporter(){
        //need overwrite in sub class:
        return new dataTransporterBase();
    }//-/


    /**
     * parseDetailDataFromResult
     * parse detail data from result from server.
     * need overwrite in subclass to meet special need.
     * @param resultData
     * @returns {*}
     */
    parseDetailDataFromResult(resultData){
        return resultData.data && resultData.data.length>0 ? resultData.data[0]:{};
    }


    /**
     * delete
     * @param params
     */
    delete(params){

        params=params || {};
        let curUrl=params.url || this.urlDelete;
        let handlerSuccess=params.success || null;
        let handlerFailed=params.failed || null;
        let requestData=params.data || {};


        let dataTranporter=this.getDataTransporter();
        dataTranporter.dataGet({
            "url":curUrl,
            "requestType":"DELETE",
            "data": requestData,
            "success":function(data){

                if(handlerSuccess){
                    handlerSuccess(detailData);
                }

            },
            "failed":function(code,msg,xmlHR){

                if(handlerFailed){
                    handlerFailed(code,msg,xmlHR);
                }

            }
        });

    }//-/

    /**
     * getDetail
     * @param params
     */
    getDetail(params){
        let SELF=this;
        params=params || {};

        let curUrl=params.url || this.urlGetDetail;


        //let requestData=params.data || {};
        let curId=params.id || null;
        let requestData=params.data || {};
        requestData.id=curId;


        let handlerSuccess=params.success || null;
        let handlerFailed=params.failed || null;

        let dataTranporter=this.getDataTransporter();
        dataTranporter.dataGet({
            "url":curUrl,
            "requestType":"GET",
            "data": requestData,
            "success":function(data){

                let detailData=SELF.parseDetailDataFromResult(data);
                if(handlerSuccess){
                    handlerSuccess(detailData);
                }

            },
            "failed":function(code,msg,xmlHR){

                if(handlerFailed){
                    handlerFailed(code,msg,xmlHR);
                }

            }
        });


    }//-/

    /**
     * viewAdd
     * view addnew ui
     * @param params
     */
    viewAdd(params){
        params=params || {};
        params.mode=0;
        if(!params.url){
            params.url=this.urlAdd;
        }
        this.view(params);
    }//-/

    /**
     * viewEdit
     * @param params
     */
    viewEdit(params){
        params=params || {};
        params.mode=1;
        let curId=params.id || null;
        if(!curId){
            console.log("no id provided when try to edit");
            return false;
        }

        if(!params.url){
            params.url=this.urlEdit;
        }
        if(!params.data){
            this.getDetail({
                "id":curId,
                "success":function (detailData) {
                    if(detailData){
                        let editor=new editUser({
                            "data":detailData,
                            "ui":{
                                "form":"#formEdit",
                                "submit":"#btnSubmit"
                            },
                        });

                        this.view(params);
                    }

                }
            })
        }

    }//-/

    /**
     * viewStatic
     * @param params
     */
    viewStatic(params){
        params=params || {};
        params.mode=2;
        if(!params.url){
            params.url=this.urlEdit;
        }
        this.view(params);
    }

    /**
     * view
     * view addNew ui or edit ui.
     *
     * @param params :
     *                .data : when edit, this refer to current item detail data in object format.
     *                .mode : 0[default]- add new; 1- modify; 2- static view.
     *                .url  : if none then take this.url
     *                .requestType: if none then take this.requestType
     *                .ui  : if noe then take this.ui.
     *                       ui.form -> form ui jQuery selector
     *                       ui.submit -> submit button jQuery selector.
     */
    view(params){

        params=params || {};

        let SELF=this;

        //mode:
        this.mode=typeof params.mode=='undefined' ? this.mode : params.mode;

        //url:
        this.url=typeof params.url=='undefined' ? this.url : params.url;


        //ui:
        this.ui=typeof params.ui=='undefined' ? this.ui : params.ui;

        //current data:
        let curData=params.data || null;
        if(curData){
            this.data=curData;
        }

        //requestType:
        this.requestType=params.requestType || this.requestType;

        //current xml url:
        let currentXMLUrl=this.mode ==0 ? this.addFormXMLUrl : this.editFormXMLUrl;

        /*{
        *                              uiForm         : form ui container selecotr like '#form1',
        *                              urlXML         : url for formmat xml.
        *                              data           : form data
        *                              actionFinished : function when finished.
        *                              flagEdit       : define action is edit or add-new.
        *                              flagView       : define static-view  without any edit control.
        *                             )*/

        let formBuilder=new j79FormBuilder({
            "uiForm":this.ui.form,
            "urlXML":currentXMLUrl,
            "data":this.data,
            "flagEdit": this.mode==1,
            "flagView": this.mode==2,
            "actionFinished":function () {


                //console.log("form build finished!");



                    j79.hideLoading(SELF.ui.form_container);
                    if(this.mode==2){

                        $(SELF.ui.form).addClass('form-view');
                        return true;
                    }

                    let submitBtnSelector='';
                    if(!SELF.ui || !SELF.ui.submit){
                        $('<div class="form-group"><div class="col-md-10 col-md-offset-2">'+
                            '<button class="btn btn-primary btn-lg btn-submit" type="button"><i class="glyphicon glyphicon-ok"></i> 提交</button>'+
                            '</div></div>').appendTo($(SELF.ui.form));
                        submitBtnSelector=SELF.ui.form+'  .btn-submit';
                    }else{
                        submitBtnSelector=SELF.ui.submit;
                    }




                    $(submitBtnSelector).click(function(e) {

                        let result=true;

                        $(SELF.ui.form+" [form-input]").each(function(){
                            let singleResult=$(this).validate();
                            if(!singleResult){
                                result=false;
                            }
                        });

                        if(result){
                            SELF.submit();
                        }else{
                            alert("信息填写不完整，请检查~");
                        }

                    });

                    //ini_onBlur_validation();


            }
        });

        formBuilder.ini();


    }//-/

    submit(){
        let SELF=this;
        let postData={};

        /*if(j79.getURLParam('idx') && j79.getURLParam('idx')!=''){//edit
            postData.action='UPDATE';
            postData.idx=j79.getURLParam('idx');

        }else{//add new
            postData.action='CREATE';
        }*/

        //append form data to postData
        let formData={};
        $(SELF.ui.form+" [form-input]").each(function(){
            let singleResult=$(this).validate();
            /*console.log('form item:');
            console.log($(this));*/

            if(singleResult==true){

                if($(this).val()){

                    let valueType=$(this).attr('value-type') || '';

                    if(SELF.flag_return_object==true && valueType.toLowerCase()=='json'){//if json type and flag_return_object==true, then return in object format.
                        console.log('this item is json type and flag_return_object=true :');
                        let curData=j79.toJSON($(this).val());
                        if(curData){
                            formData[$(this).attr('id')]= curData;
                        }
                    }else{//else return in json string.
                        formData[$(this).attr('id')]=$(this).val();
                    }
                }

            }
        });


        //do additional operation to form data:
        postData.data=this.additionalDataOperation(formData);


        console.log('form data:');
        console.log(formData);
        //view loading layer win:
        let mwsettings={
            title: '提交更新',
            bodyHtml:
                '<div class="loading">'+
                '<p>正在提交更新，请耐心等待...</p>'+
                '<div class="sk-wave">'+
                '<div class="sk-rect sk-rect1"></div>'+
                '<div class="sk-rect sk-rect2"></div>'+
                '<div class="sk-rect sk-rect3"></div>'+
                '<div class="sk-rect sk-rect4"></div>'+
                '<div class="sk-rect sk-rect5"></div>'+
                '</div></div>',
            btnHtml:'<b></b>'
        };
        j79.viewModal( mwsettings, 'mw1' );


        let dataT=SELF.getDataTransporter({"url":SELF.url});


        console.log("cur edit reT:"+SELF.requestType)


        dataT.dataTransport({
            "url":SELF.url,
            "requestType":SELF.requestType,
            "data":formData,
            "success":function(data){
                $('#mw1').modal('hide');

                console.log("submit data success!");

                if(SELF.mode==0 && SELF.successSubmitAdd){
                    SELF.successSubmitAdd();
                }else if(SELF.mode==1 && SELF.successSubmitEdit){
                    SELF.successSubmitEdit();
                }else{
                    alert("submit data success")
                }





            },
            "failed":function(code,msg,xmlHR){
                $('#mw1').modal('hide');
                console.log(code);
                console.log(msg);
                console.log(xmlHR);

                if(SELF.mode==0 && SELF.failSubmitAdd){
                    SELF.failSubmitAdd(code,msg,xmlHR);
                }else if(SELF.mode==1 && SELF.failSubmitEdit){
                    SELF.failSubmitEdit(code,msg,xmlHR);
                }else{
                    alert('failed submitting data to remote!');
                }




            }
        });




    }//-/

    /**
     * additionalDataOperation
     * after get data from form, do some additional operation to data and return.
     * need overwite in sub-class.
     * @param data
     * @returns {*}
     */
    additionalDataOperation(data){
        return data;
    }//-/


    /**
     * getDataTransporter
     * should overwrite in subclass to set correct dataTransporter class.
     * @param params
     */
    getDataTransporter(params){
        //need overwrite in subclass
        return new dataTransporter(params);
    }//-/


}//==/