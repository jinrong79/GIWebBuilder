class managerBase{

    constructor(params) {

        //parse params and initialize object attribute value.
        this.parseParam(params);

    }//-/

    parseParam(params){

        params=params || {};

        //url of remote server:
        this.url=params.url || null;
        //get detail operation url , if not provided , then use this.url
        this.urlGetDetail=params.urlGetDetail || this.url || null;
        //delete operation url , if not provided , then use this.url
        this.urlDelete=params.urlDelete || this.url || null;

        //need overwrite
        this.lister=new ListBase();

        //need overwrite
        this.editor=new editBase();

        //need overwrite
        this.dataTransporter=new dataTransporterBase();



    }//-/


    getDetail(params){
        if(this.dataTransporter){
            params.url= params.url || this.urlGetDetail;
            this.dataTransporter.dataGet(params);
        }

    }//-/

    viewList(params){
        if(this.lister){
            this.lister.load(params);
        }
    }//-/

    viewAdd(params){
        if(this.editor){
            this.editor.viewAdd(params);
        }
    }//-/


    viewEdit(params){
        let SELF=this;
        params.url= params.url || this.urlGetDetail;
        this.getDetail({
            "success": function(data){
                if(this.editor){
                    params.data=data.data;
                    this.editor.viewEdit(params);
                }
            }
        })



    }//-/

    viewStatic(params){
        let SELF=this;
        this.getDetail({
            "success": function(data){
                if(this.editor){
                    params.data=data.data;
                    this.editor.viewStatic(params);
                }
            }
        })
    }//-/

    delete(params){
        if(this.dataTransporter){
            this.dataTransporter.dataDelete(params);
        }
    }//-/






}