class editUser extends editBase{

    parseParam(params){
        super.parseParam(params);

        this.url="/api/admin/users";


        this.addFormXMLUrl="../../../j79frame/app/settings/form_user_add.xml";
        this.editFormXMLUrl="../../../j79frame/app/settings/form_user_edit.xml";


    }//-/



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