class editRole extends editBase{

    parseParam(params){

        params=params || {};

        super.parseParam(params);




        this.addFormXMLUrl="settings/form_role_add.xml";
        this.editFormXMLUrl="settings/form_role_edit.xml";


        //remote url to communicate with:
        this.url="/api/admin/roles";

        //url to get detail data, if not provided , then use this.url.
        this.urlGetDetail=params.urlGetDetail || this.url || null;

        //url to add new data, if not provided , then use this.url.
        this.urlAdd=params.urlAdd || this.url || null;

        //url to edit data, if not provided , then use this.url.
        this.urlEdit=params.urlEdit || this.url || null;

        //url to delete data,  if not provided , then use this.url.
        this.urlDelete=params.urlDelete || this.url || null;


    }//-/


    setupDeleteLink(params){
        let SELF=this;
        params=params || {};

        //get btn:
        let btnSelector=params.btn || '';
        if(!btnSelector){
            console.log("btn selector not provided");
            return;
        }


        let attrNameForId=params.attrNameForId || 'item-id';
        let handleSuccess=params.success || this.successSubmitDelete;
        let handleFailed=params.failed || this.failSubmitDelete;

        $('body').delegate(btnSelector,'click',null,function(e){
            let id=$(this).attr(attrNameForId);
            let timeStamp=$(this).attr("item-timeStamp");


            j79.mwConfirm("mwDelConfirm","删除确认","确认删除以下项目吗?<br/>编号:"+id+"<br/>",
                function () {
                    SELF.delete({
                        "data":{
                            "id":id,
                            "timestamp":timeStamp

                        },
                        "success":function(e){
                            console.log("delete success");
                            if(handleSuccess){
                                handleSuccess(e);
                            }else{
                                alert("编号"+id+" 删除成功!");
                            }

                        },
                        "failed":function (code,msg) {
                            console.log("delete failed!!");
                            if(handleFailed){
                                handleFailed(code,msg);
                            }else{
                                alert("编号"+id+" 删除失败! 发生错误，错误编码:"+code+" | 错误信息:"+msg);
                            }
                        }
                    })
                },
                function () {

                });




        });
    }//-/


    /**
     * getDataTransporter
     * @returns {dataTransporter}
     */
    getDataTransporter(){
        //need overwrite in sub class:
        return new dataTransporter();
    }//-/


    /**
     * parseDetailDataFromResult
     * parse detail data from result from server.
     * need overwrite in subclass to meet special need.
     * @param resultData
     * @returns {*}
     */
    parseDetailDataFromResult(resultData){

        let detailData= resultData.data && resultData.data.length>0 ? resultData.data[0]:{};

        if(detailData){
            let tt=detailData.timestamp;
            if(tt){
                tt=tt.toString().replace('T',' ');
                tt=tt.replace('.000Z','');
                detailData.timestamp=tt;
            }
        }

        return detailData;

    }

    /**
     * viewAdd
     * @param params
     */
    viewAdd(params){
        params=params || {};
        params.mode=0;
        params.requestType='POST';
        if(!params.success){
            params.success=function(e){
                j79.mwInform("mwOK","成功","添加角色成功！点击回到列表...", function () {
                    document.location.href=j79App.naviURL.role;
                });
            }
        }

        super.viewAdd(params);


    }//-/

    /**
     * viewEdit
     * @param params
     */
    viewEdit(params) {
        params = params || {};
        params.mode = 1;
        params.requestType = 'PUT';
        if (!params.success) {
            params.success = function (e) {
                j79.mwInform("mwOK", "成功", "修改角色成功！点击回到列表...", function () {
                    document.location.href = j79App.naviURL.role;
                });
            }
        }

        super.viewEdit(params);
    }

}//==/