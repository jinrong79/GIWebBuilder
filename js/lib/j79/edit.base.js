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
        this.settingXML=params.settingXML || null;

        //data: detail data when edit.
        this.data=params.data || null;

        //mode: 0-- add new; 1-- modify; 2-- static view
        this.mode=params.mode || 0;

        this.url=params.url || null;

        this.requestType=params.requestType || "POST";



    }//-/

    view(params){

        params=params || {};

        let SELF=this;

        //mode:
        this.mode=typeof params.mode=='undefined' ? this.mode : params.mode;



        //current data:
        let curData=params.data || null;
        if(curData){
            this.data=curData;
        }


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
            "urlXML":this.settingXML,
            "data":this.data,
            "flagEdit": this.mode==1,
            "flagView": this.mode==2,
            "actionFinished":function () {


                console.log("form build finished!");



                    j79.hideLoading(SELF.ui.form_container);
                    if(this.mode==2){

                        $(SELF.ui.form).addClass('form-view');
                        return true;
                    }

                    $('<div class="form-group"><div class="col-md-10 col-md-offset-2">'+
                        '<button class="btn btn-primary btn-lg btn-submit" type="button"><i class="glyphicon glyphicon-ok"></i> 提交</button>'+
                        '</div></div>').appendTo($(SELF.ui.form));


                    $(SELF.ui.form).find('.btn-submit').click(function(e) {

                        var result=true;

                        $(SELF.ui.form+" [form-input]").each(function(){
                            var singleResult=$(this).validate();
                            if(singleResult==false){
                                result=false;
                            }
                        });

                        if(result==true){

                            SELF.submit();
                        }else{
                            alert("信息填写不完整，请检查~");
                        }

                    });

                    ini_onBlur_validation();


            }
        });

        formBuilder.ini(this.mode==2);


    }//-/

    submit(){
        let SELF=this;
        var postData={};

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
            console.log('form item:');
            console.log($(this));

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

        postData.data=formData;

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

        let dataTmp={
            "id":"11111111222",
            "password": "123456",
            "active": 1,
            "disabled": 0,
            "loginid": "test23",
            "email": "test23@test.com",
            "mobile": "123456789233",
            "name": "Wayne King",
            "gender": 1,
            "nickname": "Wayne King",
            "avatar": "https://wx.qlogo.cn/mmopen/vi_32/cnAez0H7ZAuugCkb4AP0Jn0np1AW6yZ4cl13Etgf55nHf7omO7UtBYxNooLn5M0MLyL6rlv1iaM3ibMwDSwtfKnA/132",
            "birthday": "1979-07-01"
        };



        dataT.dataPost({
            "url":SELF.url,
            "requestType":SELF.requestType,
            //"contentType":"application/json",
            "data":formData, //dataTmp,//
            "success":function(data){
                $('#mw1').modal('hide');

                alert("submit data success")
                console.log("submit data success!");


            },
            "failed":function(code,msg,xmlHR){
                $('#mw1').modal('hide');

                console.log(code);
                console.log(msg);
                console.log(xmlHR);

                alert('failed connecting server!');

                /*if (typeof SELF.handleFailed == 'function') {

                    SELF.handleFailed(code,msg,xmlHR);

                } else {
                    alert('failed connecting server!');

                }*/

                /*console.log(code);
                console.log(msg);
                console.log(xmlHR);*/
            }
        });


        /*//post data:
        j79.post({

                data          : postData,

                title         : "更新"+settings.objectName+"详细信息" ,//alert window title.
                actionSuccess : function(result){

                    $('#mw1').modal('hide');

                    let clickRe=window.confirm(settings.objectName+'信息提交成功，点击关闭本窗口。');
                    if(clickRe==true){
                        window.opener = null;
                        window.close()
                    }
                    //alert(settings.objectName+'信息提交成功，点击关闭本窗口');



                },
                actionFailed : function(result){
                    console.log(result);
                    $('#mw1').modal('hide');
                    alert(settings.objectName+'信息提交过程中，服务器报错,出错代码：'+(result && result.error_code? result.error_code : 'N/A') +'| 提示信息：'+(result && result.msg ? result.msg :'N/A'  ));

                },



            }
        );*/

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