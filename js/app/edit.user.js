class editUser extends editBase{

    parseParam(params){
        super.parseParam(params);




        this.addFormXMLUrl="../../../j79frame/app/settings/form_user_add.xml";
        this.editFormXMLUrl="../../../j79frame/app/settings/form_user_edit.xml";


        //remote url to communicate with:
        this.url="/api/admin/users";

        //url to get detail data, if not provided , then use this.url.
        this.urlGetDetail=params.urlGetDetail || this.url || null;

        //url to add new data, if not provided , then use this.url.
        this.urlAdd=params.urlAdd || this.url || null;

        //url to edit data, if not provided , then use this.url.
        this.urlEdit=params.urlEdit || this.url || null;

        //url to delete data,  if not provided , then use this.url.
        this.urlDelete=params.urlDelete || this.url || null;


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
        return resultData.data && resultData.data.length>0 ? resultData.data[0]:{};
    }

    /**
     * viewAdd
     * @param params
     */
    viewAdd(params){
        params=params || {};
        params.mode=0;
        params.requestType='POST';
        this.view(params);
    }//-/

    /**
     * viewEdit
     * @param params
     */
    viewEdit(params){
        params=params || {};
        params.mode=1;
        params.requestType='PUT';
        this.view(params);
    }//-/

}//==/